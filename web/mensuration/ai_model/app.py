from flask import Flask, request, jsonify, send_from_directory
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import cv2
import os
import numpy as np
from PIL import Image
import colorsys

app = Flask(__name__)
def load_word_list(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            return [line.strip() for line in file if line.strip()]
    except Exception as e:
        print(f"Error loading word list: {e}")
        return []

basedir = os.path.abspath(os.path.dirname(__file__))
profanity_words = load_word_list(os.path.join(basedir, "profanity.txt"))

mental_health_templates = ["You definitely have {}", "It's 100% {}", "You're clearly {}"]
mental_health_conditions = ["depression", "anxiety", "PTSD"]
mental_health_claims = [template.format(condition) 
                      for template in mental_health_templates 
                      for condition in mental_health_conditions]

sexual_health_templates = ["You're definitely infected with {}", "{} is for life"]
sexual_health_conditions = ["HIV", "herpes"]
sexual_health_claims = [template.format(condition) 
                      for template in sexual_health_templates 
                      for condition in sexual_health_conditions]

not_serious_comments = [
    "Don't Worry!,You will be fine",
    "Don't worry,it's just Normal",
    "Don't Nothing is serious",
    "Don't Worry friend you will be fine soon",
    "Don't worry friend, timilai kei pani vako hoina",
    "I'm good",
    "Hello Doctor",
    "Hello Doc",
    "Thik xa hajur",
    "Doctor sahab sanga bhet paye ramro hunthiyo",
    "Ke vako hola",
    "kina yesto bhako hola?",
    "Kasai sanga idea xa",
    "Kina appointment hudaina",
    "Doctor sahab le suggestion dina paye ramro hunthiyo",
    "Doctor kei upaya dinush ta",
    "Sathhi haru ramro sanga bichar dinush",
    "Sathhi kasaile bichar dinush",
    "Sathhi haru kasile yesto samashya vako xa?"
    "hajur vakoxa",
    "Kina hola satthi haru le ramro suggestion nadinubhako",
    "Satthi haru ke vako ho malai?",
    "koi sanga idea xa",
    "koi sanga experience xa",
    "Kei idea xa",
    "Nice comment",
    "nice bichar",
    "Ramro satthi",
    "Ramro bichar satthi",
    "Dammi sarai ramro",
    "mithho idea nice.Yestai gardai janu."
    "Eh ramro hudai xa",
    "Hajur sanchai xa",
    "Serious rog kei pani xaina",
    "Niko vairaxa",
    "Hospital jada ramro hunxa",
    "aasptal janush na",
    "samanya rog ho",
    "Kei panni hunna",
    "Niko hunxa tapailai",
    "Namaste Doctor Sahab",
    "Namaste Daju",
    "Visit the hospital",
    "Use prescribed medicine",
    "Use medications from the doctor",
    "It's better to visit the doctor",
    "I think you should see a doctor",
    "It's probably just stress",
    "This doesn't sound too bad",
    "Try getting some rest",
    "Stay hydrated and rest up",
    "You could talk to your physician"
]
training_data = (
    [(word, "profanity") for word in profanity_words] +
    [(claim, "serious_health_claim") for claim in mental_health_claims + sexual_health_claims] +
    [(comment, "not_serious") for comment in not_serious_comments]
)

if training_data:
    texts, labels = zip(*training_data)
else:
    texts = ["safe comment"]
    labels = ["not_serious"]

try:
    vectorizer = TfidfVectorizer(ngram_range=(1,2))
    X = vectorizer.fit_transform(texts)
    classifier = LogisticRegression(class_weight="balanced", max_iter=1000)
    classifier.fit(X, labels)
    print("Model trained successfully")
except Exception as e:
    print(f"Error training model: {e}")
    vectorizer = TfidfVectorizer(ngram_range=(1,2))
    X = vectorizer.fit_transform(["safe comment"])
    classifier = LogisticRegression(class_weight="balanced", max_iter=1000)
    classifier.fit(X, ["not_serious"])

def classify_comment(text):
    try:
        if not text or text.strip() == "":
            return "not_serious"
        transformed_text = vectorizer.transform([text])
        return classifier.predict(transformed_text)[0]
    except Exception as e:
        print(f"Error classifying comment: {e}")
        return "not_serious"

def simple_nsfw_detector(image_path):
    try:
        if not os.path.exists(image_path):
            print(f"Image not found: {image_path}")
            return 0.0, None
            
        img = Image.open(image_path).convert('RGB')
        img = np.array(img)
        
        h, w, c = img.shape
        skin_pixels = 0
        
        for i in range(h):
            for j in range(w):
                r, g, b = img[i, j] / 255.0
                h_val, s, v = colorsys.rgb_to_hsv(r, g, b)
                
                if 0.0 <= h_val <= 0.15 and 0.2 <= s <= 0.8 and 0.3 <= v <= 1.0:
                    skin_pixels += 1
        
        skin_ratio = skin_pixels / (h * w)
        nsfw_score = min(skin_ratio * 3, 1.0)
        
        blurred_image_path = None
        
        if nsfw_score > 0.5:
            blurred_image = cv2.GaussianBlur(img, (99, 99), 30)
            blurred_image_path = os.path.splitext(image_path)[0] + "_blurred.jpg"
            cv2.imwrite(blurred_image_path, blurred_image)
                
        return nsfw_score, blurred_image_path
    except Exception as e:
        print(f"Error processing image: {e}")
        return 0.0, None

@app.route("/moderate", methods=["POST"])
def moderate_content():
    try:
        data = request.get_json()
        if not data:
            print("No JSON data received")
            return jsonify({"error": "No JSON data received"}), 400
            
        if "comment_text" not in data:
            print("Missing comment_text in request")
            return jsonify({"error": "Missing comment_text in request"}), 400
            
        print(f"Received comment: {data['comment_text']}")
        
        classification = classify_comment(data["comment_text"])
        result = {"label": classification}
        
        image_path = data.get("image_path")
        if image_path:
            print(f"Processing image: {image_path}")
            nsfw_score, blurred_image = simple_nsfw_detector(image_path)
            result["nsfw_score"] = nsfw_score
            if blurred_image:
                result["blurred_image"] = blurred_image
        
        print(f"Returning result: {result}")
        return jsonify(result)
    except Exception as e:
        print(f"Moderation service error: {e}")
        return jsonify({"error": f"Moderation service error: {str(e)}"}), 500

@app.route("/uploads/<path:filename>")
def serve_uploaded_image(filename):
    try:
        uploads_dir = os.path.abspath(os.path.join(basedir, "../../uploads"))
        return send_from_directory(uploads_dir, filename)
    except Exception as e:
        print(f"Error serving image: {e}")
        return jsonify({"error": f"Error serving image: {str(e)}"}), 404

if __name__ == "__main__":
    uploads_dir = os.path.abspath(os.path.join(basedir, "../../uploads"))
    if not os.path.exists(uploads_dir):
        os.makedirs(uploads_dir, exist_ok=True)
        print(f"Created uploads directory: {uploads_dir}")
    
    profanity_path = os.path.join(basedir, "profanity.txt")
    if not os.path.exists(profanity_path):
        with open(profanity_path, "w") as f:
            f.write("damn\nshit\nfuck\nass\n")
        print(f"Created profanity.txt file: {profanity_path}")
    
    print("Starting Flask app...")
    app.run(host="127.0.0.1", port=5000, debug=True)
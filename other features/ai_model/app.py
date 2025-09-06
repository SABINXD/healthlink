from flask import Flask, request, jsonify
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

app = Flask(__name__)
mental_templates = [
    "You definitely have {}", "It’s 100% {}", "No doubt, you’re {}",
    "You're clearly suffering from {}", "You will never recover from {}",
    "{} is permanent in your case", "You’ll always be {}",
    "You're showing all signs of {}", "You need institutionalization for {}",
    "{} cannot be cured","Don't go to hospital","Don't go to clinic"
]

mental_conditions = [
    "depression", "bipolar disorder", "schizophrenia", "psychosis", "anxiety",
    "OCD", "PTSD", "paranoia", "borderline personality disorder",
    "antisocial personality disorder", "dissociative identity disorder",
    "severe trauma", "mental breakdown", "delusional disorder", "mania",
    "suicidal ideation", "neurosis", "hallucinations", "chronic anxiety",
    "panic disorder"
]

sexual_templates = [
    "It’s 100% {}", "No doubt, you have {}", "You're definitely infected with {}",
    "This is clearly {}", "You can’t be cured of {}", "{} is for life",
    "Only sluts get {}", "{} is proof you’re not clean",
    "You’ll die from {}", "You're permanently contagious with {}"
]

sexual_conditions = [
    "HIV", "herpes", "syphilis", "gonorrhea", "chlamydia", "HPV",
    "trichomoniasis", "hepatitis B", "hepatitis C", "pubic lice",
    "scabies", "yeast infection", "genital warts", "bacterial vaginosis",
    "non-gonococcal urethritis", "mycoplasma genitalium",
    "pelvic inflammatory disease", "ureaplasma", "HIV stage 3",
    "STI complications"
]

mental_claims = [
    template.format(condition)
    for i in range(100)
    for template, condition in [(mental_templates[i % len(mental_templates)], mental_conditions[i % len(mental_conditions)])]
]

sexual_claims = [
    template.format(condition)
    for i in range(100)
    for template, condition in [(sexual_templates[i % len(sexual_templates)], sexual_conditions[i % len(sexual_conditions)])]
]


def load_profanity(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        return [line.strip() for line in f if line.strip()]

profanity_words = load_profanity("profanity.txt")

train_data = []

train_data += [(word, "profanity") for word in profanity_words]

all_claims = mental_claims + sexual_claims
train_data += [(text, "serious_health_claim") for text in all_claims]

not_serious_comments = [
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

train_data += [(text, "not_serious") for text in not_serious_comments * 10]
texts, labels = zip(*train_data)
vectorizer = TfidfVectorizer(ngram_range=(1, 2))
X = vectorizer.fit_transform(texts)
model = LogisticRegression(class_weight="balanced", max_iter=1000)
model.fit(X, labels)
def classify_comment(comment):
    x = vectorizer.transform([comment])
    return model.predict(x)[0]
@app.route("/moderate", methods=["POST"])
def moderate():
    data = request.get_json()

    if not data or "comment_text" not in data:
        return jsonify({"error": "Invalid request"}), 400

    comment = data["comment_text"]
    label = classify_comment(comment)

    return jsonify({"label": label})

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)

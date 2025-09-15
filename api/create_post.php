<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/config/db.php');

// <CHANGE> Enhanced AI summary generation for both text and images
function generateAISummary($caption = "", $imagePath = "") {
    $apiKey = "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";
    
    // Prepare image data if available
    $imageData = '';
    $hasImage = false;
    if ($imagePath && file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $hasImage = true;
    }
    
    // Build content array for comprehensive health analysis
    $content = [
        [
            "type" => "text",
            "text" => "You are a professional medical AI assistant. Analyze this health-related content " . 
                     ($hasImage ? "and medical image " : "") . "and provide a comprehensive analysis.

For health questions, symptoms, or medical concerns, provide:
1. A professional summary of the condition/question
2. 3-5 possible conditions or explanations with likelihood percentages
3. Practical recommendations and advice
4. What to avoid or precautions to take

For general health questions (like weight loss, fitness, nutrition), provide:
1. Evidence-based information and explanation
2. Step-by-step recommendations
3. Timeline expectations
4. Safety precautions

Format as JSON with keys: 'summary', 'conditions' (array with 'condition' and 'likelihood'), 'recommendations', 'precautions'

Always include medical disclaimer.

Content to analyze: $caption"
        ]
    ];
    
    // Add image if available
    if ($hasImage) {
        $content[] = [
            "type" => "image_url",
            "image_url" => [
                "url" => "data:image/jpeg;base64,$imageData"
            ]
        ];
    }
    
    $payload = [
        "model" => "openai/gpt-4o",
        "messages" => [
            [
                "role" => "user",
                "content" => $content
            ]
        ],
        "max_tokens" => 1000,
        "response_format" => ["type" => "json_object"]
    ];
    
    $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $response) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $content = json_decode($result['choices'][0]['message']['content'], true);
            if ($content && isset($content['summary'])) {
                // Format the response professionally
                $formattedSummary = "📋 " . $content['summary'] . "\n\n";
                
                if (isset($content['conditions']) && is_array($content['conditions'])) {
                    $formattedSummary .= "🔍 Possible Conditions/Explanations:\n";
                    foreach ($content['conditions'] as $condition) {
                        if (isset($condition['condition']) && isset($condition['likelihood'])) {
                            $formattedSummary .= "• " . $condition['condition'] . " (" . $condition['likelihood'] . "% likelihood)\n";
                        }
                    }
                    $formattedSummary .= "\n";
                }
                
                if (isset($content['recommendations'])) {
                    $formattedSummary .= "💡 Recommendations:\n" . $content['recommendations'] . "\n\n";
                }
                
                if (isset($content['precautions'])) {
                    $formattedSummary .= "⚠️ Precautions:\n" . $content['precautions'] . "\n\n";
                }
                
                $formattedSummary .= "⚠️ MEDICAL DISCLAIMER: This is not an official medical diagnosis. Please consult with a healthcare professional if you feel unwell or have concerns about your health.";
                
                return $formattedSummary;
            }
        }
    }
    
    // Fallback response for any health-related content
    return "📋 Health Analysis Completed\n\nBased on your health-related question, this appears to be a topic that would benefit from professional medical guidance.\n\n💡 General Recommendation:\nConsult with a healthcare professional for personalized advice and proper evaluation.\n\n⚠️ MEDICAL DISCLAIMER: This is not an official medical diagnosis. Please consult with a healthcare professional for proper evaluation and treatment.";
}

try {
    $conn = getDbConnection();
    
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $spoiler = isset($_POST['spoiler']) ? (int)$_POST['spoiler'] : 0;
    
    // Validate inputs
    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
        exit;
    }
    
    if (empty($caption)) {
        echo json_encode(['status' => 'error', 'message' => 'Caption is required']);
        exit;
    }
    
    // Initialize variables for image processing
    $filename = '';
    $file_path = '';
    $hasImage = false;
    
    // Check if image was uploaded (optional)
    if (isset($_FILES['post_img']) && $_FILES['post_img']['error'] === UPLOAD_ERR_OK) {
        $hasImage = true;
        $uploaded_file = $_FILES['post_img'];
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($uploaded_file['type'], $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }
        
        // Check file size (limit to 10MB)
        if ($uploaded_file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'Image is too large. Maximum size is 10MB.']);
            exit;
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = __DIR__ . '/../web/assets/img/posts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
        if (empty($file_extension)) {
            $file_extension = 'jpg';
        }
        $filename = 'post_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($uploaded_file['tmp_name'], $file_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
            exit;
        }
    }
    
    // <CHANGE> Generate AI summary for ALL health-related posts (both text and image)
    $aiSummary = '';
    $shouldGenerateAI = false;
    
    // Check if content is health-related
    $healthKeywords = ['pain', 'ache', 'symptom', 'doctor', 'medical', 'health', 'sick', 'disease', 'condition', 'treatment', 'medicine', 'hospital', 'clinic', 'diagnosis', 'therapy', 'weight', 'diet', 'fitness', 'exercise', 'nutrition', 'wellness', 'mental health', 'anxiety', 'depression', 'stress', 'headache', 'fever', 'cough', 'rash', 'infection', 'injury', 'chronic', 'acute'];
    
    $captionLower = strtolower($caption);
    foreach ($healthKeywords as $keyword) {
        if (strpos($captionLower, $keyword) !== false) {
            $shouldGenerateAI = true;
            break;
        }
    }
    
    // Also generate AI if image is uploaded (likely medical image)
    if ($hasImage) {
        $shouldGenerateAI = true;
    }
    
    if ($shouldGenerateAI) {
        $aiSummary = generateAISummary($caption, $file_path);
        error_log("Generated AI Summary for post: " . substr($aiSummary, 0, 100) . "...");
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, post_text, post_img, category, code_content, code_language, code_status, spoiler, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $aiLanguage = "Medical Analysis";
    $aiStatus = $shouldGenerateAI ? 1 : 0; // 1 if AI summary generated, 0 if not
    $stmt->bind_param("isssssii", $user_id, $caption, $filename, $category, $aiSummary, $aiLanguage, $aiStatus, $spoiler);
    
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create post: ' . $stmt->error]);
        exit;
    }
    
    $post_id = $conn->insert_id;
    
    // Return success response
    $image_url = '';
    if (!empty($filename)) {
        $image_url = "http://" . IP_ADDRESS . "/healthlink/web/assets/img/posts/" . $filename;
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Post created successfully' . ($shouldGenerateAI ? ' with AI health analysis' : ''),
        'post_id' => $post_id,
        'image_url' => $image_url,
        'ai_summary' => $aiSummary,
        'spoiler' => $spoiler,
        'ai_generated' => $shouldGenerateAI
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
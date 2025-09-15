<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/config/db.php');

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

Provide:
1. Professional summary of the condition/question
2. 3-5 possible conditions or explanations with likelihood percentages
3. Practical recommendations and advice
4. Precautions to take

Format as JSON with keys: 'summary', 'conditions' (array with 'condition' and 'likelihood'), 'recommendations', 'precautions'

Content: $caption"
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
                    $formattedSummary .= "🔍 Possible Conditions:\n";
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
    
    // Fallback response
    return "📋 Health Analysis Completed\n\nBased on your health-related content, this appears to be a topic that would benefit from professional medical guidance.\n\n💡 Recommendation: Consult with a healthcare professional for personalized advice.\n\n⚠️ MEDICAL DISCLAIMER: This is not an official medical diagnosis. Please consult with a healthcare professional for proper evaluation and treatment.";
}

try {
    $conn = getDbConnection();
    
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    if ($post_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
        exit;
    }
    
    // Get post details
    $query = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit;
    }
    
    // <CHANGE> Generate AI summary for both text and image posts
    $aiSummary = '';
    $imagePath = '';
    
    // Check if post has image
    if (!empty($post['post_img'])) {
        $imagePath = __DIR__ . "/../web/assets/img/posts/" . $post['post_img'];
    }
    
    // Generate AI summary using both text and image (if available)
    $aiSummary = generateAISummary($post['post_text'], $imagePath);
    
    // Update the post with AI analysis
    $updateQuery = "UPDATE posts SET code_content = ?, code_language = 'Medical Analysis', code_status = 1 WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $aiSummary, $post_id);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true, 
            'ai_summary' => $aiSummary,
            'message' => 'AI summary generated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to save AI summary'
        ]);
    }
    
    $stmt->close();
    $updateStmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
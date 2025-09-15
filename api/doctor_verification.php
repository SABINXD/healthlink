<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include(__DIR__ . "/config/db.php");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = handleDoctorVerification($_POST, $_FILES);
    echo json_encode($response);
} else {
    echo json_encode(['status' => false, 'msg' => 'Only POST method allowed']);
}

function handleDoctorVerification($form_data, $file_data) {
    global $db;
    
    // Validate form data
    $validation = validateVerifyDoctorForm($form_data, $file_data);
    if (!$validation['status']) {
        return $validation;
    }
    
    // Insert doctor verification data
    $result = insertVerifyDoctor($form_data, $file_data);
    if ($result) {
        return [
            'status' => true,
            'msg' => 'Doctor verification submitted successfully. We will review your application.',
            'data' => ['verification_id' => mysqli_insert_id($db)]
        ];
    } else {
        return [
            'status' => false,
            'msg' => 'Failed to submit verification. Please try again.',
            'error' => mysqli_error($db)
        ];
    }
}

function validateVerifyDoctorForm($form_data, $file_data) {
    global $db;
    
    $response = [
        'status' => true,
        'msg' => '',
        'field' => ''
    ];
    
    // Check if user ID is provided
    $user_id = $form_data['user_id'] ?? 0;
    if (!$user_id) {
        $response['status'] = false;
        $response['msg'] = "User ID is required";
        $response['field'] = 'user_id';
        return $response;
    }
    
    // Check if user already submitted verification
    $checkQuery = "SELECT * FROM doctor_verification WHERE user_id = " . intval($user_id);
    $result = mysqli_query($db, $checkQuery);
    if ($result && mysqli_num_rows($result) > 0) {
        $response['status'] = false;
        $response['msg'] = "You have already submitted a doctor verification request.";
        $response['field'] = 'alreadySubmitted';
        return $response;
    }
    
    // Required text fields
    $requiredFields = ['specialty', 'experience', 'license', 'phone', 'address', 'city', 'country'];
    foreach ($requiredFields as $field) {
        if (empty(trim($form_data[$field] ?? ''))) {
            $response['status'] = false;
            $response['msg'] = ucfirst($field) . " is required";
            $response['field'] = $field;
            return $response;
        }
    }
    
    // Validate experience is numeric
    if (!is_numeric($form_data['experience']) || $form_data['experience'] < 0) {
        $response['status'] = false;
        $response['msg'] = "Experience must be a valid number";
        $response['field'] = 'experience';
        return $response;
    }
    
    // File validation
    $files = ['citizenshipFront', 'citizenshipBack', 'medicalCertificate'];
    foreach ($files as $file) {
        if (!isset($file_data[$file]) || !$file_data[$file]['name']) {
            $response['status'] = false;
            $response['msg'] = "$file is required";
            $response['field'] = $file;
            return $response;
        }
        
        $image = $file_data[$file]['name'];
        $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $sizeKB = $file_data[$file]['size'] / 1024;
        
        if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
            $response['status'] = false;
            $response['msg'] = "Only jpg, jpeg, png allowed for $file";
            $response['field'] = $file;
            return $response;
        }
        
        if ($sizeKB > 2048) {
            $response['status'] = false;
            $response['msg'] = "File size must be less than 2MB for $file";
            $response['field'] = $file;
            return $response;
        }
    }
    
    return $response;
}

function insertVerifyDoctor($data, $imagedata) {
    global $db;
    
    // Escape text data
    $user_id = intval($data['user_id']);
    $specialty = mysqli_real_escape_string($db, $data['specialty']);
    $address = mysqli_real_escape_string($db, $data['address']);
    $city = mysqli_real_escape_string($db, $data['city']);
    $country = mysqli_real_escape_string($db, $data['country']);
    $experience = intval($data['experience']);
    $license = mysqli_real_escape_string($db, $data['license']);
    $phone = mysqli_real_escape_string($db, $data['phone']);
    
    // Upload directory
    $uploadDir = "../uploads/doctor_verification/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Handle files
    $files = ['citizenshipFront', 'citizenshipBack', 'medicalCertificate'];
    $uploadedFiles = [];
    
    foreach ($files as $file) {
        $uploadedFiles[$file] = '';
        if (isset($imagedata[$file]) && $imagedata[$file]['name']) {
            $extension = strtolower(pathinfo($imagedata[$file]['name'], PATHINFO_EXTENSION));
            $image_name = $user_id . '_' . $file . '_' . time() . '.' . $extension;
            $image_path = $uploadDir . $image_name;
            
            if (move_uploaded_file($imagedata[$file]['tmp_name'], $image_path)) {
                $uploadedFiles[$file] = $image_name;
            } else {
                return false;
            }
        }
    }
    
    // Insert into database - matching your exact table structure
    $query = "INSERT INTO doctor_verification 
        (user_id, specialty, experience, license, phone, address, city, country, 
         citizenshipFront, citizenshipBack, medicalCertificate, applied_at)
        VALUES 
        ($user_id, '$specialty', $experience, '$license', '$phone', '$address', '$city', '$country',
         '{$uploadedFiles['citizenshipFront']}', '{$uploadedFiles['citizenshipBack']}', '{$uploadedFiles['medicalCertificate']}', 
         NOW())";
    
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        // Log the error for debugging
        error_log("SQL Error: " . mysqli_error($db));
        error_log("Query: " . $query);
    }
    
    return $result;
}
?>
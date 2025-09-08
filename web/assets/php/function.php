<?php
require_once 'config.php';
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("database is not connected");

// Function to show pages
function showPage($page, $data = "")
{
    $safePage = basename($page); // Prevent directory traversal
    include("./assets/pages/$safePage.php");
}

// Function to show error
function showError($field)
{
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        if (isset($error['field']) && $field == $error['field']) { ?>
            <div class="alert alert-danger my-2" role="alert">
                <?= $error['msg'] ?>
            </div><?php
                }
            }
        }

        // Function to show previous form data
        function showFormData($field)
        {
            if (isset($_SESSION['formdata'])) {
                $formdata = $_SESSION['formdata'];
                return $formdata[$field] ?? null;
            }
        }


        // for checking dublicate email
        function isEmailRegistered($email)
        {
            global $db;
            $query = "SELECT count(*) as row FROM users WHERE email='$email'";
            $run = mysqli_query($db, $query);
            $return_data = mysqli_fetch_assoc($run);
            return $return_data['row'];
        }

        //for checking dublicate username
        function isUsernameRegistered($username)
        {
            global $db;
            $query = "SELECT count(*) as row FROM users WHERE username='$username'";
            $run = mysqli_query($db, $query);
            $return_data = mysqli_fetch_assoc($run);
            return $return_data['row'];
        }

        //for checking username registered by other
        function isUsernameRegisteredByOther($username)
        {
            global $db;
            $user_id = $_SESSION['userdata']['id'];
            $query = "SELECT count(*) as row FROM users WHERE username='$username' && id!=$user_id";
            $run = mysqli_query($db, $query);
            $return_data = mysqli_fetch_assoc($run);
            return $return_data['row'];
        }

        // Validating signup form
        function validateSignupForm($form_data)
        {
            $response = array('status' => true, 'msg' => '');

            // ✅ Password validation
            if (!isset($form_data['password']) || !$form_data['password']) {
                $response['msg'] = "Password is not provided";
                $response['status'] = false;
                $response['field'] = 'password';
                return $response;
            } else if (strlen($form_data['password']) < 8) {
                $response['msg'] = "Password must be at least 8 characters long";
                $response['status'] = false;
                $response['field'] = 'password';
                return $response;
            } else if (
                !preg_match('/[a-zA-Z]/', $form_data['password']) ||     // at least one letter
                !preg_match('/[0-9]/', $form_data['password']) ||        // at least one digit
                !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $form_data['password']) // at least one special char
            ) {
                $response['msg'] = "Password must include at least one letter, one number, and one special character";
                $response['status'] = false;
                $response['field'] = 'password';
                return $response;
            }
            // ✅ Username validation
            if (!isset($form_data['username']) || !$form_data['username']) {
                $response['msg'] = "Username is not provided";
                $response['status'] = false;
                $response['field'] = 'username';
                return $response;
            } else if (!preg_match('/^[a-zA-Z0-9]+$/', $form_data['username'])) {
                $response['msg'] = "Username must only contain letters and numbers (no symbols)";
                $response['status'] = false;
                $response['field'] = 'username';
                return $response;
            } else if (!preg_match('/[a-zA-Z]/', $form_data['username'])) {
                $response['msg'] = "Username must contain at least one letter (a-z or A-Z)";
                $response['status'] = false;
                $response['field'] = 'username';
                return $response;
            }
            // ✅ Email validation
            if (!isset($form_data['email']) || !$form_data['email']) {
                $response['msg'] = "Email is not provided";
                $response['status'] = false;
                $response['field'] = 'email';
                return $response;
            } else if (!preg_match('/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]+$/', $form_data['email'])) {
                $response['msg'] = "Email must be in correct format and only contain letters, numbers, @, and .";
                $response['status'] = false;
                $response['field'] = 'email';
                return $response;
            } else {
                // Split local part (before @)
                $email_parts = explode('@', $form_data['email']);
                if (!preg_match('/[a-zA-Z]/', $email_parts[0])) {
                    $response['msg'] = "Email must contain at least one letter before @";
                    $response['status'] = false;
                    $response['field'] = 'email';
                    return $response;
                }
            }


            // ✅ First name
            if (!isset($form_data['first_name']) || !$form_data['first_name']) {
                $response['msg'] = "First name is not provided";
                $response['status'] = false;
                $response['field'] = 'first_name';
                return $response;
            }

            // ✅ Last name
            if (!isset($form_data['last_name']) || !$form_data['last_name']) {
                $response['msg'] = "Last name is not provided";
                $response['status'] = false;
                $response['field'] = 'last_name';
                return $response;
            }

            // ✅ Check if email or username is already taken
            if (isEmailRegistered($form_data['email'])) {
                $response['msg'] = "Email is already registered";
                $response['status'] = false;
                $response['field'] = 'email';
                return $response;
            }

            if (isUsernameRegistered($form_data['username'])) {
                $response['msg'] = "Username is already registered";
                $response['status'] = false;
                $response['field'] = 'username';
                return $response;
            }

            return $response;
        }

        // validationg login in php
        function validateLoginForm($form_data)
        {
            $response = array();
            $response['status'] = true;
            $blank = false;
            if (!$form_data['password']) {
                $response['msg'] = "Password is not provided";
                $response['status'] = false;
                $response['field'] = 'password';
                $blank = true;
            }
            if (!$form_data['username_email']) {
                $response['msg'] = "Username/email is not provided";
                $response['status'] = false;
                $response['field'] = 'username_email';
                $blank = true;
            }
            if (!$blank && !checkUser($form_data)['status']) {
                $response['msg'] = "Something is incorrect we cannot find you";
                $response['status'] = false;
                $response['field'] = 'checkuser';
            } else {
                $response['user'] = checkUser($form_data)['user'];
            }
            return $response;
        }

        // fro checking code
        function validateVerify($form_data)
        {
            $response = array();
            $response['status'] = true;
            $blank = false;
            if (!$form_data['verify_code']) {
                $response['msg'] = "please Enter code";
                $response['status'] = false;
                $response['field'] = 'verify_code';
                $blank = true;
            }
            return $response;
        }

        // for checking user
        function checkUser($login_data)
        {
            global $db;
            $username_email = $login_data['username_email'];
            $password = md5($login_data['password']);
            $query = "SELECT * FROM users WHERE (email='$username_email' || username ='$username_email') && password = '$password'";
            $run = mysqli_query($db, $query);
            $data['user'] = mysqli_fetch_assoc($run) ?? array();
            if (count($data['user']) > 0) {
                $data['status'] = true;
            } else {
                $data['satus'] = false;
            }
            return $data;
        }
        //checking tthe dcotor is available  or not
        // Check if doctor exists and is active
        function isDoctorAvailable($doctorId)
        {
            global $db;

            $doctorId = (int)$doctorId;

            $query = "SELECT * FROM users
              WHERE id = $doctorId 
              AND is_doctor = 1 
              AND ac_status = 1 
              LIMIT 1";

            $result = mysqli_query($db, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result); // return doctor row
            }

            return false; // not found or not active
        }
        // get docr username 
        function getDoctorUsername($id)
        {
            global $db;

            // Ensure $id is an integer
            $id = (int)$id;

            // Run query
            $query = "SELECT username FROM users WHERE id = $id LIMIT 1";
            $result = mysqli_query($db, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['username'];
            }

            // fallback if not found
            return null;
        }





        //for geeting userr data by id
        function getUser($user_id)
        {
            global $db;
            $query = "SELECT * FROM users WHERE id=$user_id ";
            $run = mysqli_query($db, $query);
            return mysqli_fetch_assoc($run);
        }
        // function get docotr 
        function getDoctors($limit_number)
        {
            global $db;
            $query = "SELECT * FROM users WHERE is_doctor=1 LIMIT $limit_number";
            $run = mysqli_query($db, $query);
            $doctors = [];

            while ($doctor = mysqli_fetch_assoc($run)) {
                $doctors[] = $doctor;
            }

            return $doctors;
        }

        //for geting user by username
        function getUserByUsername($username)
        {
            global $db;
            $query = "SELECT * FROM users WHERE username= '$username'";
            $run = mysqli_query($db, $query);
            return mysqli_fetch_assoc($run);
        }
        //function for chnage location 
        function changeLocation($district)
        {
            global $db;
            $user_id = $_SESSION['userdata']['id'];
            $query = "UPDATE users SET current_location='$district' WHERE id=$user_id";
            return mysqli_query($db, $query);
        }
        // Function to get hospital numbers by district
        function getHospitalNumber($district)
        {
            global $db;
            $query = "SELECT * FROM hospital WHERE h_location='$district'";
            $run = mysqli_query($db, $query);

            if (!$run) {
                return [];
            }

            $hospitals = [];
            while ($row = mysqli_fetch_assoc($run)) {
                $hospitals[] = $row;
            }

            return $hospitals;
        }



        // function to get ambulance number 
        function getAmbulanceNumber($district)
        {
            global $db;

            // Log the district being queried
            error_log("Querying ambulance for district: " . $district);

            $query = "SELECT * FROM ambulance WHERE a_location='$district'";
            $run = mysqli_query($db, $query);

            if (!$run) {
                // Log the error
                error_log("Query failed: " . mysqli_error($db));
                return false;
            }

            $result = mysqli_fetch_all($run, MYSQLI_ASSOC);

            // Log the result count
            error_log("Found " . count($result) . " ambulances");

            return $result;
        }
        // for creating a new user
        function createUser($data)
        {
            global $db;
            $first_name = mysqli_real_escape_string($db, $data['first_name']);
            $last_name = mysqli_real_escape_string($db, $data['last_name']);
            $gender = $data['gender'];
            $email = mysqli_real_escape_string($db, $data['email']);
            $username = mysqli_real_escape_string($db, $data['username']);
            $password = mysqli_real_escape_string($db, $data['password']);
            $password = md5($password);
            $query = "INSERT INTO users(first_name,last_name,gender,email,username,password)";
            $query .= "VALUES ('$first_name','$last_name',$gender,'$email','$username','$password')";
            return mysqli_query($db, $query);
        }

        //function to verify user email
        function verifyEmail($email)
        {
            global $db;
            $query = "UPDATE users SET ac_status=1 WHERE email='$email'";
            return mysqli_query($db, $query);
        }

        //loging out the user
        if (isset($_GET['logout'])) {
            session_destroy();
            header("location:../../");
        }

        // function to change password
        function resetPassword($email, $password)
        {
            global $db;
            $password = md5($password);
            $query = "UPDATE users SET password='$password' WHERE email='$email'";
            return mysqli_query($db, $query);
        }
        //function fir validating doctor verify form
        function validateVerifyDoctorForm($form_data, $file_data)
        {
            global $db; // Make sure DB connection is available

            $response = [
                'status' => true,
                'msg' => '',
                'field' => ''
            ];

            // =========================
            // Check if user already submitted verification
            // =========================
            $user_id = $_SESSION['userdata']['id'] ?? 0;
            $checkQuery = "SELECT * FROM doctor_verification WHERE user_id = $user_id";
            $result = mysqli_query($db, $checkQuery);
            if ($result && mysqli_num_rows($result) > 0) {
                $response['status'] = false;
                $response['msg'] = "You have already submitted a doctor verification request.";
                $response['field'] = 'alreadySubmitted';
                return $response;
            }

            // =========================
            // Required text fields
            // =========================
            $requiredFields = ['specialty', 'experience', 'license', 'phone', 'address', 'city', 'country'];
            foreach ($requiredFields as $field) {
                if (empty(trim($form_data[$field] ?? ''))) {
                    $response['status'] = false;
                    $response['msg'] = ucfirst($field) . " is required";
                    $response['field'] = $field;
                    return $response;
                }
            }

            // =========================
            // File validation
            // =========================
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
                $sizeKB = $file_data[$file]['size'] / 1024; // KB

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

        function insertVerifyDoctor($data, $imagedata)
        {
            global $db;
            // Escape text data
            $specialty = mysqli_real_escape_string($db, $data['specialty']);
            $address = mysqli_real_escape_string($db, $data['address']);
            $city = mysqli_real_escape_string($db, $data['city']);
            $country = mysqli_real_escape_string($db, $data['country']);
            $experience = mysqli_real_escape_string($db, $data['experience']);
            $license = mysqli_real_escape_string($db, $data['license']);
            $phone = mysqli_real_escape_string($db, $data['phone']);

            // Upload directory - make sure this matches where files are stored
            $uploadDir = "../img/verifydoctor/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Handle files
            $files = ['citizenshipFront', 'citizenshipBack', 'medicalCertificate'];
            $uploadedFiles = [];

            foreach ($files as $file) {
                $uploadedFiles[$file] = '';
                if (isset($imagedata[$file]) && $imagedata[$file]['name']) {
                    $image_name = time() . '_' . basename($imagedata[$file]['name']);
                    $image_path = $uploadDir . $image_name;
                    if (move_uploaded_file($imagedata[$file]['tmp_name'], $image_path)) {
                        $uploadedFiles[$file] = $image_name;
                    } else {
                        return false; // File upload failed
                    }
                }
            }

            $user_id = $_SESSION['userdata']['id'] ?? 0;
            $query = "INSERT INTO doctor_verification 
    (user_id,specialty,experience,license,phone,address,city,country,citizenshipFront,citizenshipBack,medicalCertificate)
    VALUES 
    ($user_id,'$specialty','$experience','$license','$phone','$address','$city','$country','{$uploadedFiles['citizenshipFront']}','{$uploadedFiles['citizenshipBack']}','{$uploadedFiles['medicalCertificate']}')";

            return mysqli_query($db, $query);
        }

        //function fir validating update form
        function validateUpdateForm($form_data, $image_data)
        {
            $response = array();
            $response['status'] = true;
            if (!$form_data['username']) {
                $response['msg'] = "username is not given";
                $response['status'] = false;
                $response['field'] = 'username';
            }
            if (!$form_data['last_name']) {
                $response['msg'] = "last name is not given";
                $response['status'] = false;
                $response['field'] = 'last_name';
            }
            if (!$form_data['first_name']) {
                $response['msg'] = "first name is not given";
                $response['status'] = false;
                $response['field'] = 'first_name';
            }
            if (isUsernameRegisteredByOther($form_data['username'])) {
                $response['msg'] = $form_data['username'] . " is already registered";
                $response['status'] = false;
                $response['field'] = 'username';
            }
            if ($image_data['name']) {
                $image = basename($image_data['name']);
                $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $size = $image_data['size'] / 1024;
                if ($type != 'jpg' && $type != 'jpeg' && $type != 'png') {
                    $response['msg'] = "only jpg,jpeg,png images are allowed";
                    $response['status'] = false;
                    $response['field'] = 'profile_pic';
                }
                if ($size > 2048) {
                    $response['msg'] = "upload image less then 2  mb";
                    $response['status'] = false;
                    $response['field'] = 'profile_pic';
                }
            }
            return $response;
        }

        // function for updating profile
        function updateProfile($data, $imagedata)
        {
            global $db;
            $first_name = mysqli_real_escape_string($db, $data['first_name']);
            $last_name = mysqli_real_escape_string($db, $data['last_name']);
            $username = mysqli_real_escape_string($db, $data['username']);
            $password = mysqli_real_escape_string($db, $data['password']);
            if (!$data['password']) {
                $password = $_SESSION['userdata']['password'];
            } else {
                $password = md5($password);
                $_SESSION['userdata']['password'] = $password;
            }
            $profile_pic = "";
            if ($imagedata['name']) {
                $image_name = time() . basename($imagedata['name']);
                $image_dir = "../img/profile/$image_name";
                move_uploaded_file($imagedata['tmp_name'], $image_dir);
                $profile_pic = ", profile_pic='$image_name'";
            }
            $query = "UPDATE users SET first_name = '$first_name', last_name='$last_name', username='$username', password='$password' $profile_pic WHERE id=" . $_SESSION['userdata']['id'];
            $result = mysqli_query($db, $query);
            if (!$result) {
                die("Database error: " . mysqli_error($db));
            }
            return $result;
        }
        // Add to function.php
        // Add to function.php

        function liveSearch($keyword)
        {
            global $db;
            $keyword = mysqli_real_escape_string($db, $keyword);
            $results = array('posts' => array());

            // Only search if keyword has at least 2 characters
            if (strlen($keyword) < 2) {
                return $results;
            }

            // Post search
            $postQuery = "SELECT id, post_title, post_desc, post_category, created_at 
                  FROM posts 
                  WHERE post_title LIKE '%$keyword%' 
                  OR post_desc LIKE '%$keyword%' 
                  OR post_category LIKE '%$keyword%'
                  ORDER BY created_at DESC 
                  LIMIT 10";

            $postResult = mysqli_query($db, $postQuery);
            if ($postResult) {
                while ($row = mysqli_fetch_assoc($postResult)) {
                    $results['posts'][] = $row;
                }
            }

            return $results;
        } //function to create post
        function createPost($text, $image)
        {
            global $db;
            $post_text = mysqli_real_escape_string($db, $text['post_text']);
            $user_id = $_SESSION['userdata']['id'];
            $image_name = time() . basename($image['name']);
            $image_dir = "../img/posts/$image_name";
            move_uploaded_file($image['tmp_name'], $image_dir);
            $query = "INSERT INTO posts(user_id,post_text,post_img)";
            $query .= "VALUES ($user_id,'$post_text','$image_name')";
            return mysqli_query($db, $query);
        }

        function generateAISummary($caption = "", $imagePath = "")
        {
            $apiKey = "api_key";

            // Prepare image data if available
            $imageData = '';
            if ($imagePath && file_exists("../img/posts/" . $imagePath)) {
                $imageData = base64_encode(file_get_contents("../img/posts/" . $imagePath));
            }

            // Build content array
            $content = [
                [
                    "type" => "text",
                    "text" => "Analyze this health-related post and provide:
1. A brief summary of the post.
2. A list of at least 3 possible conditions that might relate to the symptoms described, each with a likelihood percentage (just the number, without the % sign).

Format your response as JSON with two keys: 'summary' and 'conditions'. The 'conditions' should be an array of objects, each with 'condition' and 'likelihood'.

Caption: $caption"
                ]
            ];

            // Add image if available
            if ($imageData) {
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
                "max_tokens" => 500,
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
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                $content = json_decode($result['choices'][0]['message']['content'], true);
                if ($content && isset($content['summary']) && isset($content['conditions'])) {
                    return $content;
                }
            }

            // Fallback response
            return [
                'summary' => "⚠️ No summary generated.",
                'conditions' => [
                    ['condition' => 'General Health', 'likelihood' => 75],
                    ['condition' => 'Preventive Care', 'likelihood' => 60],
                    ['condition' => 'Lifestyle Factors', 'likelihood' => 45]
                ]
            ];
        }
        // creating no code
        function createNoCodePost($data, $image)
        {
            global $db;

            // Extract and sanitize data
            $post_title = mysqli_real_escape_string($db, $data['post_title']);
            $post_category = mysqli_real_escape_string($db, $data['post_category']);
            $post_desc = mysqli_real_escape_string($db, $data['post_desc']);
            $user_id = $_SESSION['userdata']['id'];
            $post_privacy = isset($data['post_privacy']) ? (int)$data['post_privacy'] : 0;
            $spoiler = isset($data['spoiler']) ? (int)$data['spoiler'] : 0;

            // Handle image upload (optional)
            $image_name = '';
            if ($image && $image['name']) {
                $image_name = time() . basename($image['name']);
                $image_dir = "../img/posts/$image_name";
                move_uploaded_file($image['tmp_name'], $image_dir);
            }

            // Generate AI summary
            $aiResult = generateAISummary($post_title . " " . $post_desc, $image_name);
            $aiContent = json_encode($aiResult);
            $aiContent = mysqli_real_escape_string($db, $aiContent);

            // Extract conditions
            $conditions = '';
            if (isset($aiResult['conditions']) && is_array($aiResult['conditions'])) {
                $conditions = json_encode($aiResult['conditions']);
                $conditions = mysqli_real_escape_string($db, $conditions);
            }

            // Insert into database
            $query = "INSERT INTO posts(user_id, post_title, post_category, post_desc, post_img, code_status, code_content, possible_conditions, post_privacy, spoiler) 
              VALUES ($user_id, '$post_title', '$post_category', '$post_desc', '$image_name', 0, '$aiContent', '$conditions', $post_privacy, $spoiler)";
            return mysqli_query($db, $query);
        }
        //function to validate post
        function validateNewPost($data, $image_data)
        {
            $response = array('status' => true, 'msg' => '', 'field' => '');

            // Check title
            if (empty($data['post_title'])) {
                $response['status'] = false;
                $response['msg'] = "Title is required";
                $response['field'] = 'post_title';
                return $response;
            }

            // Check category
            if (empty($data['post_category'])) {
                $response['status'] = false;
                $response['msg'] = "Category is required";
                $response['field'] = 'post_category';
                return $response;
            }

            // If image is provided, validate it
            if ($image_data && $image_data['name']) {
                $image = basename($image_data['name']);
                $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $size = $image_data['size'] / 1024;

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    $response['status'] = false;
                    $response['msg'] = "Only jpg, jpeg, png images are allowed";
                    $response['field'] = 'post_img';
                    return $response;
                }

                if ($size > 2048) {
                    $response['status'] = false;
                    $response['msg'] = "Upload image less than 2MB";
                    $response['field'] = 'post_img';
                    return $response;
                }
            }

            return $response;
        }

        function getPost($category = null)
        {
            global $db;
            $query = "
    SELECT 
        u.id AS uid,
        p.id,
        p.user_id,
        p.post_img,
        p.post_title,
        p.post_desc,
        p.post_category,
        p.code_content,
        p.possible_conditions,
        p.code_language,
        p.tags,
        p.code_status,
        p.post_privacy,
        p.spoiler,
        p.created_at,
        u.first_name,
        u.last_name,
        u.username,
        u.profile_pic
    FROM posts p
    JOIN users u ON u.id = p.user_id
    ";

            // Add category filter if provided
            if ($category && $category !== 'All Topics') {
                $category = mysqli_real_escape_string($db, $category);
                $query .= " WHERE p.post_category = '$category'";
            }

            $query .= " ORDER BY p.created_at DESC";
            $run = mysqli_query($db, $query);

            if (!$run) {
                die('Query Failed: ' . mysqli_error($db));
            }

            $posts = [];
            while ($row = mysqli_fetch_assoc($run)) {
                $posts[] = $row;
            }

            return $posts;
        }
        //get post by id
        function getPostById($user_id)
        {
            global $db;
            $query = "SELECT * FROM posts WHERE user_id =$user_id ";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getPostById(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        //get poster id
        function getPosterId($post_id)
        {
            global $db;
            $query = "SELECT user_id FROM posts WHERE id=$post_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getPosterId(): " . mysqli_error($db) . " Query: " . $query);
                return null;
            }
            return mysqli_fetch_assoc($run)['user_id'];
        }

        // for getting user for follow sugesstion
        function getFollowSuggestions()
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "SELECT * FROM users WHERE id != $current_user LIMIT 7";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getFollowSuggestions(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        //function filter sugesstion list
        function filterFollowSuggestion()
        {
            $list = getFollowSuggestions();
            $filter_list  = array();
            foreach ($list as $user) {
                if (!checkFollowed($user['id'])) {
                    $filter_list[] = $user;
                }
            }
            return $filter_list;
        }

        // for checkinh the user  follwed by logined user
        function checkFollowed($user_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "SELECT count(*) as row FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in checkFollowed(): " . mysqli_error($db) . " Query: " . $query);
                return false;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        //function for follow user
        function followUser($user_id)
        {
            global $db;
            $cu = getUser($_SESSION['userdata']['id']);
            $current_user = $_SESSION['userdata']['id'];
            $query = "INSERT INTO follow_list(follower_id,user_id) VALUES ($current_user,$user_id)";
            createNotification($cu['id'], $user_id, "started following you !");
            return mysqli_query($db, $query);
        }

        //function for unfollow user
        function unfollowUser($user_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "DELETE FROM  follow_list WHERE follower_id=$current_user && user_id=$user_id";
            return mysqli_query($db, $query);
        }

        //For getting follwer count
        function getFollowersCount($user_id)
        {
            global $db;
            $query = "SELECT * FROM follow_list WHERE user_id=$user_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getFollowersCount(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        // get follwing list
        function getFollowingCount($user_id)
        {
            global $db;
            $query = "SELECT * FROM follow_list WHERE follower_id=$user_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getFollowingCount(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        //function to check liked or not
        function checkLiked($post_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "SELECT count(*) as row FROM likes WHERE user_id=$current_user && post_id=$post_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in checkLiked(): " . mysqli_error($db) . " Query: " . $query);
                return false;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        //function for like post
        function like($post_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "INSERT INTO likes(post_id,user_id) VALUES ($post_id,$current_user)";
            $poster_id = getPosterId($post_id);
            if ($poster_id != $current_user) {
                createNotification($current_user, $poster_id, "liked your post !", $post_id);
            }
            return mysqli_query($db, $query);
        }

        //function for unlike post
        function unLike($post_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "DELETE FROM  likes WHERE user_id=$current_user && post_id=$post_id ";
            $poster_id = getPosterId($post_id);
            if ($poster_id != $current_user) {
                createNotification($current_user, $poster_id, "unliked your post !", $post_id);
            }
            return mysqli_query($db, $query);
        }


        //For getting like count
        function getLikesCount($post_id)
        {
            global $db;
            $query = "SELECT * FROM likes WHERE post_id=$post_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getLikesCount(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        // function for  add comment in the post
        function addComment($post_id, $comment)
        {
            global $db;
            $comment = mysqli_real_escape_string($db, $comment);
            $current_user = $_SESSION['userdata']['id'];
            $query = "INSERT INTO comments(post_id,user_id,comment) VALUES ($post_id,$current_user,'$comment')";
            $poster_id = getPosterId($post_id);
            if ($poster_id != $current_user) {
                createNotification($current_user, $poster_id, "commented on your post", $post_id);
            }
            return mysqli_query($db, $query);
        }

        //For getting comment  count
        function getComments($post_id)
        {
            global $db;

            $query = "
        SELECT c.*, u.first_name, u.last_name, u.profile_pic, u.is_doctor
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = $post_id
        ORDER BY u.is_doctor DESC, c.created_at ASC
    ";

            $run = mysqli_query($db, $query);

            if (!$run) {
                error_log("MySQL Query Error in getComments(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }

            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        // function to show time
        function show_time($time)
        {
            // Note: This function directly returns HTML. For better separation of concerns,
            // consider returning a formatted string and embedding it in your HTML.
            return '<time style="font-size:small" class="timeago text-muted text-small" datetime="' . $time . '"></time>';
        }

        //function to delete post
        function deletePost($post_id)
        {
            global $db;
            $user_id = $_SESSION['userdata']['id'];
            $dellike = "DELETE FROM likes WHERE post_id=$post_id && user_id=$user_id";
            mysqli_query($db, $dellike);
            $delcom = "DELETE FROM comments WHERE post_id=$post_id && user_id=$user_id";
            mysqli_query($db, $delcom);
            $not = "UPDATE notifications SET read_status=2 WHERE post_id=$post_id && to_user_id=$user_id";
            mysqli_query($db, $not);
            $query = "DELETE FROM posts WHERE id=$post_id";
            return mysqli_query($db, $query);
        }

        //function to get time
        function gettime($date)
        {
            return date('H:i - (F jS, Y )', strtotime($date));
        }

        // function for block user
        function blockUser($blocked_user_id)
        {
            global $db;
            $cu = getUser($_SESSION['userdata']['id']);
            $current_user = $_SESSION['userdata']['id'];
            $query = "INSERT INTO block_list(user_id,blocked_user_id) VALUES($current_user,$blocked_user_id)";
            $query2 = "DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$blocked_user_id";
            mysqli_query($db, $query2);
            $query3 = "DELETE FROM follow_list WHERE follower_id=$blocked_user_id && user_id=$current_user";
            mysqli_query($db, $query3);
            return mysqli_query($db, $query);
        }

        //check block status
        function checkBS($user_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "SELECT count(*) as row FROM block_list WHERE (user_id=$current_user && blocked_user_id=$user_id) || (user_id=$user_id && blocked_user_id=$current_user)";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in checkBS(): " . mysqli_error($db) . " Query: " . $query);
                return false;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        //check block status
        function checkBlockStatus($current_user, $user_id)
        {
            global $db;
            $query = "SELECT count(*) as row FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in checkBlockStatus(): " . mysqli_error($db) . " Query: " . $query);
                return false;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        //for unblocking the user
        function unblockUser($user_id)
        {
            global $db;
            $current_user = $_SESSION['userdata']['id'];
            $query = "DELETE FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
            return mysqli_query($db, $query);
        }

        //function to filter post
        function filterPosts()
        {
            // This function filters posts based on block status.
            // If you want to display ALL posts regardless of block status,
            // you should call getPost() directly instead of filterPosts().
            global $db;
            $list = getPost(); // Assuming `getPost()` fetches all posts as an array
            $filter_list = array();
            $current_user = $_SESSION['userdata']['id'];
            foreach ($list as $post) {
                $post_user_id = $post['user_id'];
                if (!checkBS($post_user_id)) { // Check block status
                    $filter_list[] = $post;
                }
            }
            return $filter_list;
        }

        // function to search user
        function searchUser($keyword)
        {
            global $db;
            $query = "SELECT * FROM users WHERE username LIKE '%" . $keyword . "%' || (first_name LIKE '%" . $keyword . "%' || last_name LIKE '%" . $keyword . "%') LIMIT 5";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in searchUser(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        // functioins to created noptification
        function createNotification($from_user_id, $to_user_id, $msg, $post_id = 0)
        {
            global $db;
            $query = "INSERT INTO notifications(from_user_id,to_user_id,message,post_id) VALUES($from_user_id,$to_user_id,'$msg',$post_id)";
            mysqli_query($db, $query);
        }

        // functions to get notification
        function getNotifications()
        {
            $cu_user_id = $_SESSION['userdata']['id'];
            global $db;
            $query = "SELECT * FROM notifications WHERE to_user_id=$cu_user_id ORDER BY id DESC";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getNotifications(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        //function
        function getUnreadNotificationsCount()
        {
            $cu_user_id = $_SESSION['userdata']['id'];
            global $db;
            $query = "SELECT count(*) as row FROM notifications WHERE to_user_id=$cu_user_id && read_status=0 ORDER BY id DESC";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getUnreadNotificationsCount(): " . mysqli_error($db) . " Query: " . $query);
                return 0;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        //  function to make notification as read
        function setNotificationStatusAsRead()
        {
            $cu_user_id = $_SESSION['userdata']['id'];
            global $db;
            $query = "UPDATE notifications SET read_status=1 WHERE to_user_id=$cu_user_id";
            return mysqli_query($db, $query);
        }

        //function to get id of chat user
        function getActiveChatUserId()
        {
            global $db;
            $current_user_id = $_SESSION['userdata']['id'];
            $query = "SELECT from_user_id,to_user_id FROM messages WHERE to_user_id=$current_user_id || from_user_id=$current_user_id ORDER BY id DESC";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getActiveChatUserId(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            $data = mysqli_fetch_all($run, MYSQLI_ASSOC);
            $ids = array();
            foreach ($data as $ch) {
                if ($ch['from_user_id'] != $current_user_id && !in_array($ch['from_user_id'], $ids)) {
                    $ids[] = $ch['from_user_id'];
                }
                if ($ch['to_user_id'] != $current_user_id && !in_array($ch['to_user_id'], $ids)) {
                    $ids[] = $ch['to_user_id'];
                }
            }
            return $ids;
        }

        // function to get messages
        function getMessages($user_id)
        {
            global $db;
            $current_user_id = $_SESSION['userdata']['id'];
            $query = "SELECT  * FROM  messages WHERE (to_user_id=$current_user_id && from_user_id=$user_id) || (from_user_id=$current_user_id && to_user_id=$user_id) ORDER BY id DESC";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in getMessages(): " . mysqli_error($db) . " Query: " . $query);
                return [];
            }
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        }

        //FUNCTIOn to get all messages
        function getAllMessages()
        {
            $active_chat_ids = getActiveChatUserId();
            $conversation = array();
            foreach ($active_chat_ids as $index => $id) {
                $conversation[$index]['user_id'] = $id;
                $conversation[$index]['messages'] = getMessages($id);
            }
            return $conversation;
        }

        //function to read message stautus
        function updateReadMessageStatus($user_id)
        {
            global $db;
            $current_user_id = $_SESSION['userdata']['id'];
            $query = "UPDATE messages SET read_status=1 WHERE to_user_id=$current_user_id && from_user_id=$user_id";
            return mysqli_query($db, $query);
        }

        //function to send message
        function sendMessage($user_id, $msg)
        {
            global $db;
            $current_user_id = $_SESSION['userdata']['id'];
            $msg = mysqli_real_escape_string($db, $msg);
            $query = "INSERT INTO messages(from_user_id,to_user_id,message) VALUES($current_user_id,$user_id,'$msg')";
            updateReadMessageStatus($user_id);
            return mysqli_query($db, $query);
        }

        //function to get new msg count
        function newMsgCount()
        {
            global $db;
            $current_user_id = $_SESSION['userdata']['id'];
            $query = "SELECT count(*) as row FROM messages WHERE to_user_id=$current_user_id && read_status=0";
            $run = mysqli_query($db, $query);
            if (!$run) {
                error_log("MySQL Query Error in newMsgCount(): " . mysqli_error($db) . " Query: " . $query);
                return 0;
            }
            return mysqli_fetch_assoc($run)['row'];
        }

        function getHealthNews($cacheTime = 3600)
        {
            $cacheFile = 'cache/health_news_rss.json';

            // Check cache first (even if expired, we can use it as fallback)
            $fallbackData = null;
            if (file_exists($cacheFile)) {
                $data = file_get_contents($cacheFile);
                $fallbackData = json_decode($data, true);
                // Return cached data if still valid
                if (!empty($fallbackData) && (time() - filemtime($cacheFile)) < $cacheTime) {
                    return $fallbackData;
                }
            }

            // Ensure cache directory exists
            if (!file_exists('cache')) {
                if (!mkdir('cache', 0777, true)) {
                    error_log("Failed to create cache directory");
                }
            }

            // Try multiple RSS feeds in case one fails
            $feedUrls = [
                'https://healthnewsnepal.com/feed/',
                'https://www.nepalhealthnews.com/feed'


            ];

            $news = [];
            $successfulFeed = null;

            foreach ($feedUrls as $feedUrl) {
                try {
                    // First try with cURL
                    if (function_exists('curl_init')) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $feedUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

                        $feedContent = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $error = curl_error($ch);
                        curl_close($ch);

                        if ($httpCode === 200 && !empty($feedContent)) {
                            $xml = @simplexml_load_string($feedContent);
                            if ($xml !== false) {
                                $successfulFeed = $feedUrl;
                                break;
                            }
                        }
                    }

                    // If cURL failed, try with file_get_contents
                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 15,
                            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'ignore_errors' => true
                        ],
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ]);

                    $feedContent = @file_get_contents($feedUrl, false, $context);

                    if ($feedContent !== false) {
                        $xml = @simplexml_load_string($feedContent);
                        if ($xml !== false) {
                            $successfulFeed = $feedUrl;
                            break;
                        }
                    }
                } catch (Exception $e) {
                    error_log("Exception trying feed $feedUrl: " . $e->getMessage());
                    continue;
                }
            }

            if ($successfulFeed === null) {
                error_log("All RSS feeds failed");
                // If we have cached data, return it as last resort
                if (!empty($fallbackData)) {
                    error_log("Returning cached health news due to fetch failure");
                    return $fallbackData;
                }
                return ['error' => 'Unable to fetch health news at this time. Please try again later.'];
            }

            // Determine source name based on URL
            $sourceName = 'Health News';
            if (strpos($successfulFeed, 'nepalihealth') !== false) {
                $sourceName = 'Nepali Health';
            } elseif (strpos($successfulFeed, 'webmd') !== false) {
                $sourceName = 'WebMD';
            } elseif (strpos($successfulFeed, 'mayoclinic') !== false) {
                $sourceName = 'Mayo Clinic';
            } elseif (strpos($successfulFeed, 'medicalnewstoday') !== false) {
                $sourceName = 'Medical News Today';
            }

            // Parse RSS feed items
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    $title = (string)$item->title;
                    $link = (string)$item->link;
                    $pubDate = (string)$item->pubDate;

                    if (!empty($title) && !empty($link) && !empty($pubDate)) {
                        $news[] = [
                            'id' => md5($link),
                            'title' => $title,
                            'source' => $sourceName,
                            'published_at' => strtotime($pubDate),
                            'url' => $link
                        ];
                    }
                }
            }

            if (empty($news)) {
                error_log("No news items found in feed: $successfulFeed");
                // If we have cached data, return it as last resort
                if (!empty($fallbackData)) {
                    error_log("Returning cached health news due to empty feed");
                    return $fallbackData;
                }
                return ['error' => 'No health news items found at this time.'];
            }

            // Sort by published_at (newest first)
            usort($news, function ($a, $b) {
                return $b['published_at'] - $a['published_at'];
            });

            // Limit to 10 items
            $news = array_slice($news, 0, 10);

            // Save to cache
            file_put_contents($cacheFile, json_encode($news));

            return $news;
        }

        // Updated getBookedAppointments function
        function getBookedAppointments($doctor_id)
        {
            global $db;
            $doctor_id = (int)$doctor_id;
            // Get today's date to filter future appointments only
            $today = date('Y-m-d');
            $query = "SELECT datetime, a_satus 
              FROM appointment 
              WHERE doctor_id = $doctor_id 
              AND DATE(datetime) >= '$today'
              AND a_satus IN (0, 1)"; // Only pending and approved appointments
            $run = mysqli_query($db, $query);
            $result = [];
            if ($run) {
                while ($row = mysqli_fetch_assoc($run)) {
                    // Extract date and time from datetime
                    $date = date('Y-m-d', strtotime($row['datetime']));
                    $time = date('H:i', strtotime($row['datetime']));
                    $result[$date][] = $time;
                }
            }
            return $result;
        }

        // Updated bookAppointment function
        function bookAppointment($doctor_id, $patient_id, $date, $time, $desc, $reason)
        {
            global $db;

            // Check if patient_id is valid
            if (empty($patient_id)) {
                return [
                    'success' => false,
                    'message' => 'Invalid patient ID.'
                ];
            }

            // Sanitize inputs
            $doctor_id   = (int)$doctor_id;
            $patient_id  = (int)$patient_id;
            $date        = mysqli_real_escape_string($db, $date);
            $time        = mysqli_real_escape_string($db, $time);
            $desc        = mysqli_real_escape_string($db, $desc);
            $reason      = mysqli_real_escape_string($db, $reason);

            // Combine date and time into datetime format
            $datetime = $date . ' ' . $time . ':00'; // Format: YYYY-MM-DD HH:MM:SS

            // Get patient name from session
            $patient_name = isset($_SESSION['userdata']['first_name']) && isset($_SESSION['userdata']['last_name'])
                ? mysqli_real_escape_string($db, $_SESSION['userdata']['first_name'] . ' ' . $_SESSION['userdata']['last_name'])
                : '';

            // Check if the time slot is available
            $check_query = "SELECT id FROM appointment 
           WHERE doctor_id = $doctor_id 
           AND DATE(datetime) = '$date' 
           AND TIME(datetime) = '$time' 
           AND a_satus IN (0, 1)";
            $check_result = mysqli_query($db, $check_query);

            if (!$check_result) {
                $error = mysqli_error($db);
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $error
                ];
            }

            if (mysqli_num_rows($check_result) > 0) {
                return [
                    'success' => false,
                    'message' => 'This time slot is already booked.'
                ];
            }

            // Insert the new appointment
            $query = "INSERT INTO appointment 
          (doctor_id, patient_id, datetime, created_at, patient_name, patient_desc, a_satus, reason) 
          VALUES 
          ($doctor_id, $patient_id, '$datetime', NOW(), '$patient_name', '$desc', 0, '$reason')";
            $result = mysqli_query($db, $query);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Appointment booked successfully.'
                ];
            } else {
                $error = mysqli_error($db);
                return [
                    'success' => false,
                    'message' => 'Failed to book appointment: ' . $error
                ];
            }
        }
        // Get patient appointments
        function getPatientAppointments($patient_id)
        {
            global $db;
            $patient_id = (int)$patient_id;

            $query = "SELECT a.*, 
              u.first_name, u.last_name, u.doctor_type, u.doctor_address 
              FROM appointment a
              JOIN users u ON a.doctor_id = u.id
              WHERE a.patient_id = $patient_id
              ORDER BY a.datetime DESC";

            $result = mysqli_query($db, $query);
            $appointments = ['upcoming' => [], 'approved' => [], 'completed' => []];

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $datetime = new DateTime($row['datetime']);
                    $now = new DateTime();

                    // Categorize appointments by status
                    if ($row['a_satus'] == 0) {
                        // Pending appointments
                        $appointments['upcoming'][] = $row;
                    } elseif ($row['a_satus'] == 1) {
                        // Approved appointments
                        $appointments['approved'][] = $row;
                    } else {
                        // Completed appointments
                        $appointments['completed'][] = $row;
                    }
                }
            }

            return $appointments;
        }
        // Get doctor appointments
        function getDoctorAppointments($doctor_id)
        {
            global $db;
            $doctor_id = (int)$doctor_id;

            $query = "SELECT a.*, 
              u.first_name, u.last_name, u.email 
              FROM appointment a
              JOIN users u ON a.patient_id = u.id
              WHERE a.doctor_id = $doctor_id
              ORDER BY a.datetime DESC";

            $result = mysqli_query($db, $query);
            $appointments = ['pending' => [], 'approved' => [], 'declined' => []];

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Categorize appointments by status
                    if ($row['a_satus'] == 0) {
                        // Pending appointments
                        $appointments['pending'][] = $row;
                    } elseif ($row['a_satus'] == 1) {
                        // Approved appointments
                        $appointments['approved'][] = $row;
                    } else {
                        // Declined appointments (status 2 or any other)
                        $appointments['declined'][] = $row;
                    }
                }
            }

            return $appointments;
        }

        // Update appointment status
        function updateAppointmentStatus($appointment_id, $status)
        {
            global $db;
            $appointment_id = (int)$appointment_id;
            $status = (int)$status;

            $query = "UPDATE appointment SET a_satus = $status WHERE id = $appointment_id";
            $result = mysqli_query($db, $query);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Appointment status updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update appointment status: ' . mysqli_error($db)
                ];
            }
        }
                    ?>

    <?php
    require_once 'function.php';
    require_once 'send_code.php';





    //php for signup validating and help user to signup in healthlink
    if (isset($_GET['signup'])) {
        $response = validateSignupForm($_POST);
        if ($response['status']) {
            if (createUser($_POST)) {
                header('Location: ../../?login');
            } else {
                echo "<script>alert('Something is wrong')</script>";
            }
        } else {
            $_SESSION['error'] = $response;
            $_SESSION['formdata'] = $_POST;
            header("location:../../?signup");
        }
    }

    //php for login validation for user
    if (isset($_GET['login'])) {

        $response = validateLoginForm($_POST);
        if ($response['status']) {
            $_SESSION['Auth'] = true;
            $_SESSION['userdata'] = $response['user'];
            if ($response['user']['ac_status'] == 0) {
                $_SESSION['code'] = $code = rand(111111, 999999);
                sendCode($response['user']['email'], 'Verify Your Email', $code);
            }
            header("location:../../");
        } else {
            $_SESSION['error'] = $response;
            $_SESSION['formdata'] = $_POST;
            header("location:../../?login");
        }
    }
    if (isset($_GET['resend_code'])) {
        $_SESSION['code'] = $code = rand(111111, 999999);
        sendCode($_SESSION['userdata']['email'], 'Verify Your Email', $code);
        header("location:../../?codesent");
    }
    if (isset($_GET['verify_email'])) {
        $user_code = $_POST['code'];
        $code = $_SESSION['code'];
        if ($user_code == $code) {
            if (verifyEmail($_SESSION['userdata']['email'])) {
                header("location:../../");
            } else {
                echo "<script>alert('Something is wrong')</script>";
            }
        } else {
            $response['msg'] = 'Incorrect Verification Code';
            if (!$_POST['code']) {
                $response['msg'] = 'Enter 6 digit Code';
            }
            $response['field'] = 'email_verify';
            $_SESSION['error'] = $response;
            header("location:../../");
        }
    }
    // for forgotting password
    if (isset($_GET['forgotpassword'])) {
        if (!$_POST['email']) {
            $response['msg'] = "Please Enter Email Id";
            $response['field'] = 'email';
            $_SESSION['error'] = $response;
            header("location:../../?forgotpassword");
        } else if (!isEmailRegistered($_POST['email'])) {
            $response['msg'] = "Email id is not  registered";
            $response['field'] = 'email';
            $_SESSION['error'] = $response;
            header("location:../../?forgotpassword");
        } else {
            $_SESSION['forgot_email'] = $_POST['email'];
            $_SESSION['forgot_email'] = $_POST['email'];
            $_SESSION['forgot_code'] = $code = rand(111111, 999999);
            sendCode($_POST['email'], 'forgot Your Password', $code);
            header("location:../../?forgotpassword&codesent");
        }
    }

    // for user forgot code verify 
    if (isset($_GET['verifycode'])) {
        $user_code = $_POST['code'];
        $code = $_SESSION['forgot_code'];
        if ($user_code == $code) {
            $_SESSION['auth_temp'] = true;
            header("location:../../?forgotpassword");
        } else {
            $response['msg'] = 'Incorrect Verification Code';
            if (!$_POST['code']) {
                $response['msg'] = 'Enter 6 digit Code';
            }
            $response['field'] = 'email_verify';
            $_SESSION['error'] = $response;
            header("location:../../?forgotpassword");
        }
    }
    //now function to eset password
    if (isset($_GET['changepassword'])) {
        if (!$_POST['password']) {
            $response['msg'] = "enter your new password";
            $response['field'] = 'password';
            $_SESSION['error'] = $response;
            header('location:../../?forgotpassword');
        } else {
            resetPassword($_SESSION['forgot_email'], $_POST['password']);
            session_destroy();
            header('location:../../?reseted');
        }
    }
    //for live sreach
    if (isset($_GET['live_search'])) {
        header('Content-Type: application/json');
        $keyword = $_POST['keyword'] ?? '';
        $results = liveSearch($keyword);
        echo json_encode($results);
        exit;
    }

    //edit profile 
    if (isset($_GET['updateprofile'])) {

        $response = validateUpdateForm($_POST, $_FILES['profile_pic']);

        if ($response['status']) {

            if (updateProfile($_POST, $_FILES['profile_pic'])) {
                header("location:../../?editprofile&success");
            } else {
                echo "something is wrong";
            }
        } else {
            $_SESSION['error'] = $response;
            header("location:../../?editprofile");
        }
    }
    //verify doctor
    if (isset($_GET['verifydoctor'])) {
        // Debugging - log all POST data
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        $response = validateVerifyDoctorForm($_POST, $_FILES);
        if ($response['status']) {
            $result = insertVerifyDoctor($_POST, $_FILES);
            if ($result === true) {
                header("Location: ../../?verificationsucess");
                exit;
            } else {
                // Log the error for debugging
                error_log("Insert failed: " . $result);

                // Show error to user
                $_SESSION['error'] = [
                    'status' => false,
                    'msg' => "Error submitting form: " . $result,
                    'field' => 'general'
                ];
                header("Location: ../../?verifydoctor");
                exit;
            }
        } else {
            $_SESSION['error'] = $response;
            header("Location: ../../?verifydoctor");
            exit;
        }
    }



    //chnage the location
    if (isset($_GET['refreshNumber'])) {
        $district = $_POST['district'];
        if (!$district) {
            $response['msg'] = "Please Select District";
            $response['field'] = 'district';
            $_SESSION['error'] = $response;
            header("location:../../");
        } else {
            if (changeLocation($district)) {
                header("location:../../");
            } else {
                echo "something went wrong";
            }
        }
    }

    // actions to submit appointment 
    if (isset($_GET['book_appointment'])) {
        // Check if user is logged in
        if (!isset($_SESSION['userdata']['id']) || empty($_SESSION['userdata']['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'You must be logged in to book an appointment.'
            ]);
            exit();
        }

        $date = $_POST['date'];
        $time = $_POST['time'];
        $doctor_id = $_POST['doctor_id'];
        $patient_id = $_SESSION['userdata']['id'];
        $desc = $_POST['desc'];
        $reason = $_POST['reason'];

        // Validate required fields
        if (!$date || !$time) {
            echo json_encode([
                'success' => false,
                'message' => 'Please select date and time.'
            ]);
            exit();
        }

        if (!$doctor_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid doctor ID.'
            ]);
            exit();
        }

        // Call the booking function
        $booking = bookAppointment($doctor_id, $patient_id, $date, $time, $desc, $reason);
        echo json_encode($booking);
        exit();
    }

    // managing add post 

    // Managing add post 
    if (isset($_GET['addnocodepost'])) {
        $response = validateNewPost($_POST, $_FILES['post_img']);
        if ($response['status']) {
            if (createNoCodePost($_POST, $_FILES['post_img'])) {
                header("location:../../?new_post_added");
            } else {
                echo 'Something went wrong';
            }
        } else {
            $_SESSION['error'] = $response;
            header("location:../../");
        }
    }




    //ajax for delete post
    if (isset($_GET['deletepost'])) {
        $post_id = $_GET['deletepost'];
        if (deletePost($post_id)) {
            header("location:{$_SERVER['HTTP_REFERER']}");
        } else {
            echo "something went wrong";
        }
    }
    // blocking user
    if (isset($_GET['block'])) {
        $user_id = $_GET['block'];
        $user = $_GET['username'];
        if (blockUser($user_id)) {
            header("location:../../?u=$user");
        } else {
            echo "something went wrong";
        }
    }


    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $host = 'localhost';
    $db = 'healthlink';
    $user = 'root';
    $pass = '';
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve POST data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $username = $_POST['username'] ?? '';
    $gender = $_POST['gender'] ?? '';


    if (empty($email)) {
        echo "Missing email"; // More specific error message
        exit;
    }


    if (empty($firstName)) {
        // This is an email availability check
        $checkQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            exit;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "exists";
        } else {
            echo "available";
        }
        $stmt->close();
        $conn->close();
        exit;
    }


    if (empty($password) || empty($firstName) || empty($lastName) || empty($username) || empty($gender)) {
        echo "Missing required fields for signup";
        exit;
    }

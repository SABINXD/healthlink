<?php
require_once($function_url ?? '../../assets/php/function.php');

// Function to get doctor verification requests with pagination
function getDoctorVerificationRequests($limit = 20, $offset = 0, $status = 'all')
{
    global $db;
    if (!$db) {
        error_log("Database connection not available in getDoctorVerificationRequests");
        return [];
    }
    $statusCondition = '';
    if ($status !== 'all') {
        // Map string to integer
        $statusInt = ($status == 'pending') ? 0 : (($status == 'approved') ? 1 : 2);
        $statusCondition = "WHERE dv.status = $statusInt";
    }
    $query = "SELECT dv.*, u.first_name, u.last_name, u.email, u.is_doctor 
              FROM doctor_verification dv
              LEFT JOIN users u ON dv.user_id = u.id
              $statusCondition
              ORDER BY dv.created_at DESC
              LIMIT $limit OFFSET $offset";

    $result = mysqli_query($db, $query);
    if (!$result) {
        error_log("Query error: " . mysqli_error($db));
        return [];
    }

    $requests = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $requests[] = $row;
    }

    return $requests;
}

function getDoctorVerificationStats()
{
    global $db;
    if (!$db) {
        error_log("Database connection not available in getDoctorVerificationStats");
        return [
            'pending' => 0,
            'approved' => 0,
            'declined' => 0,
            'total' => 0
        ];
    }

    $stats = [
        'pending' => 0,
        'approved' => 0,
        'declined' => 0,
        'total' => 0
    ];

    // Count pending (status = 0)
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status = 0";
    $result = mysqli_query($db, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['pending'] = $row['count'];
    }

    // Count approved (status = 1)
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status = 1";
    $result = mysqli_query($db, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['approved'] = $row['count'];
    }

    // Count declined (status = 2)
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status = 2";
    $result = mysqli_query($db, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['declined'] = $row['count'];
    }

    $stats['total'] = $stats['pending'] + $stats['approved'] + $stats['declined'];
    return $stats;
}

function approveDoctorVerification($request_id, $notes = '')
{
    global $db;
    mysqli_begin_transaction($db);
    try {
        // Get user_id and email from the verification request
        $query = "SELECT dv.user_id, u.email FROM doctor_verification dv
                  JOIN users u ON dv.user_id = u.id
                  WHERE dv.id = $request_id";
        $result = mysqli_query($db, $query);
        if (!$result || mysqli_num_rows($result) == 0) {
            throw new Exception("Verification request not found");
        }
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];
        $email = $row['email'];

        // Update user status to doctor (1)
        $query = "UPDATE users SET is_doctor = 1 WHERE id = $user_id";
        if (!mysqli_query($db, $query)) {
            throw new Exception("Failed to update user status");
        }

        // Update verification request status to 1 (approved)
        $query = "UPDATE doctor_verification SET status = 1, updated_at = NOW(), notes = '" . mysqli_real_escape_string($db, $notes) . "' WHERE id = $request_id";
        if (!mysqli_query($db, $query)) {
            throw new Exception("Failed to update verification status");
        }

        // Send approval notification email
        sendApprovalEmail($email, $notes);

        mysqli_commit($db);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($db);
        error_log("Error in approveDoctorVerification: " . $e->getMessage());
        return false;
    }
}

function declineDoctorVerification($request_id, $notes = '')
{
    global $db;
    mysqli_begin_transaction($db);
    try {
        // Get user_id and email from the verification request
        $query = "SELECT dv.user_id, u.email FROM doctor_verification dv
                  JOIN users u ON dv.user_id = u.id
                  WHERE dv.id = $request_id";
        $result = mysqli_query($db, $query);
        if (!$result || mysqli_num_rows($result) == 0) {
            throw new Exception("Verification request not found");
        }
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];
        $email = $row['email'];

        // Update user status to not doctor (0)
        $query = "UPDATE users SET is_doctor = 0 WHERE id = $user_id";
        if (!mysqli_query($db, $query)) {
            throw new Exception("Failed to update user status");
        }

        // Update verification request status to 2 (declined)
        $query = "UPDATE doctor_verification SET status = 2, updated_at = NOW(), notes = '" . mysqli_real_escape_string($db, $notes) . "' WHERE id = $request_id";
        if (!mysqli_query($db, $query)) {
            throw new Exception("Failed to update verification status");
        }

        // Send decline notification email
        sendDeclineEmail($email, $notes);

        mysqli_commit($db);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($db);
        error_log("Error in declineDoctorVerification: " . $e->getMessage());
        return false;
    }
}
// Function to send approval email
function sendApprovalEmail($email, $notes)
{
    $subject = "Your Doctor Verification Has Been Approved";
    $message = "
    <html>
    <head>
        <title>Doctor Verification Approved</title>
    </head>
    <body>
        <h2>Congratulations!</h2>
        <p>Your doctor verification request has been approved.</p>
        <p>You now have full access to doctor features on our platform.</p>
        " . ($notes ? "<p><strong>Admin Notes:</strong> " . htmlspecialchars($notes) . "</p>" : "") . "
        <p>Thank you for using our platform.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <noreply@healthlink.com>' . "\r\n";

    mail($email, $subject, $message, $headers);
}

// Function to send decline email
function sendDeclineEmail($email, $notes)
{
    $subject = "Your Doctor Verification Status";
    $message = "
    <html>
    <head>
        <title>Doctor Verification Status</title>
    </head>
    <body>
        <h2>Verification Status Update</h2>
        <p>We regret to inform you that your doctor verification request has been declined.</p>
        " . ($notes ? "<p><strong>Reason:</strong> " . htmlspecialchars($notes) . "</p>" : "") . "
        <p>You may reapply after addressing the issues mentioned above.</p>
        <p>Thank you for your understanding.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <noreply@healthlink.com>' . "\r\n";

    mail($email, $subject, $message, $headers);
}

// Other existing functions remain unchanged
function checkAdminUser($login_data)
{
    global $db;
    $email = $login_data['email'];
    $password = $login_data['password'];
    $query = "SELECT * FROM admin WHERE email='$email' && password='$password'";
    $run = mysqli_query($db, $query);
    $data['user'] = mysqli_fetch_assoc($run) ?? array();
    if (count($data['user']) > 0) {
        $data['status'] = true;
        $data['user_id'] = $data['user']['id'];
    } else {
        $data['status'] = false;
    }
    return $data;
}

function getAdmin($user_id)
{
    global $db;
    $query = "SELECT * FROM admin WHERE id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run);
}

function totalCommentsCount()
{
    global $db;
    $query = "SELECT count(*) as row FROM comments";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
}

function totalPostsCount()
{
    global $db;
    $query = "SELECT count(*) as row FROM posts";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
}

function totalUsersCount()
{
    global $db;
    $query = "SELECT count(*) as row FROM users";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
}

function totalLikesCount()
{
    global $db;
    $query = "SELECT count(*) as row FROM likes";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
}

function getUsersList()
{
    global $db;
    $query = "SELECT * FROM users ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
}

function loginUserByAdmin($email)
{
    global $db;
    $query = "SELECT * FROM users WHERE email='$email'";
    $run = mysqli_query($db, $query);
    $data['user'] = mysqli_fetch_assoc($run) ?? array();
    if (count($data['user']) > 0) {
        $data['status'] = true;
    } else {
        $data['status'] = false;
    }
    return $data;
}

function blockUserByAdmin($user_id)
{
    global $db;
    $query = "UPDATE users SET ac_status=2 WHERE id=$user_id";
    return mysqli_query($db, $query);
}

function unblockUserByAdmin($user_id)
{
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE id=$user_id";
    return mysqli_query($db, $query);
}

function updateAdmin($data)
{
    global $db;
    $password = $data['password'];
    $password_text = $data['password'];
    $full_name = $data['full_name'];
    $email = $data['email'];
    $user_id = $data['user_id'];
    $query = "UPDATE admin SET full_name='$full_name',email='$email',password='$password',password_text='$password_text' WHERE id=$user_id";
    return mysqli_query($db, $query);
}

function deletePostByAdmin($post_id)
{
    global $db;
    $dellike = "DELETE FROM likes WHERE post_id=$post_id ";
    mysqli_query($db, $dellike);
    $delcom = "DELETE FROM comments WHERE post_id=$post_id";
    mysqli_query($db, $delcom);
    $not = "UPDATE notifications SET read_status=2 WHERE post_id=$post_id";
    mysqli_query($db, $not);
    $userId = getUserByPostId($post_id);
    createNotification(1, $userId, "has  deleted Your post !", $post_id);
    $query = "DELETE FROM posts WHERE id=$post_id";
    return mysqli_query($db, $query);
}

function getUserByPostId($post_id)
{
    global $db;
    $query = "SELECT user_id FROM posts WHERE id = $post_id";
    $result = mysqli_query($db, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            return $row['user_id'];
        } else {
            error_log("No user found for post ID: " . $post_id);
            return false;
        }
    } else {
        error_log("MySQL Error in getUserByPostId: " . mysqli_error($db));
        return false;
    }
}

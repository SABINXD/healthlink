<?php
require_once($function_url ?? '../../assets/php/function.php');
//for checking the user
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
// function.php
function getUserByPostId($post_id)
{
    global $db;
    $query = "SELECT user_id FROM posts WHERE id = $post_id";
    $result = mysqli_query($db, $query);
    if ($result) {  // Check if the query executed successfully
        $row = mysqli_fetch_assoc($result);
        if ($row) { // Check if a row was returned
            return $row['user_id'];
        } else {
            // Log that no user was found (important for debugging)
            error_log("No user found for post ID: " . $post_id);
            return false; // Return false if no user is found
        }
    } else {
        // Log the MySQL error (essential for debugging)
        error_log("MySQL Error in getUserByPostId: " . mysqli_error($db));
        return false; // Return false if the query fails
    }
}
// Function to get doctor verification requests
function getDoctorVerificationRequests()
{
    global $db;
    $query = "SELECT dv.*, u.first_name, u.last_name, u.email, u.is_doctor 
              FROM doctor_verification dv
              JOIN users u ON dv.user_id = u.id
              ORDER BY dv.applied_at DESC";
    $run = mysqli_query($db, $query);
    $requests = mysqli_fetch_all($run, true);
    // Handle NULL or empty status values
    foreach ($requests as &$request) {
        if (empty($request['status'])) {
            $request['status'] = 'pending';
        }
    }
    return $requests;
}
// Function to approve doctor verification
function approveDoctorVerification($request_id)
{
    global $db;
    // Start transaction
    mysqli_begin_transaction($db);
    try {
        // Get user_id from the verification request
        $query = "SELECT user_id FROM doctor_verification WHERE id = $request_id";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];
        // Update user status to doctor (1)
        $query = "UPDATE users SET is_doctor = 1 WHERE id = $user_id";
        mysqli_query($db, $query);
        // Update verification request status
        $query = "UPDATE doctor_verification SET status = 'approved', reviewed_at = NOW() WHERE id = $request_id";
        mysqli_query($db, $query);
        // Commit transaction
        mysqli_commit($db);
        return true;
    } catch (Exception $e) {
        // Rollback in case of error
        mysqli_rollback($db);
        return false;
    }
}
// Function to decline doctor verification
function declineDoctorVerification($request_id)
{
    global $db;
    // Start transaction
    mysqli_begin_transaction($db);
    try {
        // Get user_id from the verification request
        $query = "SELECT user_id FROM doctor_verification WHERE id = $request_id";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];
        // Update user status to not doctor (0)
        $query = "UPDATE users SET is_doctor = 0 WHERE id = $user_id";
        mysqli_query($db, $query);
        // Update verification request status
        $query = "UPDATE doctor_verification SET status = 'declined', reviewed_at = NOW() WHERE id = $request_id";
        mysqli_query($db, $query);
        // Commit transaction
        mysqli_commit($db);
        return true;
    } catch (Exception $e) {
        // Rollback in case of error
        mysqli_rollback($db);
        return false;
    }
}
function getDoctorVerificationStats()
{
    global $db;
    $stats = [
        'pending' => 0,
        'approved' => 0,
        'declined' => 0,
        'total' => 0
    ];
    
    // Count pending (including NULL and empty values)
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status IS NULL OR status = '' OR status = 'pending'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $stats['pending'] = $row['count'];
    
    // Count approved
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status = 'approved'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $stats['approved'] = $row['count'];
    
    // Count declined
    $query = "SELECT COUNT(*) as count FROM doctor_verification WHERE status = 'declined'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $stats['declined'] = $row['count'];
    
    $stats['total'] = $stats['pending'] + $stats['approved'] + $stats['declined'];
    return $stats;
}
?>
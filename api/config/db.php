<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthlink";

function getLocalIPv4() {
    $output = [];
    exec("ipconfig", $output);
    $currentAdapter = '';
    $preferredIp = '';
    foreach ($output as $line) {
        $line = trim($line);
        // Detect adapter header
        if (preg_match('/adapter (.+):/', $line, $matches)) {
            $currentAdapter = strtolower($matches[1]);
            continue;
        }
        if (
            str_contains($currentAdapter, 'radmin') ||
            str_contains($currentAdapter, 'virtual') ||
            str_contains($currentAdapter, 'loopback') ||
            str_contains($currentAdapter, 'vmware') ||
            str_contains($currentAdapter, 'tunnel') ||
            str_contains($currentAdapter, 'bluetooth')
        ) {
            continue;
        }
        // Match IPv4 address from preferred adapter
        if (preg_match("/IPv4 Address.*?: ([\d.]+)/", $line, $matches)) {
            $ip = $matches[1];
            // Only use private IP ranges
            if (preg_match('/^(192\.168|10\.|172\.(1[6-9]|2[0-9]|3[0-1]))\./', $ip)) {
                $preferredIp = $ip;
                break; // First valid IP is enough
            }
        }
    }
    return $preferredIp ?: '10.40.20.108'; // Use your server IP as fallback
}


$IP_ADDRESS = getLocalIPv4();

define('IP_ADDRESS', $IP_ADDRESS);

global $db;
$db = null;

function getDbConnection() {
    global $servername, $username, $password, $dbname, $db;
    
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        if (!$conn->set_charset("utf8mb4")) {
            throw new Exception("Error loading character set utf8mb4: " . $conn->error);
        }
        
        $db = $conn;
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw $e;
    }
}

try {
    $db = getDbConnection();
} catch (Exception $e) {
    // Log error for debugging
    error_log("Database connection error: " . $e->getMessage());
    
    // Return JSON error response if this is an API call
    if (!empty($_SERVER['HTTP_CONTENT_TYPE']) && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database connection failed',
            'error_details' => $e->getMessage()
        ]);
    }
    
    die();
}

// ✅ Optional for debugging:
// echo "Server IPv4: " . IP_ADDRESS;
?>
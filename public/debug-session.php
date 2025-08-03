<?php
// Debug script to check current session status
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<hr>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
if (empty($_SESSION)) {
    echo "No active session data found.\n";
} else {
    print_r($_SESSION);
}
echo "</pre>";

echo "<h3>Session ID:</h3>";
echo "<p>Session ID: " . session_id() . "</p>";

echo "<h3>Session Status:</h3>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session Status Meaning: ";
switch (session_status()) {
    case PHP_SESSION_DISABLED:
        echo "Sessions are disabled";
        break;
    case PHP_SESSION_NONE:
        echo "Sessions are enabled, but none exists";
        break;
    case PHP_SESSION_ACTIVE:
        echo "Sessions are enabled, and one exists";
        break;
}
echo "</p>";

echo "<h3>Cookies:</h3>";
echo "<pre>";
if (empty($_COOKIE)) {
    echo "No cookies found.\n";
} else {
    print_r($_COOKIE);
}
echo "</pre>";

echo "<h3>User Authentication Check:</h3>";
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'none';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'none';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'none';

echo "<p>Is Logged In: " . ($is_logged_in ? 'YES' : 'NO') . "</p>";
echo "<p>User ID: " . $user_id . "</p>";
echo "<p>Username: " . $username . "</p>";
echo "<p>User Role: " . $user_role . "</p>";

echo "<h3>Database Connection Test:</h3>";
try {
    require_once '../config/database.php';
    echo "<p style='color: green;'>Database connection: SUCCESS</p>";
    
    if ($is_logged_in && $user_id !== 'none') {
        $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            echo "<h4>Database User Info:</h4>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>No user found in database with ID: " . $user_id . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h3>Server Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Script Path: " . __FILE__ . "</p>";

echo "<hr>";
echo "<h3>Actions:</h3>";
echo "<a href='logout.php' style='background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Force Logout</a> ";
echo "<a href='index.php' style='background: blue; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Go to Home</a> ";
echo "<a href='seller-channel.php' style='background: orange; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Go to Seller Channel</a>";
?>
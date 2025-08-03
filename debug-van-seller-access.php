<?php
/**
 * Debug Script: Investigate why Vân is seeing seller dashboard
 * This script will check all possible reasons for seller access
 */

require_once 'config/database.php';

echo "<h1>Debug: Vân Seller Access Investigation</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .error { color: red; font-weight: bold; }
    .success { color: green; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "<div class='section'>";
    echo "<h2>1. Database Connection</h2>";
    echo "<p class='success'>✓ Database connection successful</p>";
    echo "</div>";
    
    // Search for Vân in the database
    echo "<div class='section'>";
    echo "<h2>2. Finding Vân in Database</h2>";
    
    // Search by first name
    $stmt = $pdo->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ?");
    $stmt->execute(['%Vân%', '%Vân%']);
    $vanUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($vanUsers)) {
        echo "<p class='warning'>⚠ No users found with name containing 'Vân'</p>";
        
        // Let's search for similar names
        $stmt = $pdo->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR last_name LIKE ?");
        $stmt->execute(['%Van%', '%Van%', '%Văn%', '%Văn%']);
        $similarUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($similarUsers)) {
            echo "<p class='info'>Found similar users:</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>User Type</th><th>Created</th></tr>";
            foreach ($similarUsers as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td class='" . ($user['user_type'] === 'seller' ? 'error' : 'success') . "'>" . htmlspecialchars($user['user_type']) . "</td>";
                echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p class='success'>✓ Found " . count($vanUsers) . " user(s) with name containing 'Vân':</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>User Type</th><th>Created</th><th>Last Login</th></tr>";
        foreach ($vanUsers as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td class='" . ($user['user_type'] === 'seller' ? 'error' : 'success') . "'>" . htmlspecialchars($user['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($user['last_login'] ?? 'Never') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // Check current session
    echo "<div class='section'>";
    echo "<h2>3. Current Session Analysis</h2>";
    session_start();
    
    if (isset($_SESSION['user_id'])) {
        echo "<p class='info'>Current session user ID: " . $_SESSION['user_id'] . "</p>";
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currentUser) {
            echo "<p>Current logged-in user:</p>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($currentUser as $key => $value) {
                $class = ($key === 'user_type' && $value === 'seller') ? 'error' : '';
                echo "<tr><td>" . htmlspecialchars($key) . "</td><td class='$class'>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>✗ Session user ID exists but user not found in database!</p>";
        }
    } else {
        echo "<p class='warning'>⚠ No active session found</p>";
    }
    
    echo "<p>All session variables:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div>";
    
    // Check all recent seller activities
    echo "<div class='section'>";
    echo "<h2>4. Recent Seller Activities</h2>";
    
    $stmt = $pdo->query("
        SELECT u.first_name, u.last_name, u.email, u.user_type, u.created_at, u.last_login
        FROM users u 
        WHERE u.user_type = 'seller' 
        ORDER BY u.last_login DESC, u.created_at DESC
        LIMIT 10
    ");
    $recentSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Recent seller accounts (last 10):</p>";
    echo "<table>";
    echo "<tr><th>Name</th><th>Email</th><th>User Type</th><th>Created</th><th>Last Login</th></tr>";
    foreach ($recentSellers as $seller) {
        $name = htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']);
        $highlight = (strpos($name, 'Vân') !== false || strpos($name, 'Van') !== false) ? 'error' : '';
        echo "<tr class='$highlight'>";
        echo "<td>" . $name . "</td>";
        echo "<td>" . htmlspecialchars($seller['email']) . "</td>";
        echo "<td>" . htmlspecialchars($seller['user_type']) . "</td>";
        echo "<td>" . htmlspecialchars($seller['created_at']) . "</td>";
        echo "<td>" . htmlspecialchars($seller['last_login'] ?? 'Never') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Check for any account type changes
    echo "<div class='section'>";
    echo "<h2>5. Account Type Change Detection</h2>";
    
    // Check if there's an audit log table
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('user_audit_log', $tables) || in_array('audit_log', $tables)) {
        echo "<p class='info'>Found audit log table - checking for account changes...</p>";
        // Add audit log queries here if table exists
    } else {
        echo "<p class='warning'>⚠ No audit log table found</p>";
    }
    
    // Check for recent user_type changes by looking at creation patterns
    $stmt = $pdo->query("
        SELECT 
            DATE(created_at) as date,
            user_type,
            COUNT(*) as count,
            GROUP_CONCAT(CONCAT(first_name, ' ', last_name, ' (', email, ')') SEPARATOR ', ') as users
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAYS)
        GROUP BY DATE(created_at), user_type
        ORDER BY created_at DESC
    ");
    $recentChanges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Recent account creations (last 7 days):</p>";
    echo "<table>";
    echo "<tr><th>Date</th><th>Type</th><th>Count</th><th>Users</th></tr>";
    foreach ($recentChanges as $change) {
        $highlight = ($change['user_type'] === 'seller') ? 'warning' : '';
        echo "<tr class='$highlight'>";
        echo "<td>" . htmlspecialchars($change['date']) . "</td>";
        echo "<td>" . htmlspecialchars($change['user_type']) . "</td>";
        echo "<td>" . htmlspecialchars($change['count']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($change['users'], 0, 200)) . (strlen($change['users']) > 200 ? '...' : '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Check authentication logic
    echo "<div class='section'>";
    echo "<h2>6. Authentication Logic Check</h2>";
    
    if (file_exists('includes/header.php')) {
        echo "<p class='info'>✓ Header.php exists - checking authentication logic</p>";
        $headerContent = file_get_contents('includes/header.php');
        
        // Look for user_type checks
        if (strpos($headerContent, 'user_type') !== false) {
            echo "<p class='success'>✓ User type checks found in header</p>";
        } else {
            echo "<p class='error'>✗ No user type checks found in header</p>";
        }
        
        // Look for session management
        if (strpos($headerContent, 'session_start') !== false) {
            echo "<p class='success'>✓ Session management found</p>";
        } else {
            echo "<p class='warning'>⚠ No session_start found in header</p>";
        }
    } else {
        echo "<p class='error'>✗ Header.php not found</p>";
    }
    echo "</div>";
    
    // Provide recommendations
    echo "<div class='section'>";
    echo "<h2>7. Recommendations</h2>";
    
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<h3>🔍 Investigation Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Check if Vân actually has a seller account:</strong> Look for any user with name variations of 'Vân'</li>";
    echo "<li><strong>Verify session integrity:</strong> Ensure the session belongs to the right user</li>";
    echo "<li><strong>Check for account hijacking:</strong> Verify if someone else is using Vân's account</li>";
    echo "<li><strong>Review authentication flow:</strong> Check if there are bypass conditions in the code</li>";
    echo "<li><strong>Check for admin privileges:</strong> See if Vân has admin access that shows all interfaces</li>";
    echo "</ol>";
    
    echo "<h3>🛠 Immediate Actions:</h3>";
    echo "<ul>";
    echo "<li>If Vân should NOT be a seller: Change their user_type to 'buyer' in the database</li>";
    echo "<li>If this is a session issue: Clear Vân's session and have them log in again</li>";
    echo "<li>If this is a code issue: Add stricter user_type validation</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>Database Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

// Add JavaScript for easy actions
echo "<script>
function fixUserType(userId, newType) {
    if (confirm('Are you sure you want to change user type to ' + newType + '?')) {
        // This would need to be implemented as a separate endpoint
        alert('Please implement the user type change endpoint');
    }
}
</script>";

echo "<br><p><em>Debug completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
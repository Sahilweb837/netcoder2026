 <?php
 
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'qeduwdgcmu');
define('DB_PASS', 'Eq8PnEeNqT');
define('DB_NAME', 'qeduwdgcmu');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>

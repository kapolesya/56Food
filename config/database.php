<?php
// Centralized database connection used across the application.
// Loads credentials and exposes a mysqli `$conn` variable.

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = '56food';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$conn) {
    // Do not expose DB details in production
    http_response_code(500);
    error_log('Database connection failed: ' . mysqli_connect_error());
    exit('Database connection failed.');
}

// Use utf8mb4 for full Unicode support
mysqli_set_charset($conn, 'utf8mb4');

// Optional: enable exceptions during development
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>

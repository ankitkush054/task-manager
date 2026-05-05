<?php
// Start session (optional but useful)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "task_manager";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset (important for UTF-8 support)
$conn->set_charset("utf8mb4");

// Optional: error reporting (for development only)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
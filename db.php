<?php
// ========================================================
// db.php - Database Configuration for MySQL Workbench / phpMyAdmin
// Sumanth College Hostel Management System
// ========================================================

$host     = "127.0.0.1"; // or "localhost"
$port     = 3306;        // Standard MySQL / Workbench Port
$user     = "root";      // MySQL username
$password = "root@123";          // MySQL password (enter your password if set in MySQL Workbench)
$dbname   = "sumanth_hostel_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    $db_connected = false;
    $db_error_message = $conn->connect_error;
} else {
    $db_connected = true;
    $db_error_message = "";
    $conn->set_charset("utf8mb4");
}
?>

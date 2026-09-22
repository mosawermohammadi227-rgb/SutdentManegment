<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'student_management';

// Connect to MySQL without selecting a database first.
$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die('MySQL connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// Create the database automatically if it does not exist.
$createDatabase = "CREATE DATABASE IF NOT EXISTS `$database`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci";

if (!$conn->query($createDatabase)) {
    die('Database creation failed: ' . $conn->error);
}

// Select the application database.
if (!$conn->select_db($database)) {
    die('Database selection failed: ' . $conn->error);
}

$conn->set_charset('utf8mb4');
?>

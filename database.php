<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'student_management';

try {
    // Connect to MySQL server first. Do not select a database yet.
    $conn = new mysqli($host, $user, $password);
    $conn->set_charset('utf8mb4');

    // Create the database automatically when it does not exist.
    $conn->query("
        CREATE DATABASE IF NOT EXISTS `$database`
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci
    ");

    $conn->select_db($database);

    // Create all required tables automatically.
    $conn->query("
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(50) NOT NULL UNIQUE,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            father_name VARCHAR(100) NOT NULL,
            gender ENUM('Male','Female') NOT NULL DEFAULT 'Male',
            phone VARCHAR(30) DEFAULT NULL,
            address VARCHAR(255) DEFAULT NULL,
            class_name VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            attendance_date DATE NOT NULL,
            status ENUM('Present','Absent') NOT NULL,
            UNIQUE KEY unique_attendance (student_id, attendance_date),
            CONSTRAINT fk_attendance_student
                FOREIGN KEY (student_id) REFERENCES students(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            subject VARCHAR(100) NOT NULL,
            marks DECIMAL(5,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_results_student
                FOREIGN KEY (student_id) REFERENCES students(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");

    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Database error: ' . htmlspecialchars($e->getMessage()));
}
?>

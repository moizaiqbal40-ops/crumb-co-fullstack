<?php
/**
 * Database connection (MySQL, for XAMPP's default MySQL setup).
 *
 * Default XAMPP credentials: user "root", empty password, host "localhost".
 * If you changed your MySQL root password, update it below.
 */

$DB_HOST = "localhost";
$DB_NAME = "crumb_co";
$DB_USER = "root";
$DB_PASS = "";

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed. Make sure MySQL is running in XAMPP and the 'crumb_co' database has been imported. (" . $e->getMessage() . ")");
}

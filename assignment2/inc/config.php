<?php
// inc/config.php
// Database connection settings
$host   = 'localhost';
$dbName = 'assignment2';
$user   = 'root';         // or your MySQL username
$pass   = '';             // or your MySQL password

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ]
  );
} catch (PDOException $e) {
  // If connection fails, stop and show the error
  die("Database Connection Failed: " . $e->getMessage());
}

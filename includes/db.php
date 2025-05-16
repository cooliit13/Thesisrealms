<?php

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "thesis_db"; // updated from "crud" to your actual DB name

// MySQLi connection (if needed elsewhere)
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check MySQLi connection
if ($conn->connect_error) {
    die("MySQLi Connection Failed: " . $conn->connect_error);
}

// PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbName;charset=utf8mb4", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "DATABASE CONNECTION SUCCESSFUL"; // Optional debug message
} catch (PDOException $e) {
    die("PDO Connection Failed: " . $e->getMessage());
}
?>

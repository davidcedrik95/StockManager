<?php
// api/database_connect.php
$host_name = 'db5018574434.hosting-data.io';
$database = 'dbs14737411';
$user_name = 'dbu2173288';
$password = 'cc4ef!NQfm4UAFs';

try {
    $pdo = new PDO("mysql:host=$host_name;dbname=$database;charset=utf8mb4", $user_name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}
?>
<?php
session_start();


$DB_HOST = 'localhost'; // jika gunakan Docker: 'db'
$DB_USER = 'vuln';
$DB_PASS = 'vulnpass';
$DB_NAME = 'vulnapp';


try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}


function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function esc($s)
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
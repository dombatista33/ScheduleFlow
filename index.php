<?php
session_start();

// Database configuration using environment variables
$mysql_host = getenv('MYSQL_HOST');
$mysql_database = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

try {
    // Connect to MySQL database using environment variables
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_username, $mysql_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Log the error for debugging (in production, log to file)
    error_log("Database connection error: " . $e->getMessage());
    die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
}

// Route handling
$page = $_GET['page'] ?? 'home';

switch($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'services':
        include 'pages/services.php';
        break;
    case 'calendar':
        include 'pages/calendar.php';
        break;
    case 'booking':
        include 'pages/booking.php';
        break;
    case 'confirmation':
        include 'pages/confirmation.php';
        break;
    case 'admin':
        include 'admin/dashboard.php';
        break;
    default:
        include 'pages/home.php';
}
?>
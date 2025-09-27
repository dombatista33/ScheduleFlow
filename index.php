<?php
session_start();

// Database configuration
$db_host = 'terapiaebemestar.com.br';
$db_name = 'terapiae_terapia';
$db_user = 'terapiae_terapia';
$db_pass = 'Ha31038866##';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
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
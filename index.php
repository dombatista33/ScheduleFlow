<?php
session_start();

// Database configuration - fallback to local PostgreSQL if external MySQL fails
$db_host = getenv('PGHOST') ?: 'localhost';
$db_name = getenv('PGDATABASE') ?: 'postgres';
$db_user = getenv('PGUSER') ?: 'postgres';
$db_pass = getenv('PGPASSWORD') ?: '';
$db_port = getenv('PGPORT') ?: '5432';

try {
    // Try PostgreSQL connection first (for development)
    $pdo = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Fallback to external MySQL if PostgreSQL fails
    try {
        $pdo = new PDO("mysql:host=terapiaebemestar.com.br;dbname=terapiae_terapia;charset=utf8", 'terapiae_terapia', 'Ha31038866##');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e2) {
        die("Erro de conexão com o banco de dados.");
    }
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
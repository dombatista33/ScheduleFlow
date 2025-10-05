<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Connect to PostgreSQL database using Replit DATABASE_URL
    $database_url = getenv('DATABASE_URL');
    if (!$database_url) {
        throw new Exception("DATABASE_URL not found");
    }
    
    // Parse DATABASE_URL to build proper PostgreSQL DSN
    $url_parts = parse_url($database_url);
    $host = $url_parts['host'];
    $port = isset($url_parts['port']) ? $url_parts['port'] : 5432;
    $dbname = ltrim($url_parts['path'], '/');
    $user = $url_parts['user'];
    $password = $url_parts['pass'];
    
    // Build PostgreSQL DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Log the error for debugging (in production, log to file)
    error_log("Database connection error: " . $e->getMessage());
    die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
} catch(Exception $e) {
    error_log("Configuration error: " . $e->getMessage());
    die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
}
?>

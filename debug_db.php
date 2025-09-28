<?php
// Debug script to check database connection and tables

// Database configuration using environment variables
$mysql_host = getenv('MYSQL_HOST');
$mysql_database = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

echo "=== Database Connection Test ===\n";
echo "Host: $mysql_host\n";
echo "Database: $mysql_database\n";
echo "Username: $mysql_username\n\n";

try {
    // Connect to MySQL database using environment variables
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_username, $mysql_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!\n\n";
    
    // Show all tables
    echo "=== Available Tables ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $table) {
        echo "- $table\n";
    }
    
    // Check if time_slots table exists and has data
    echo "\n=== Time Slots Table Check ===\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM time_slots");
        $count = $stmt->fetchColumn();
        echo "✅ time_slots table exists with $count records\n";
        
        // Show some sample data
        echo "\n=== Sample Time Slots ===\n";
        $stmt = $pdo->query("SELECT * FROM time_slots LIMIT 5");
        $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($slots as $slot) {
            print_r($slot);
        }
    } catch(PDOException $e) {
        echo "❌ time_slots table issue: " . $e->getMessage() . "\n";
    }
    
    // Check appointments table
    echo "\n=== Appointments Table Check ===\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM appointments");
        $count = $stmt->fetchColumn();
        echo "✅ appointments table exists with $count records\n";
    } catch(PDOException $e) {
        echo "❌ appointments table issue: " . $e->getMessage() . "\n";
    }
    
} catch(PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
}
?>
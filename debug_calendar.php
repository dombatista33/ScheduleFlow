<?php
// Debug script to test calendar date selection

// Database configuration using environment variables
$mysql_host = getenv('MYSQL_HOST');
$mysql_database = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_username, $mysql_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test with a specific date
    $test_date = '2025-09-30';
    echo "=== Testing Date: $test_date ===\n\n";
    
    // Check what time slots exist for this date
    echo "1. All time slots for $test_date:\n";
    $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE date = ?");
    $stmt->execute([$test_date]);
    $all_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($all_slots) . " total time slots\n";
    foreach($all_slots as $slot) {
        echo "- {$slot['time']} (available: {$slot['is_available']})\n";
    }
    
    echo "\n2. Check appointments for $test_date:\n";
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_date = ?");
    $stmt->execute([$test_date]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($appointments) . " appointments\n";
    foreach($appointments as $apt) {
        echo "- {$apt['appointment_time']} (status: {$apt['status']})\n";
    }
    
    echo "\n3. Available time slots (using calendar.php query):\n";
    $stmt = $pdo->prepare("
        SELECT t.time 
        FROM time_slots t 
        LEFT JOIN appointments a ON t.date = a.appointment_date AND t.time = a.appointment_time AND a.status != 'cancelled'
        WHERE t.date = ? AND t.is_available = true AND a.id IS NULL
        ORDER BY t.time ASC
    ");
    $stmt->execute([$test_date]);
    $available_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Found " . count($available_times) . " available slots\n";
    foreach($available_times as $time) {
        echo "- $time\n";
    }
    
    echo "\n4. Test what the calendar page receives:\n";
    echo "URL would be: index.php?page=calendar&date=$test_date\n";
    echo "\$_GET['date'] would be: '$test_date'\n";
    echo "Date validation: " . (strtotime($test_date) ? "Valid" : "Invalid") . "\n";
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
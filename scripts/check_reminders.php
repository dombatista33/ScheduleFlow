<?php
/**
 * Verificador de Status dos Lembretes - Terapia e Bem Estar
 * Mostra quais agendamentos têm lembretes pendentes
 * 
 * Execução: php scripts/check_reminders.php
 */

// Set working directory to project root
chdir(dirname(__DIR__));

try {
    // Connect to PostgreSQL database
    $database_url = getenv('DATABASE_URL');
    if (!$database_url) {
        throw new Exception("DATABASE_URL not found");
    }
    
    $url_parts = parse_url($database_url);
    $host = $url_parts['host'];
    $port = isset($url_parts['port']) ? $url_parts['port'] : 5432;
    $dbname = ltrim($url_parts['path'], '/');
    $user = $url_parts['user'];
    $password = $url_parts['pass'];
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Status dos Lembretes ===\n";
    echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Get appointments for next 7 days
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.reminder_sent,
            c.full_name,
            c.email,
            s.name as service_name
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        JOIN services s ON a.service_id = s.id
        WHERE a.appointment_date >= CURRENT_DATE 
        AND a.appointment_date <= CURRENT_DATE + INTERVAL '7 days'
        AND a.status = 'confirmed'
        ORDER BY a.appointment_date, a.appointment_time
    ");
    
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "Nenhum agendamento encontrado para os próximos 7 dias.\n";
        exit(0);
    }
    
    $pending = 0;
    $sent = 0;
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    foreach ($appointments as $app) {
        $status = $app['reminder_sent'] ? '✅ ENVIADO' : '⏳ PENDENTE';
        $is_tomorrow = $app['appointment_date'] === $tomorrow ? ' [AMANHÃ]' : '';
        
        echo sprintf(
            "ID: %d | %s %s | %s | %s | %s%s\n",
            $app['id'],
            $app['appointment_date'],
            $app['appointment_time'],
            $app['full_name'],
            $app['service_name'],
            $status,
            $is_tomorrow
        );
        
        if ($app['reminder_sent']) {
            $sent++;
        } else {
            $pending++;
        }
    }
    
    echo "\n=== Resumo ===\n";
    echo "Total de agendamentos: " . count($appointments) . "\n";
    echo "Lembretes enviados: $sent\n";
    echo "Lembretes pendentes: $pending\n";
    
    if ($pending > 0) {
        echo "\n💡 Para enviar lembretes pendentes, execute:\n";
        echo "   php scripts/send_reminders.php\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
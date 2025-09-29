<?php
/**
 * Sistema Automático de Lembretes - Terapia e Bem Estar
 * Envia lembretes por email 24h antes das consultas
 * 
 * Execução: php scripts/send_reminders.php
 */

// Set working directory to project root
chdir(dirname(__DIR__));

// Include required files
require_once 'includes/email_system.php';

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
    
    echo "=== Sistema de Lembretes Automáticos ===\n";
    echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Calculate tomorrow's date
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    echo "Verificando agendamentos para: $tomorrow\n";
    
    // Get appointments for tomorrow that haven't received reminder yet
    $stmt = $pdo->prepare("
        SELECT 
            a.id as appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.virtual_room_link,
            a.reminder_sent,
            c.full_name,
            c.email,
            c.whatsapp,
            s.name as service_name,
            s.duration,
            s.price
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        JOIN services s ON a.service_id = s.id
        WHERE a.appointment_date = ? 
        AND a.status = 'confirmed'
        AND (a.reminder_sent IS FALSE OR a.reminder_sent IS NULL)
        ORDER BY a.appointment_time
    ");
    
    $stmt->execute([$tomorrow]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "Nenhum agendamento encontrado para $tomorrow ou todos os lembretes já foram enviados.\n";
        exit(0);
    }
    
    echo "Encontrados " . count($appointments) . " agendamento(s) para enviar lembretes:\n\n";
    
    // Initialize email system
    $email_system = new EmailSystem();
    $sent_count = 0;
    $failed_count = 0;
    
    foreach ($appointments as $appointment) {
        echo "Processando agendamento #{$appointment['appointment_id']}:\n";
        echo "  Cliente: {$appointment['full_name']}\n";
        echo "  Email: {$appointment['email']}\n";
        echo "  Horário: {$appointment['appointment_time']}\n";
        echo "  Serviço: {$appointment['service_name']}\n";
        
        try {
            // Prepare reminder data
            $reminder_data = [
                'full_name' => $appointment['full_name'],
                'email' => $appointment['email'],
                'service_name' => $appointment['service_name'],
                'appointment_date' => $appointment['appointment_date'],
                'appointment_time' => $appointment['appointment_time'],
                'duration' => $appointment['duration'],
                'price' => $appointment['price'],
                'virtual_room_link' => $appointment['virtual_room_link']
            ];
            
            // Send reminder email
            $email_sent = $email_system->sendAppointmentReminder($reminder_data);
            
            if ($email_sent) {
                // Mark reminder as sent
                $update_stmt = $pdo->prepare("
                    UPDATE appointments 
                    SET reminder_sent = TRUE 
                    WHERE id = ?
                ");
                $update_stmt->execute([$appointment['appointment_id']]);
                
                echo "  ✅ Lembrete enviado com sucesso!\n";
                $sent_count++;
            } else {
                echo "  ❌ Falha ao enviar lembrete\n";
                $failed_count++;
            }
            
        } catch (Exception $e) {
            echo "  ❌ Erro: " . $e->getMessage() . "\n";
            $failed_count++;
        }
        
        echo "\n";
    }
    
    // Summary
    echo "=== Resumo da Execução ===\n";
    echo "Lembretes enviados com sucesso: $sent_count\n";
    echo "Falhas no envio: $failed_count\n";
    echo "Total processado: " . count($appointments) . "\n";
    echo "Execução finalizada em: " . date('Y-m-d H:i:s') . "\n";
    
    // Log summary to file
    $log_entry = date('Y-m-d H:i:s') . " | REMINDER_BATCH | SUCCESS: $sent_count | FAILED: $failed_count | DATE: $tomorrow\n";
    
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents('logs/reminders_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    
} catch (Exception $e) {
    echo "❌ Erro crítico: " . $e->getMessage() . "\n";
    
    // Log critical error
    $error_entry = date('Y-m-d H:i:s') . " | REMINDER_ERROR | " . $e->getMessage() . "\n";
    
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents('logs/reminders_log.txt', $error_entry, FILE_APPEND | LOCK_EX);
    exit(1);
}
?>
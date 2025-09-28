<?php
/**
 * Sistema de E-mail para Confirmação de Agendamentos
 * Terapia e Bem Estar - Dra. Daniela Lima
 */

class EmailSystem {
    private $from_email = 'contato@terapiaebemestar.com.br';
    private $from_name = 'Terapia e Bem Estar - Dra. Daniela Lima';
    private $reply_to = 'contato@terapiaebemestar.com.br';
    
    /**
     * Envia e-mail de confirmação de agendamento
     */
    public function sendAppointmentConfirmation($appointment_data) {
        $to = $appointment_data['email'];
        $subject = 'Confirmação de Agendamento - Terapia e Bem Estar';
        
        // Generate email content
        $message = $this->generateConfirmationEmailTemplate($appointment_data);
        
        // Email headers
        $headers = $this->getEmailHeaders();
        
        // Send email
        $sent = mail($to, $subject, $message, $headers);
        
        // Log email attempt
        $this->logEmailAttempt($to, $subject, $sent);
        
        return $sent;
    }
    
    /**
     * Gera template HTML para e-mail de confirmação
     */
    private function generateConfirmationEmailTemplate($data) {
        $date_formatted = date('d/m/Y', strtotime($data['appointment_date']));
        $time_formatted = date('H:i', strtotime($data['appointment_time']));
        $price_formatted = number_format($data['price'], 2, ',', '.');
        
        $template = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Agendamento</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f7f5; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #8b9a8b, #a8c8ec); color: white; padding: 2rem; text-align: center; }
        .header h1 { margin: 0; font-size: 1.8rem; }
        .content { padding: 2rem; }
        .appointment-card { background: #f8faf8; border-left: 4px solid #8b9a8b; padding: 1.5rem; margin: 1rem 0; border-radius: 5px; }
        .appointment-card h3 { margin: 0 0 1rem 0; color: #8b9a8b; }
        .detail-row { margin: 0.5rem 0; }
        .detail-row strong { color: #555; }
        .steps { margin: 2rem 0; }
        .step { background: #fff; border: 1px solid #e0e6e0; padding: 1rem; margin: 0.5rem 0; border-radius: 5px; }
        .step h4 { margin: 0 0 0.5rem 0; color: #8b9a8b; }
        .footer { background: #f0f2f0; padding: 1.5rem; text-align: center; color: #666; font-size: 0.9rem; }
        .contact-info { background: #e8f2e8; padding: 1rem; border-radius: 5px; margin: 1rem 0; }
        .whatsapp-link { display: inline-block; background: #25D366; color: white; padding: 0.8rem 1.5rem; text-decoration: none; border-radius: 25px; margin: 1rem 0; }
        .important { background: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; margin: 1rem 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Agendamento Confirmado!</h1>
            <p>Terapia e Bem Estar - Dra. Daniela Lima</p>
        </div>
        
        <div class="content">
            <p>Olá <strong>' . htmlspecialchars($data['full_name']) . '</strong>,</p>
            <p>Seu agendamento foi realizado com sucesso! Confira abaixo os detalhes:</p>
            
            <div class="appointment-card">
                <h3>Detalhes do Agendamento</h3>
                <div class="detail-row"><strong>Paciente:</strong> ' . htmlspecialchars($data['full_name']) . '</div>
                <div class="detail-row"><strong>Serviço:</strong> ' . htmlspecialchars($data['service_name']) . '</div>
                <div class="detail-row"><strong>Data:</strong> ' . $date_formatted . '</div>
                <div class="detail-row"><strong>Horário:</strong> ' . $time_formatted . '</div>
                <div class="detail-row"><strong>Duração:</strong> ' . $data['duration'] . ' minutos</div>
                <div class="detail-row"><strong>Valor:</strong> R$ ' . $price_formatted . '</div>
                <div class="detail-row"><strong>Status:</strong> Aguardando Pagamento</div>
            </div>
            
            <div class="steps">
                <h3>Próximos Passos:</h3>
                
                <div class="step">
                    <h4>1. Pagamento</h4>
                    <p>Você receberá as informações de pagamento via WhatsApp nos próximos minutos. O pagamento deve ser realizado até 24 horas antes da consulta.</p>
                </div>
                
                <div class="step">
                    <h4>2. Confirmação</h4>
                    <p>Após o pagamento confirmado, você receberá o link da sala virtual Google Meet para sua consulta.</p>
                </div>
                
                <div class="step">
                    <h4>3. Lembrete</h4>
                    <p>Enviaremos um lembrete 24 horas antes da consulta com todas as informações necessárias.</p>
                </div>
            </div>
            
            <div class="contact-info">
                <h4>Contatos para Dúvidas:</h4>
                <p><strong>WhatsApp:</strong> (11) 99999-9999</p>
                <p><strong>E-mail:</strong> contato@terapiaebemestar.com.br</p>
                <a href="https://wa.me/5511999999999" class="whatsapp-link">💬 Falar no WhatsApp</a>
            </div>
            
            <div class="important">
                <h4>⚠️ Informações Importantes:</h4>
                <ul>
                    <li>Cancelamentos devem ser feitos com pelo menos 24 horas de antecedência</li>
                    <li>Teste sua câmera e microfone antes da consulta</li>
                    <li>Certifique-se de ter uma conexão estável com a internet</li>
                    <li>Mantenha seu WhatsApp ativo para receber atualizações</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Dra. Daniela Lima</strong><br>
            Psicóloga Clínica CRP 00000/00<br>
            Especialista em Terapia Cognitiva Comportamental</p>
            <p>© 2024 Terapia e Bem Estar - Todos os direitos reservados</p>
        </div>
    </div>
</body>
</html>';
        
        return $template;
    }
    
    /**
     * Gera headers para e-mail HTML
     */
    private function getEmailHeaders() {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";
        $headers .= "Reply-To: {$this->reply_to}" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return $headers;
    }
    
    /**
     * Log attempts de envio de e-mail
     */
    private function logEmailAttempt($to, $subject, $success) {
        $log_entry = date('Y-m-d H:i:s') . " | " . 
                    ($success ? "SUCCESS" : "FAILED") . " | " .
                    "TO: {$to} | SUBJECT: {$subject}" . "\n";
        
        file_put_contents('logs/email_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Envia e-mail de lembrete 24h antes da consulta
     */
    public function sendAppointmentReminder($appointment_data) {
        $to = $appointment_data['email'];
        $subject = 'Lembrete: Sua consulta é amanhã - Terapia e Bem Estar';
        
        $message = $this->generateReminderEmailTemplate($appointment_data);
        $headers = $this->getEmailHeaders();
        
        $sent = mail($to, $subject, $message, $headers);
        $this->logEmailAttempt($to, $subject, $sent);
        
        return $sent;
    }
    
    /**
     * Template para e-mail de lembrete
     */
    private function generateReminderEmailTemplate($data) {
        $date_formatted = date('d/m/Y', strtotime($data['appointment_date']));
        $time_formatted = date('H:i', strtotime($data['appointment_time']));
        
        $template = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f7f5; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #8b9a8b, #a8c8ec); color: white; padding: 2rem; text-align: center; }
        .content { padding: 2rem; }
        .reminder-card { background: #fff3cd; border-left: 4px solid #ffc107; padding: 1.5rem; margin: 1rem 0; border-radius: 5px; }
        .meeting-info { background: #e8f2e8; padding: 1rem; border-radius: 5px; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Lembrete: Sua consulta é amanhã!</h1>
        </div>
        
        <div class="content">
            <p>Olá <strong>' . htmlspecialchars($data['full_name']) . '</strong>,</p>
            
            <div class="reminder-card">
                <h3>Sua consulta será amanhã:</h3>
                <p><strong>Data:</strong> ' . $date_formatted . '</p>
                <p><strong>Horário:</strong> ' . $time_formatted . '</p>
                <p><strong>Serviço:</strong> ' . htmlspecialchars($data['service_name']) . '</p>
            </div>
            
            <div class="meeting-info">
                <h4>📱 Link da Sala Virtual:</h4>
                <p><a href="' . htmlspecialchars($data['virtual_room_link']) . '" style="color: #8b9a8b; font-weight: bold;">Clique aqui para entrar na consulta</a></p>
            </div>
            
            <p><strong>Dicas importantes:</strong></p>
            <ul>
                <li>Entre na sala 5 minutos antes do horário</li>
                <li>Teste sua câmera e microfone</li>
                <li>Escolha um ambiente tranquilo e privado</li>
                <li>Tenha um copo de água próximo</li>
            </ul>
        </div>
    </div>
</body>
</html>';
        
        return $template;
    }
}
?>
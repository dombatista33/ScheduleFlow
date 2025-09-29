<?php
// Referenced from replitmail integration - adapted for PHP
// Original integration: blueprint:replitmail

/**
 * ReplitEmail - Sistema de envio de email usando a API Replit
 * Adaptado da integração ReplitMail para PHP
 */
class ReplitEmail {
    private $auth_token;
    
    public function __construct() {
        $this->auth_token = $this->getAuthToken();
    }
    
    /**
     * Obtém o token de autenticação do Replit
     */
    private function getAuthToken() {
        $repl_identity = getenv('REPL_IDENTITY');
        $web_repl_renewal = getenv('WEB_REPL_RENEWAL');
        
        if ($repl_identity) {
            return $repl_identity;
        } elseif ($web_repl_renewal) {
            return $web_repl_renewal;
        } else {
            throw new Exception('No authentication token found. Please ensure you\'re running in Replit environment.');
        }
    }
    
    /**
     * Envia email usando a API Replit
     * @param array $message Array com to, subject, html, text
     * @return array Resposta da API
     */
    public function sendEmail($message) {
        $url = 'https://connectors.replit.com/api/v2/mailer/send';
        
        $data = json_encode([
            'to' => $message['to'],
            'subject' => $message['subject'],
            'html' => $message['html'] ?? null,
            'text' => $message['text'] ?? null,
            'cc' => $message['cc'] ?? null,
            'attachments' => $message['attachments'] ?? null
        ]);
        
        $options = [
            'http' => [
                'header' => [
                    'Content-Type: application/json',
                    'X-Replit-Token: ' . $this->auth_token
                ],
                'method' => 'POST',
                'content' => $data,
                'timeout' => 30
            ]
        ];
        
        $context = stream_context_create($options);
        
        // Suprimir warnings para evitar "headers already sent"
        $result = @file_get_contents($url, false, $context);
        
        if ($result === FALSE) {
            // Verificar se houve erro HTTP específico
            $error = error_get_last();
            if ($error && strpos($error['message'], '403 Forbidden') !== false) {
                throw new Exception('Authentication failed: Replit email service access denied');
            }
            throw new Exception('Failed to send email via Replit API');
        }
        
        $response = json_decode($result, true);
        
        if (!$response) {
            throw new Exception('Invalid response from Replit API');
        }
        
        return $response;
    }
    
    /**
     * Envia email de confirmação de agendamento
     */
    public function sendAppointmentConfirmation($appointment_data) {
        try {
            $to = $appointment_data['email'];
            $subject = 'Confirmação de Agendamento - Terapia e Bem Estar';
            
            // Usar o template HTML existente
            $html_content = $this->generateConfirmationEmailTemplate($appointment_data);
            
            // Versão texto simples
            $text_content = $this->generateTextConfirmation($appointment_data);
            
            $message = [
                'to' => $to,
                'subject' => $subject,
                'html' => $html_content,
                'text' => $text_content
            ];
            
            $result = $this->sendEmail($message);
            
            // Log do resultado
            $this->logEmailAttempt($to, $subject, true);
            
            return true;
            
        } catch (Exception $e) {
            // Log silencioso - sem output para não quebrar headers
            error_log('ReplitEmail error: ' . $e->getMessage());
            $this->logEmailAttempt($appointment_data['email'], $subject ?? 'Confirmação de Agendamento', false);
            return false;
        }
    }
    
    /**
     * Gera template HTML para confirmação (reusando template existente)
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
                <div class="detail-row"><strong>Status:</strong> Confirmado</div>
            </div>
            
            <div class="steps">
                <h3>Próximos Passos:</h3>
                
                <div class="step">
                    <h4>1. Link da Consulta</h4>
                    <p>Você receberá o link da sala virtual Google Meet 24 horas antes da consulta via WhatsApp e email.</p>
                </div>
                
                <div class="step">
                    <h4>2. Lembrete</h4>
                    <p>Enviaremos um lembrete com todas as informações necessárias para sua consulta.</p>
                </div>
                
                <div class="step">
                    <h4>3. Preparação</h4>
                    <p>Certifique-se de ter uma conexão estável e um ambiente tranquilo para a consulta.</p>
                </div>
            </div>
            
            <div class="contact-info">
                <h4>Contatos para Dúvidas:</h4>
                <p><strong>WhatsApp:</strong> (11) 99999-9999</p>
                <p><strong>E-mail:</strong> contato@terapiaebemestar.com.br</p>
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
     * Gera versão texto do email
     */
    private function generateTextConfirmation($data) {
        $date_formatted = date('d/m/Y', strtotime($data['appointment_date']));
        $time_formatted = date('H:i', strtotime($data['appointment_time']));
        $price_formatted = number_format($data['price'], 2, ',', '.');
        
        return "AGENDAMENTO CONFIRMADO - Terapia e Bem Estar

Olá " . $data['full_name'] . ",

Seu agendamento foi realizado com sucesso!

DETALHES DO AGENDAMENTO:
- Paciente: " . $data['full_name'] . "
- Serviço: " . $data['service_name'] . "
- Data: " . $date_formatted . "
- Horário: " . $time_formatted . "
- Duração: " . $data['duration'] . " minutos
- Valor: R$ " . $price_formatted . "
- Status: Confirmado

PRÓXIMOS PASSOS:
1. Link da Consulta: Você receberá o link 24 horas antes da consulta
2. Lembrete: Enviaremos todas as informações necessárias
3. Preparação: Certifique-se de ter conexão estável

CONTATO:
WhatsApp: (11) 99999-9999
E-mail: contato@terapiaebemestar.com.br

Dra. Daniela Lima
Psicóloga Clínica CRP 00000/00
© 2024 Terapia e Bem Estar";
    }
    
    /**
     * Log das tentativas de email
     */
    private function logEmailAttempt($to, $subject, $success) {
        $log_entry = date('Y-m-d H:i:s') . " | " . 
                    ($success ? "SUCCESS" : "FAILED") . " | " .
                    "ReplitEmail | TO: {$to} | SUBJECT: {$subject}" . "\n";
        
        // Create logs directory if it doesn't exist
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        
        file_put_contents('logs/email_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    }
}
?>
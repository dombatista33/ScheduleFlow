<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Agendamento - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Terapia e Bem Estar</a>
                <nav class="nav">
                    <a href="index.php">Início</a>
                    <a href="index.php?page=services">Serviços</a>
                    <a href="index.php?page=calendar" class="active">Agendar</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php
            $service_id = $_GET['service_id'] ?? null;
            $selected_date = $_GET['date'] ?? null;
            $selected_time = $_GET['time'] ?? null;
            $error = '';
            $success = '';
            
            // Validate required parameters
            if (!$service_id || !$selected_date || !$selected_time) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            // Get service information
            try {
                $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
                $stmt->execute([$service_id]);
                $service = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$service) {
                    header('Location: index.php?page=calendar');
                    exit;
                }
            } catch(PDOException $e) {
                $error = "Erro ao carregar informações do serviço.";
            }
            
            // Include email system
            require_once 'includes/email_system.php';
            
            // Process form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
                $full_name = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
                $notes = trim($_POST['notes'] ?? '');
                
                // Validation
                if (empty($full_name)) {
                    $error = "Por favor, informe seu nome completo.";
                } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Por favor, informe um e-mail válido.";
                } elseif (empty($whatsapp)) {
                    $error = "Por favor, informe seu número do WhatsApp.";
                } else {
                    // Check if time slot is still available
                    try {
                        $stmt = $pdo->prepare("
                            SELECT COUNT(*) FROM appointments 
                            WHERE appointment_date = ? AND appointment_time = ? AND status != 'cancelled'
                        ");
                        $stmt->execute([$selected_date, $selected_time]);
                        $count = $stmt->fetchColumn();
                        
                        if ($count > 0) {
                            $error = "Este horário já foi agendado. Por favor, escolha outro horário.";
                        } else {
                            // Create or get client
                            $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
                            $stmt->execute([$email]);
                            $client_id = $stmt->fetchColumn();
                            
                            if (!$client_id) {
                                $stmt = $pdo->prepare("INSERT INTO clients (full_name, email, whatsapp) VALUES (?, ?, ?)");
                                $stmt->execute([$full_name, $email, $whatsapp]);
                                $client_id = $pdo->lastInsertId();
                            } else {
                                // Update client information
                                $stmt = $pdo->prepare("UPDATE clients SET full_name = ?, whatsapp = ? WHERE id = ?");
                                $stmt->execute([$full_name, $whatsapp, $client_id]);
                            }
                            
                            // Create appointment
                            $virtual_room_link = "https://meet.google.com/new"; // This would be dynamically generated
                            $stmt = $pdo->prepare("
                                INSERT INTO appointments (client_id, service_id, appointment_date, appointment_time, status, payment_method, virtual_room_link, notes) 
                                VALUES (?, ?, ?, ?, 'pending', 'Pix/Transferência', ?, ?)
                            ");
                            $stmt->execute([$client_id, $service_id, $selected_date, $selected_time, $virtual_room_link, $notes]);
                            $appointment_id = $pdo->lastInsertId();
                            
                            // Send confirmation email
                            $email_sent = false;
                            try {
                                $email_system = new EmailSystem();
                                $email_data = [
                                    'full_name' => $full_name,
                                    'email' => $email,
                                    'service_name' => $service['name'],
                                    'appointment_date' => $selected_date,
                                    'appointment_time' => $selected_time,
                                    'duration' => $service['duration'],
                                    'price' => $service['price'],
                                    'virtual_room_link' => $virtual_room_link
                                ];
                                
                                $email_sent = $email_system->sendAppointmentConfirmation($email_data);
                                
                                // Log email sending result
                                if (!$email_sent) {
                                    error_log("Failed to send confirmation email to: " . $email);
                                }
                            } catch (Exception $e) {
                                error_log("Email system error: " . $e->getMessage());
                                $email_sent = false;
                            }
                            
                            // Store email status in session for confirmation page
                            $_SESSION['email_sent'] = $email_sent;
                            $_SESSION['email_address'] = $email;
                            
                            // Redirect to confirmation page
                            header("Location: index.php?page=confirmation&appointment_id=$appointment_id");
                            exit;
                        }
                    } catch(PDOException $e) {
                        $error = "Erro ao processar agendamento. Tente novamente.";
                    }
                }
            }
            ?>

            <section class="hero">
                <h1>Finalizar Agendamento</h1>
                <p class="subtitle">Preencha seus dados para confirmar o agendamento</p>
            </section>

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Appointment Summary -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Resumo do Agendamento</h2>
                    <?php if (isset($service)): ?>
                        <div style="background: rgba(139, 154, 139, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1rem;">
                            <p><strong>Serviço:</strong> <?= htmlspecialchars($service['name']) ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($selected_date)) ?></p>
                            <p><strong>Horário:</strong> <?= date('H:i', strtotime($selected_time)) ?></p>
                            <p><strong>Duração:</strong> <?= $service['duration'] ?> minutos</p>
                            <p><strong>Valor:</strong> R$ <?= number_format($service['price'], 2, ',', '.') ?></p>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Booking Form -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Seus Dados</h2>
                    
                    <?php if ($error): ?>
                        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="full_name">Nome Completo *</label>
                            <input type="text" id="full_name" name="full_name" required 
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                                   placeholder="Digite seu nome completo">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="Digite seu e-mail">
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp">WhatsApp (com DDD) *</label>
                            <input type="tel" id="whatsapp" name="whatsapp" required 
                                   value="<?= htmlspecialchars($_POST['whatsapp'] ?? '') ?>"
                                   placeholder="(11) 99999-9999">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Observações (opcional)</label>
                            <textarea id="notes" name="notes" rows="4" 
                                      placeholder="Descreva brevemente o motivo da consulta ou alguma informação importante"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                        </div>
                        
                        <div style="text-align: center; margin-top: 2rem;">
                            <button type="submit" class="btn btn-large">Confirmar Agendamento</button>
                        </div>
                    </form>
                </section>

                <!-- Payment Information -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Informações de Pagamento</h2>
                    <p>O pagamento pode ser realizado via:</p>
                    <ul style="margin-left: 2rem; line-height: 1.8;">
                        <li><strong>Pix:</strong> Chave será enviada por WhatsApp após confirmação</li>
                        <li><strong>Transferência bancária:</strong> Dados bancários serão fornecidos</li>
                        <li><strong>Cartão de crédito:</strong> Link de pagamento será enviado</li>
                    </ul>
                    <p style="margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                        <strong>Importante:</strong> O pagamento deve ser realizado até 24 horas antes da consulta para garantir a confirmação do agendamento.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Terapia e Bem Estar - Dra. Daniela Lima. Todos os direitos reservados.</p>
            <p>CRP 00000/00 | Atendimento psicológico online</p>
        </div>
    </footer>

    <script>
        // Format WhatsApp field
        document.getElementById('whatsapp').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                }
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
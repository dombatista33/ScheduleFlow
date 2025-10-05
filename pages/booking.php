<?php
global $pdo;
            $selected_date = $_GET['date'] ?? null;
            $selected_time = $_GET['time'] ?? null;
            $error = '';
            $success = '';
            
            // Validate required parameters
            if (!$selected_date || !$selected_time) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            // Server-side validation of date format and availability
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $selected_time)) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            // Verify date is not in the past and not weekend
            $selected_datetime = DateTime::createFromFormat('Y-m-d', $selected_date);
            $today = new DateTime('today');
            $dayOfWeek = $selected_datetime->format('w'); // 0=Sunday, 6=Saturday
            
            if ($selected_datetime < $today || $dayOfWeek == 0 || $dayOfWeek == 6) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            // Verify time slot exists and is available
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM time_slots WHERE date = ? AND time = ? AND is_available = true");
                $stmt->execute([$selected_date, $selected_time]);
                $slot_exists = $stmt->fetchColumn();
                
                if (!$slot_exists) {
                    header('Location: index.php?page=calendar');
                    exit;
                }
            } catch(PDOException $e) {
                header('Location: index.php?page=calendar');
                exit;
            }
            
            // Get all services for selection
            $services = [];
            try {
                $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                $error = "Erro ao carregar informações dos serviços.";
            }
            
            // Include email system
            require_once 'includes/email_system.php';
            require_once 'includes/replit_email.php';
            
            // Process form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
                $service_id = $_POST['service_id'] ?? null;
                $full_name = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
                $notes = trim($_POST['notes'] ?? '');
                
                // Get selected service information
                $service = null;
                if ($service_id) {
                    foreach ($services as $svc) {
                        if ($svc['id'] == $service_id) {
                            $service = $svc;
                            break;
                        }
                    }
                }
                
                // Validation
                if (empty($service_id) || !$service) {
                    $error = "Por favor, selecione um serviço.";
                } elseif (empty($full_name)) {
                    $error = "Por favor, informe seu nome completo.";
                } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Por favor, informe um e-mail válido.";
                } elseif (empty($whatsapp)) {
                    $error = "Por favor, informe seu número do WhatsApp.";
                } else {
                    // Use transaction to prevent race condition
                    try {
                        $pdo->beginTransaction();
                        
                        // Lock the specific time slot and verify availability
                        $stmt = $pdo->prepare("
                            SELECT id, is_available FROM time_slots 
                            WHERE date = ? AND time = ? 
                            FOR UPDATE
                        ");
                        $stmt->execute([$selected_date, $selected_time]);
                        $slot = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$slot || !$slot['is_available']) {
                            $pdo->rollback();
                            $error = "Este horário não está mais disponível. Por favor, escolha outro horário.";
                        } else {
                            // Check if appointment already exists
                            $stmt = $pdo->prepare("
                                SELECT COUNT(*) FROM appointments 
                                WHERE appointment_date = ? AND appointment_time = ? AND status != 'cancelled'
                            ");
                            $stmt->execute([$selected_date, $selected_time]);
                            $count = $stmt->fetchColumn();
                            
                            if ($count > 0) {
                                $pdo->rollback();
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
                                VALUES (?, ?, ?, ?, 'confirmed', NULL, ?, ?)
                            ");
                            $stmt->execute([$client_id, $service_id, $selected_date, $selected_time, $virtual_room_link, $notes]);
                            $appointment_id = $pdo->lastInsertId();
                            
                            $pdo->commit();
                            
                            // Send confirmation email
                            $email_sent = false;
                            try {
                                // Try ReplitEmail first
                                $replit_email = new ReplitEmail();
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
                                
                                $email_sent = $replit_email->sendAppointmentConfirmation($email_data);
                                
                                // Fallback to regular system if ReplitEmail fails
                                if (!$email_sent) {
                                    error_log("ReplitEmail failed, trying fallback system");
                                    try {
                                        $email_system = new EmailSystem();
                                        $email_sent = $email_system->sendAppointmentConfirmation($email_data);
                                    } catch (Exception $e) {
                                        error_log("Fallback email system also failed: " . $e->getMessage());
                                        $email_sent = false;
                                    }
                                }
                                
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
                        }
                    } catch(PDOException $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollback();
                        }
                        $error = "Erro ao processar agendamento. Tente novamente.";
                    }
                }
            }
?>
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
                    <a href="index.php?page=google_meet_tutorial">Primeira Consulta</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="hero">
                <h1>Finalizar Agendamento</h1>
                <p class="subtitle">Selecione o serviço e preencha seus dados para confirmar o agendamento</p>
            </section>

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Appointment Summary -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Resumo do Agendamento</h2>
                    <div style="background: rgba(139, 154, 139, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1rem;">
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($selected_date)) ?></p>
                        <p><strong>Horário:</strong> <?= date('H:i', strtotime($selected_time)) ?></p>
                        <p><em>Selecione um serviço abaixo para ver duração e valor</em></p>
                    </div>
                </section>

                <!-- Booking Form -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Dados do Agendamento</h2>
                    
                    <?php if ($error): ?>
                        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="service_id">Escolha o Serviço *</label>
                            <select id="service_id" name="service_id" required onchange="updateServiceInfo()">
                                <option value="">Selecione um serviço</option>
                                <?php foreach($services as $svc): ?>
                                    <option value="<?= $svc['id'] ?>" 
                                            data-duration="<?= $svc['duration'] ?>" 
                                            data-price="<?= number_format($svc['price'], 2, ',', '.') ?>"
                                            data-description="<?= htmlspecialchars($svc['description']) ?>"
                                            <?= ($_POST['service_id'] ?? '') == $svc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($svc['name']) ?> - R$ <?= number_format($svc['price'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="service-info" style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(139, 154, 139, 0.05); border-radius: 5px; display: none;">
                                <p id="service-description" style="margin: 0; font-size: 0.9rem; color: var(--text-light);"></p>
                                <p id="service-details" style="margin: 0.5rem 0 0 0; font-size: 0.9rem;"><strong>Duração:</strong> <span id="service-duration"></span> min | <strong>Valor:</strong> R$ <span id="service-price"></span></p>
                            </div>
                        </div>
                        
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
        
        // Update service information when selection changes
        function updateServiceInfo() {
            const select = document.getElementById('service_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('service-info');
            const descriptionEl = document.getElementById('service-description');
            const durationEl = document.getElementById('service-duration');
            const priceEl = document.getElementById('service-price');
            
            if (option.value) {
                descriptionEl.textContent = option.dataset.description;
                durationEl.textContent = option.dataset.duration;
                priceEl.textContent = option.dataset.price;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }
        
        // Initialize service info if option is already selected
        document.addEventListener('DOMContentLoaded', function() {
            updateServiceInfo();
        });
    </script>
</body>
</html>
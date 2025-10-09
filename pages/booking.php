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
            <!-- Progress Indicator -->
            <div style="max-width: 800px; margin: 2rem auto 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
                    <div style="position: absolute; top: 50%; left: 0; right: 0; height: 2px; background: var(--success-color); z-index: 0;"></div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">✓</div>
                        <span style="font-size: 0.85rem; color: var(--success-color);">Horário</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">2</div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary-color);">Seus Dados</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">3</div>
                        <span style="font-size: 0.85rem; color: #999;">Revisão</span>
                    </div>
                </div>
            </div>

            <section class="hero">
                <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.75rem;">Seus Dados</h1>
                <p class="subtitle" style="color: var(--text-light); font-size: 1.15rem; max-width: 650px; margin: 0 auto; line-height: 1.6;">
                    Agora só precisamos de algumas informações suas. Leva menos de 2 minutos.
                </p>
            </section>

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; max-width: 1000px; margin: 0 auto;">
                <!-- Appointment Summary Card -->
                <section class="card" style="background: linear-gradient(135deg, rgba(139, 154, 139, 0.1), rgba(173, 216, 230, 0.1)); border: 2px solid var(--success-color);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; background: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">✓</div>
                        <div>
                            <h2 style="color: var(--success-color); margin: 0; font-size: 1.3rem;">Seu Horário Está Reservado!</h2>
                            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Garantido por 10 minutos enquanto você finaliza</p>
                        </div>
                    </div>
                    <div style="background: white; padding: 1.5rem; border-radius: 10px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.85rem; font-weight: 500;">📅 Data escolhida</p>
                                <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= date('d/m/Y', strtotime($selected_date)) ?></p>
                            </div>
                            <div>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.85rem; font-weight: 500;">⏰ Horário escolhido</p>
                                <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= date('H:i', strtotime($selected_time)) ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Booking Form -->
                <section class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="color: var(--primary-color); margin-bottom: 0.75rem; font-size: 1.6rem; font-weight: 600;">📝 Preencha os Campos Abaixo</h2>
                        <p style="color: var(--text-light); margin: 0; line-height: 1.7; font-size: 1rem;">São apenas 4 campos simples para confirmar seu agendamento</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.2rem;">⚠️</span>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="service_id">Qual tipo de consulta você precisa? *</label>
                            <select id="service_id" name="service_id" required onchange="updateServiceInfo()">
                                <option value="">Escolha o serviço</option>
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
                            <div id="service-info" style="margin-top: 0.75rem; padding: 1rem; background: rgba(173, 216, 230, 0.1); border-radius: 8px; border-left: 3px solid var(--accent-color); display: none;">
                                <p id="service-description" style="margin: 0 0 0.5rem 0; font-size: 0.95rem; color: var(--text-dark);"></p>
                                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                                    <p style="margin: 0; font-size: 0.9rem;"><strong>⏱️ Duração:</strong> <span id="service-duration"></span> min</p>
                                    <p style="margin: 0; font-size: 0.9rem;"><strong>💰 Investimento:</strong> R$ <span id="service-price"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Como podemos chamá-lo? *</label>
                            <input type="text" id="full_name" name="full_name" required 
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                                   placeholder="Seu nome completo">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Qual seu e-mail? *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="seu@email.com">
                            <small style="color: var(--text-light); display: block; margin-top: 0.25rem;">📧 Enviaremos a confirmação por e-mail</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp">WhatsApp para contato *</label>
                            <input type="tel" id="whatsapp" name="whatsapp" required 
                                   value="<?= htmlspecialchars($_POST['whatsapp'] ?? '') ?>"
                                   placeholder="(11) 99999-9999">
                            <small style="color: var(--text-light); display: block; margin-top: 0.25rem;">📱 Usaremos apenas para confirmar sua consulta</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Algo que gostaria de compartilhar? (opcional)</label>
                            <textarea id="notes" name="notes" rows="4" 
                                      placeholder="Pode nos contar brevemente o motivo da consulta ou alguma informação que considere importante"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                        </div>
                        
                        <div style="text-align: center; margin-top: 2rem;">
                            <button type="submit" class="btn btn-large" style="font-size: 1.1rem; padding: 1rem 2.5rem;">
                                Confirmar Meu Agendamento →
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Payment Information - Completamente Redesenhada -->
                <section class="card" style="background: linear-gradient(135deg, rgba(173, 216, 230, 0.1), white); border: 2px solid rgba(173, 216, 230, 0.3);">
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="color: var(--accent-color); margin-bottom: 0.75rem; font-size: 1.6rem; font-weight: 600;">💳 Como Funciona o Pagamento</h2>
                        <p style="color: var(--text-dark); margin: 0; line-height: 1.7; font-size: 1.05rem;">Rápido, simples e 100% seguro</p>
                    </div>

                    <!-- Payment Options Cards -->
                    <div style="display: grid; gap: 1.5rem; margin-bottom: 2rem;">
                        <!-- Pix Option -->
                        <div style="background: white; padding: 2rem; border-radius: 15px; border: 2px solid var(--primary-color); box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1rem;">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), #6d8c6d); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; box-shadow: 0 3px 10px rgba(139, 154, 139, 0.3);">💸</div>
                                <div style="flex: 1;">
                                    <h3 style="margin: 0 0 0.25rem 0; font-size: 1.3rem; color: var(--primary-color); font-weight: 700;">Pix</h3>
                                    <p style="margin: 0; color: var(--success-color); font-size: 0.95rem; font-weight: 600;">✓ Recomendado - Mais rápido</p>
                                </div>
                            </div>
                            <p style="color: var(--text-dark); margin: 0 0 0.75rem 0; font-size: 1rem; line-height: 1.7;">
                                Após confirmar seu agendamento, você receberá a <strong>chave Pix</strong> via WhatsApp e também por email.
                            </p>
                            <p style="color: var(--text-light); margin: 0; font-size: 0.95rem; line-height: 1.6;">
                                É só abrir seu app bancário, copiar a chave e fazer o pagamento em segundos.
                            </p>
                        </div>
                        
                        <!-- Transferência Option -->
                        <div style="background: white; padding: 2rem; border-radius: 15px; border: 2px solid var(--accent-color); box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1rem;">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--accent-color), #b89968); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; box-shadow: 0 3px 10px rgba(173, 216, 230, 0.3);">🏦</div>
                                <div style="flex: 1;">
                                    <h3 style="margin: 0 0 0.25rem 0; font-size: 1.3rem; color: var(--accent-color); font-weight: 700;">Transferência Bancária</h3>
                                    <p style="margin: 0; color: var(--text-light); font-size: 0.95rem; font-weight: 500;">Alternativa tradicional</p>
                                </div>
                            </div>
                            <p style="color: var(--text-dark); margin: 0 0 0.75rem 0; font-size: 1rem; line-height: 1.7;">
                                Você receberá os <strong>dados bancários completos</strong> (banco, agência, conta) por email.
                            </p>
                            <p style="color: var(--text-light); margin: 0; font-size: 0.95rem; line-height: 1.6;">
                                Faça a transferência normalmente pelo seu internet banking.
                            </p>
                        </div>
                    </div>

                    <!-- 24h Rule - DESTAQUE -->
                    <div style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.08)); border: 3px solid #FFC107; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);">
                        <div style="display: flex; align-items: start; gap: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: #FFC107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 3px 10px rgba(255, 193, 7, 0.4);">⏰</div>
                            <div>
                                <p style="margin: 0 0 0.75rem 0; font-weight: 600; color: var(--text-dark); font-size: 1.05rem;">Prazo de Confirmação</p>
                                <p style="margin: 0; color: var(--text-dark); line-height: 1.7; font-size: 0.95rem;">
                                    Para garantir seu horário, realize o pagamento <strong>até 24 horas antes da sessão</strong>. 
                                    Logo após confirmar, você receberá todas as instruções de pagamento por e-mail e WhatsApp. É rápido e fácil!
                                </p>
                            </div>
                        </div>
                    </div>
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

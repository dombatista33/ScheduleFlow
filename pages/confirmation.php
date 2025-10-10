<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Confirmado - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">

            <div class="header-content">
                <a href="index.php" class="logo"><img src="assets/images/logo-dra-daniela.png" alt="Dra. Daniela Lima - Psicóloga"></a>
                <nav class="nav">
                    <a href="index.php">Início</a>
                    <a href="index.php?page=services">Serviços</a>
                    <a href="index.php?page=calendar">Agendar</a>
                    <a href="index.php?page=google_meet_tutorial">Primeira Consulta</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">

            <?php
            global $pdo;
            $appointment_id = $_GET['appointment_id'] ?? null;
            
            if (!$appointment_id) {
                header('Location: index.php');
                exit;
            }
            
            // Get appointment details
            try {
                $stmt = $pdo->prepare("
                    SELECT a.*, c.full_name, c.email, c.whatsapp, s.name as service_name, s.price, s.duration
                    FROM appointments a
                    JOIN clients c ON a.client_id = c.id
                    JOIN services s ON a.service_id = s.id
                    WHERE a.id = ?
                ");
                $stmt->execute([$appointment_id]);
                $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$appointment) {
                    header('Location: index.php');
                    exit;
                }
            } catch(PDOException $e) {
                header('Location: index.php');
                exit;
            }
            ?>

            <section class="hero">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: var(--success-color); border-radius: 50%; margin-bottom: 1.5rem;">
                    <span style="font-size: 3rem; color: white;">✓</span>
                </div>
                <h1 style="color: var(--success-color); font-size: 2.5rem; margin-bottom: 0.75rem;">Tudo Confirmado!</h1>
                <p class="subtitle" style="font-size: 1.2rem; max-width: 650px; margin: 0 auto; line-height: 1.6;">
                    Sua sessão está agendada. Agora é só seguir os próximos passos simples abaixo.
                </p>
            </section>

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; max-width: 1000px; margin: 0 auto;">
                <!-- Appointment Details -->
                <section class="card" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05)); border: 2px solid var(--success-color);">
                    <h2 style="color: var(--success-color); margin-bottom: 1.5rem; font-size: 1.6rem; font-weight: 600;">📋 Resumo da Sua Sessão</h2>
                    <div style="background: white; padding: 2.5rem; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div style="display: grid; gap: 1.25rem;">
                            <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                <span style="font-size: 1.5rem;">👤</span>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Paciente</p>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($appointment['full_name']) ?></p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                <span style="font-size: 1.5rem;">💚</span>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Serviço</p>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($appointment['service_name']) ?></p>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1.5rem;">📅</span>
                                    <div>
                                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Data</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1.5rem;">⏰</span>
                                    <div>
                                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Horário</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= date('H:i', strtotime($appointment['appointment_time'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1.5rem;">⏱️</span>
                                    <div>
                                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Duração</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);"><?= $appointment['duration'] ?> minutos</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1.5rem;">💰</span>
                                    <div>
                                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Valor</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 1.15rem; font-weight: 600; color: var(--text-dark);">R$ <?= number_format($appointment['price'], 2, ',', '.') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Next Steps -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem; font-size: 1.6rem; font-weight: 600;">✅ O Que Fazer Agora</h2>
                    
                    <?php if (isset($_SESSION['email_sent']) && $_SESSION['email_sent']): ?>
                        <div style="padding: 1.5rem; border-radius: 12px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05)); border: 2px solid var(--success-color); margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                <span style="font-size: 1.8rem;">✉️</span>
                                <h3 style="color: var(--success-color); margin: 0; font-size: 1.2rem; font-weight: 600;">Email de Confirmação Enviado!</h3>
                            </div>
                            <p style="margin: 0; color: var(--text-dark); line-height: 1.7;">
                                Enviamos todas as informações para <strong><?= htmlspecialchars($appointment['email']) ?></strong>. 
                                Verifique sua caixa de entrada (e também o spam, por precaução).
                            </p>
                        </div>
                    <?php elseif (isset($_SESSION['email_sent']) && !$_SESSION['email_sent']): ?>
                        <div style="padding: 1.5rem; border-radius: 12px; background: rgba(255, 193, 7, 0.1); border: 2px solid #FFC107; margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                <span style="font-size: 1.8rem;">📱</span>
                                <h3 style="color: #F57C00; margin: 0; font-size: 1.2rem; font-weight: 600;">Você Receberá no WhatsApp</h3>
                            </div>
                            <p style="margin: 0; color: var(--text-dark); line-height: 1.7;">
                                Houve um problema temporário com o email, mas não se preocupe! Enviaremos todas as informações via WhatsApp.
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php 
                    // Clear email status from session after displaying
                    unset($_SESSION['email_sent'], $_SESSION['email_address']); 
                    ?>
                    
                    <!-- Step 1: Payment with 24h rule -->
                    <div style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.08)); padding: 2rem; border-radius: 15px; border: 3px solid #FFC107; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);">
                        <div style="display: flex; align-items: start; gap: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: #FFC107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 3px 10px rgba(255, 193, 7, 0.4);">1️⃣</div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 0.75rem 0; color: #F57C00; font-size: 1.3rem; font-weight: 700;">Realize o Pagamento em até 24 Horas</h3>
                                <p style="margin: 0 0 1rem 0; color: var(--text-dark); line-height: 1.7; font-size: 1.05rem;">
                                    <strong>Importante:</strong> Para garantir sua vaga, faça o pagamento <strong>até 24 horas antes da sessão</strong>.
                                </p>
                                <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem;">
                                    <p style="margin: 0 0 1rem 0; font-weight: 600; color: var(--text-dark); font-size: 1.05rem;">Opções de Pagamento:</p>
                                    <div style="display: grid; gap: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-size: 1.3rem;">💸</span>
                                            <p style="margin: 0; color: var(--text-dark); font-size: 1rem;"><strong>Pix:</strong> Chave enviada por WhatsApp e email</p>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-size: 1.3rem;">🏦</span>
                                            <p style="margin: 0; color: var(--text-dark); font-size: 1rem;"><strong>Transferência:</strong> Dados bancários no email</p>
                                        </div>
                                    </div>
                                </div>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.95rem; font-style: italic;">
                                    Você já recebeu (ou está recebendo) todas as informações de pagamento por WhatsApp e email.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Meeting Link -->
                    <div style="background: white; padding: 2rem; border-radius: 15px; border: 2px solid var(--primary-color); margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: start; gap: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; color: white;">2️⃣</div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 0.75rem 0; color: var(--primary-color); font-size: 1.3rem; font-weight: 700;">Aguarde o Link da Sala Virtual</h3>
                                <p style="margin: 0; color: var(--text-dark); line-height: 1.7; font-size: 1.05rem;">
                                    24 horas antes da sua sessão, você receberá o <strong>link do Google Meet</strong> por WhatsApp e email. 
                                    É só clicar no link na hora marcada.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Preparation -->
                    <div style="background: white; padding: 2rem; border-radius: 15px; border: 2px solid var(--accent-color); margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: start; gap: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; color: white;">3️⃣</div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 0.75rem 0; color: var(--accent-color); font-size: 1.3rem; font-weight: 700;">Prepare-se para a Sessão</h3>
                                <p style="margin: 0 0 1rem 0; color: var(--text-dark); line-height: 1.7; font-size: 1.05rem;">
                                    No dia da consulta, escolha um ambiente tranquilo e privado. Teste sua câmera e microfone com antecedência.
                                </p>
                                <a href="index.php?page=google_meet_tutorial" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1rem;">
                                    📱 Ver Guia: Como Acessar a Consulta Online
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Important Notes -->
                <section class="card" style="background: linear-gradient(to bottom, rgba(173, 216, 230, 0.08), white); border: 2px solid rgba(173, 216, 230, 0.3);">
                    <h2 style="color: var(--accent-color); margin-bottom: 1.5rem; font-size: 1.6rem; font-weight: 600;">💡 Informações Importantes</h2>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border-radius: 10px;">
                            <span style="font-size: 1.5rem;">📞</span>
                            <div>
                                <p style="margin: 0; color: var(--text-dark); line-height: 1.7; font-size: 1rem;">
                                    <strong>Seus Contatos:</strong> WhatsApp <?= htmlspecialchars($appointment['whatsapp']) ?> | Email <?= htmlspecialchars($appointment['email']) ?>
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border-radius: 10px;">
                            <span style="font-size: 1.5rem;">🔄</span>
                            <div>
                                <p style="margin: 0; color: var(--text-dark); line-height: 1.7; font-size: 1rem;">
                                    <strong>Cancelamento ou Remarcação:</strong> Entre em contato pelo WhatsApp com pelo menos 24 horas de antecedência
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border-radius: 10px;">
                            <span style="font-size: 1.5rem;">📲</span>
                            <div>
                                <p style="margin: 0; color: var(--text-dark); line-height: 1.7; font-size: 1rem;">
                                    <strong>Mantenha o WhatsApp ativo:</strong> Você receberá todas as atualizações e lembretes por lá
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div style="text-align: center; margin-top: 3rem; padding: 2rem 0;">
                <a href="index.php" class="btn btn-large" style="margin: 0 0.5rem; font-size: 1.1rem; padding: 1rem 2.5rem;">Voltar ao Início</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="logo-footer"><img src="assets/images/logo-dra-daniela.png" alt="Dra. Daniela Lima - Psicóloga"></div>
            <p>&copy; 2024 Terapia e Bem Estar - Dra. Daniela Lima. Todos os direitos reservados.</p>
            <p>CRP 00000/00 | Atendimento psicológico online</p>
        </div>
    </footer>
</body>
</html>
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
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Terapia e Bem Estar</a>
                <nav class="nav">
                    <a href="index.php">Início</a>
                    <a href="index.php?page=services">Serviços</a>
                    <a href="index.php?page=calendar">Agendar</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
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
                <h1 style="color: var(--success-color);">✓ Agendamento Confirmado!</h1>
                <p class="subtitle">Seu agendamento foi realizado com sucesso</p>
            </section>

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Appointment Details -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Detalhes do Agendamento</h2>
                    <div style="background: rgba(124, 179, 66, 0.1); padding: 2rem; border-radius: 15px;">
                        <p><strong>Paciente:</strong> <?= htmlspecialchars($appointment['full_name']) ?></p>
                        <p><strong>Serviço:</strong> <?= htmlspecialchars($appointment['service_name']) ?></p>
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></p>
                        <p><strong>Horário:</strong> <?= date('H:i', strtotime($appointment['appointment_time'])) ?></p>
                        <p><strong>Duração:</strong> <?= $appointment['duration'] ?> minutos</p>
                        <p><strong>Valor:</strong> R$ <?= number_format($appointment['price'], 2, ',', '.') ?></p>
                        <p><strong>Status:</strong> <span class="status-badge status-confirmed">Confirmado</span></p>
                    </div>
                </section>

                <!-- Next Steps -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Próximos Passos</h2>
                    <div style="display: grid; gap: 1rem;">
                        <?php if (isset($_SESSION['email_sent']) && $_SESSION['email_sent']): ?>
                            <div style="padding: 1rem; border-left: 4px solid var(--success-color); background: rgba(124, 179, 66, 0.05);">
                                <h3 style="color: var(--success-color); margin-bottom: 0.5rem;">✉️ E-mail Enviado</h3>
                                <p>Um e-mail de confirmação foi enviado para <strong><?= htmlspecialchars($appointment['email']) ?></strong> com todos os detalhes do agendamento.</p>
                            </div>
                        <?php elseif (isset($_SESSION['email_sent']) && !$_SESSION['email_sent']): ?>
                            <div style="padding: 1rem; border-left: 4px solid var(--warning-color); background: rgba(255, 138, 101, 0.05);">
                                <h3 style="color: var(--warning-color); margin-bottom: 0.5rem;">⚠️ E-mail Não Enviado</h3>
                                <p>Houve um problema ao enviar o e-mail de confirmação. Por favor, anote os detalhes do agendamento. Você receberá as informações via WhatsApp.</p>
                            </div>
                        <?php endif; ?>
                        <?php 
                        // Clear email status from session after displaying
                        unset($_SESSION['email_sent'], $_SESSION['email_address']); 
                        ?>
                        <div style="padding: 1rem; border-left: 4px solid var(--primary-color); background: rgba(139, 154, 139, 0.05);">
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">1. Link da Consulta</h3>
                            <p>Você receberá o link da sala virtual via WhatsApp e email 24 horas antes da consulta.</p>
                        </div>
                        <div style="padding: 1rem; border-left: 4px solid var(--secondary-color); background: rgba(168, 200, 236, 0.05);">
                            <h3 style="color: var(--secondary-color); margin-bottom: 0.5rem;">2. Lembrete</h3>
                            <p>Você receberá um lembrete com todas as informações necessárias para a consulta.</p>
                        </div>
                        <div style="padding: 1rem; border-left: 4px solid var(--accent-color); background: rgba(212, 185, 150, 0.05);">
                            <h3 style="color: var(--earth-tone); margin-bottom: 0.5rem;">3. Preparação</h3>
                            <p>Certifique-se de ter uma conexão estável e um ambiente tranquilo para a consulta.</p>
                            <p style="margin-top: 0.5rem;">
                                <a href="index.php?page=google_meet_tutorial" style="color: var(--primary-color); font-weight: bold; text-decoration: underline;">
                                    📱 Ver guia completo: Como acessar a consulta online
                                </a>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Contact Information -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Informações de Contato</h2>
                    <p><strong>WhatsApp:</strong> <?= htmlspecialchars($appointment['whatsapp']) ?></p>
                    <p><strong>E-mail:</strong> <?= htmlspecialchars($appointment['email']) ?></p>
                    <p style="margin-top: 1rem; color: var(--text-light);">
                        Em caso de dúvidas ou necessidade de remarcar, entre em contato conosco pelo WhatsApp (11) 99999-9999.
                    </p>
                </section>

                <!-- Virtual Meeting -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Sala Virtual</h2>
                    <p>Sua consulta será realizada via Google Meet. O link da sala será enviado 24 horas antes da consulta:</p>
                    <div style="background: rgba(139, 154, 139, 0.1); padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p><strong>Link da Sala:</strong> <span style="color: var(--text-light);">Será enviado 24 horas antes da consulta</span></p>
                    </div>
                    <p style="color: var(--text-light); font-size: 0.9rem;">
                        <strong>Dica:</strong> Teste sua câmera e microfone antes da consulta. Certifique-se de ter uma conexão estável com a internet.
                    </p>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="index.php?page=google_meet_tutorial" class="btn" style="display: inline-block;">
                            📱 Como Acessar a Consulta Online
                        </a>
                    </div>
                </section>

                <!-- Important Notes -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Informações Importantes</h2>
                    <ul style="margin-left: 1.5rem; line-height: 1.8;">
                        <li>Cancelamentos devem ser feitos com pelo menos 24 horas de antecedência</li>
                        <li>A sessão terá duração de <?= $appointment['duration'] ?> minutos</li>
                        <li>Mantenha seu WhatsApp ativo para receber as atualizações</li>
                        <li>Em caso de problemas técnicos, entre em contato imediatamente</li>
                    </ul>
                </section>
            </div>

            <div class="text-center mt-2">
                <a href="index.php" class="btn">Voltar ao Início</a>
                <a href="index.php?page=calendar" class="btn btn-secondary">Agendar Outra Consulta</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Terapia e Bem Estar - Dra. Daniela Lima. Todos os direitos reservados.</p>
            <p>CRP 00000/00 | Atendimento psicológico online</p>
        </div>
    </footer>
</body>
</html>
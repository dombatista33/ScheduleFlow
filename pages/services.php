<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Terapia e Bem Estar</a>
                <nav class="nav">
                    <a href="index.php">Início</a>
                    <a href="index.php?page=services" class="active">Serviços</a>
                    <a href="index.php?page=calendar">Agendar</a>
                    <a href="index.php?page=google_meet_tutorial">Primeira Consulta</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="hero">
                <h1>Nossos Serviços</h1>
                <p class="subtitle">Escolha o tipo de atendimento que melhor atende às suas necessidades</p>
            </section>

            <section class="services-grid">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
                    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($services as $service): ?>
                        <div class="card service-card">
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <p><?= htmlspecialchars($service['description']) ?></p>
                            <div class="service-info">
                                <span class="duration"><?= $service['duration'] ?> minutos</span>
                                <span class="price">R$ <?= number_format($service['price'], 2, ',', '.') ?></span>
                            </div>
                            <div class="mt-2">
                                <a href="index.php?page=calendar&service_id=<?= $service['id'] ?>" class="btn">Agendar Agora</a>
                            </div>
                        </div>
                    <?php endforeach;
                } catch(PDOException $e) {
                    echo '<div class="card"><p>Erro ao carregar serviços. Tente novamente mais tarde.</p></div>';
                }
                ?>
            </section>

            <section class="card mt-2">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Sobre a Terapia Cognitiva Comportamental</h2>
                <p>A Terapia Cognitiva Comportamental (TCC) é uma abordagem psicoterapêutica baseada em evidências científicas que se concentra na identificação e modificação de padrões de pensamento e comportamento que podem estar contribuindo para problemas emocionais.</p>
                
                <h3 style="color: var(--primary-color); margin: 1.5rem 0 1rem 0;">O que você pode esperar das sessões:</h3>
                <ul style="margin-left: 2rem; line-height: 1.8;">
                    <li>Ambiente acolhedor e sem julgamentos</li>
                    <li>Técnicas baseadas em evidências científicas</li>
                    <li>Estratégias práticas para o dia a dia</li>
                    <li>Foco em soluções e desenvolvimento de habilidades</li>
                    <li>Atendimento personalizado às suas necessidades específicas</li>
                </ul>

                <h3 style="color: var(--primary-color); margin: 1.5rem 0 1rem 0;">Principais áreas de atuação:</h3>
                <ul style="margin-left: 2rem; line-height: 1.8;">
                    <li>Transtornos de ansiedade e síndrome do pânico</li>
                    <li>Depressão e transtornos do humor</li>
                    <li>Problemas de relacionamento</li>
                    <li>Estresse e esgotamento profissional</li>
                    <li>Baixa autoestima e autoconfiança</li>
                    <li>Dificuldades de adaptação e mudanças</li>
                </ul>
            </section>

            <section class="contact-info">
                <h3>Dúvidas sobre qual serviço escolher?</h3>
                <p>Entre em contato conosco pelo WhatsApp (11) 99999-9999 ou e-mail contato@terapiaebemestar.com.br</p>
                <p>Teremos prazer em ajudá-lo a escolher o melhor atendimento para suas necessidades.</p>
            </section>
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
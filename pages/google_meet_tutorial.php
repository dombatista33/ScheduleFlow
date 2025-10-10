<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Como Acessar sua Consulta Online - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tutorial-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .tutorial-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary-color);
        }
        
        .step {
            background: rgba(139, 154, 139, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--secondary-color);
        }
        
        .step-number {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            margin-right: 1rem;
            font-size: 1.1rem;
        }
        
        .step-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }
        
        .step-content {
            margin-left: 3rem;
            line-height: 1.8;
            color: var(--text-light);
        }
        
        .checklist {
            list-style: none;
            padding-left: 0;
        }
        
        .checklist li {
            padding: 0.5rem 0;
            padding-left: 2rem;
            position: relative;
        }
        
        .checklist li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .alert-box {
            background: rgba(134, 188, 223, 0.1);
            border-left: 4px solid var(--secondary-color);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .alert-box strong {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }
        
        .download-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: var(--text-dark);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .download-btn:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .tip-box {
            background: rgba(255, 138, 101, 0.05);
            border-left: 4px solid var(--warning-color);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        @media (max-width: 768px) {
            .tutorial-section {
                padding: 1.5rem;
            }
            
            .step-content {
                margin-left: 0;
                margin-top: 1rem;
            }
            
            .download-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo"><img src="assets/images/logo-dra-daniela.png" alt="Dra. Daniela Lima - Psicóloga"></a>
                <nav class="nav">
                    <a href="index.php">Início</a>
                    <a href="index.php?page=services">Serviços</a>
                    <a href="index.php?page=calendar">Agendar</a>
                    <a href="index.php?page=google_meet_tutorial" class="active">Primeira Consulta</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="hero">
                <h1>Como Acessar sua Consulta Online</h1>
                <p class="subtitle">Um guia simples e completo para você se conectar com tranquilidade</p>
            </section>

            <div class="alert-box">
                <strong>😊 Bem-vindo(a)!</strong>
                <p style="margin-top: 0.5rem; margin-bottom: 0;">
                    Estou muito feliz por você estar aqui. Este guia foi preparado especialmente para tornar sua experiência com as consultas online o mais tranquila e acolhedora possível. Não se preocupe se você nunca fez uma videochamada antes – vamos passar por cada passo juntos!
                </p>
            </div>

            <!-- Preparação -->
            <section class="tutorial-section">
                <h2>📋 Preparação para a Consulta</h2>
                <p style="margin-bottom: 2rem;">Antes do dia da sua consulta, reserve alguns minutos para se preparar. Isso ajudará você a aproveitar melhor nosso tempo juntos.</p>
                
                <ul class="checklist">
                    <li><strong>Escolha um lugar tranquilo:</strong> Procure um ambiente privado onde você se sinta confortável para conversar sem interrupções.</li>
                    <li><strong>Verifique a bateria:</strong> Certifique-se de que seu celular está carregado ou conecte-o ao carregador durante a consulta.</li>
                    <li><strong>Teste sua internet:</strong> Uma conexão Wi-Fi estável é ideal. Se usar dados móveis, verifique se o sinal está bom.</li>
                    <li><strong>Tenha fones de ouvido:</strong> Embora não seja obrigatório, fones ajudam na privacidade e qualidade do áudio.</li>
                    <li><strong>Deixe um copo de água por perto:</strong> Cuide de si mesmo com carinho durante nossa conversa.</li>
                </ul>
            </section>

            <!-- Passo 1: Download -->
            <section class="tutorial-section">
                <h2>📱 Passo a Passo: Como Participar da sua Consulta</h2>
                
                <div class="step">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <span class="step-number">1</span>
                        <div class="step-title">Baixe o Aplicativo Google Meet</div>
                    </div>
                    <div class="step-content">
                        <p><strong>Se você ainda não tem o aplicativo instalado:</strong></p>
                        <p>O Google Meet é gratuito e seguro. Você pode baixá-lo através da loja de aplicativos do seu celular:</p>
                        
                        <div class="download-buttons">
                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.meetings" 
                               target="_blank" 
                               class="download-btn">
                                📱 Baixar para Android (Google Play)
                            </a>
                            <a href="https://apps.apple.com/br/app/google-meet/id1013231476" 
                               target="_blank" 
                               class="download-btn">
                                🍎 Baixar para iPhone (App Store)
                            </a>
                        </div>
                        
                        <p style="margin-top: 1.5rem;"><strong>Já tem o Google Meet?</strong> Perfeito! Pode ir direto para o próximo passo.</p>
                    </div>
                </div>

                <!-- Passo 2: Acesso -->
                <div class="step">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <span class="step-number">2</span>
                        <div class="step-title">Acesse o Link da Reunião</div>
                    </div>
                    <div class="step-content">
                        <p>No dia e horário da sua consulta, você receberá um <strong>link por e-mail ou WhatsApp</strong>. É muito simples acessar:</p>
                        
                        <ul class="checklist" style="margin-top: 1rem;">
                            <li>Abra o e-mail de confirmação ou a mensagem do WhatsApp que enviei para você</li>
                            <li>Encontre o link que começa com <code style="background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 3px;">meet.google.com</code></li>
                            <li>Toque no link – ele abrirá automaticamente no aplicativo Google Meet</li>
                            <li>Se for a primeira vez, o aplicativo pode pedir permissão para usar sua câmera e microfone – clique em <strong>"Permitir"</strong></li>
                        </ul>
                        
                        <div class="tip-box" style="margin-top: 1.5rem;">
                            <strong>💡 Dica importante:</strong> Tente acessar o link com 5 minutos de antecedência. Isso dá tempo para resolver qualquer detalhe técnico e começarmos no horário, com tranquilidade.
                        </div>
                    </div>
                </div>

                <!-- Passo 3: Verificação -->
                <div class="step">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <span class="step-number">3</span>
                        <div class="step-title">Verifique seu Áudio e Vídeo</div>
                    </div>
                    <div class="step-content">
                        <p>Antes de entrar na consulta, o Google Meet mostrará uma prévia de como você está aparecendo:</p>
                        
                        <ul class="checklist" style="margin-top: 1rem;">
                            <li><strong>Vídeo:</strong> Você verá sua própria imagem. Ajuste a posição do celular para que seu rosto fique bem enquadrado, em um lugar com boa iluminação (de preferência com luz natural ou uma janela à sua frente)</li>
                            <li><strong>Áudio:</strong> Fale algo e veja se o indicador de áudio se move. Se não funcionar, verifique se o microfone não está bloqueado nas configurações</li>
                            <li><strong>Câmera e microfone desligados?</strong> Procure os ícones de câmera 📹 e microfone 🎤 na tela e toque neles para ativar</li>
                        </ul>
                        
                        <p style="margin-top: 1.5rem;">Quando estiver tudo certo, toque no botão <strong>"Participar agora"</strong> ou <strong>"Entrar na reunião"</strong>.</p>
                    </div>
                </div>

                <!-- Passo 4: Consulta -->
                <div class="step">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <span class="step-number">4</span>
                        <div class="step-title">Durante a Consulta</div>
                    </div>
                    <div class="step-content">
                        <p>Assim que você entrar, nos veremos e poderemos começar nossa conversa. Algumas orientações para que você se sinta mais à vontade:</p>
                        
                        <ul class="checklist" style="margin-top: 1rem;">
                            <li>Deixe o celular apoiado em algo estável (uma mesa, um suporte) para não precisar segurá-lo o tempo todo</li>
                            <li>Mantenha o celular na horizontal (deitado) para uma melhor visualização</li>
                            <li>Se precisar desligar a câmera em algum momento, tudo bem – basta tocar no ícone da câmera</li>
                            <li>Você pode silenciar seu microfone tocando no ícone de microfone, caso haja algum barulho externo temporário</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Privacidade e Foco -->
            <section class="tutorial-section">
                <h2>🔒 Privacidade e Tranquilidade</h2>
                <p>Sua privacidade e conforto são muito importantes para mim. Aqui estão algumas orientações finais:</p>
                
                <ul class="checklist" style="margin-top: 1.5rem;">
                    <li><strong>Privacidade garantida:</strong> Nossa conversa é confidencial e protegida pelo sigilo profissional, assim como em uma consulta presencial</li>
                    <li><strong>Minimize distrações:</strong> Silencie notificações de outros aplicativos para não ser interrompido</li>
                    <li><strong>Esteja presente:</strong> Tente se concentrar em nosso momento juntos. Não se preocupe com a tecnologia – se algo der errado, resolvemos juntos</li>
                    <li><strong>Precisa de ajuda?</strong> Se tiver qualquer dificuldade para acessar, entre em contato pelo WhatsApp que eu te ajudo com todo carinho</li>
                </ul>
            </section>

            <!-- Final encorajador -->
            <div class="alert-box" style="background: rgba(139, 154, 139, 0.1); border-left-color: var(--primary-color);">
                <strong style="color: var(--primary-color);">💚 Estou aqui para você!</strong>
                <p style="margin-top: 0.5rem; margin-bottom: 0;">
                    Lembre-se: não existe pergunta boba ou dificuldade pequena demais. Se precisar de ajuda em qualquer etapa, pode me chamar. Estou aqui para tornar esse processo o mais acolhedor possível para você. Mal posso esperar para nossa conversa!
                </p>
                <p style="margin-top: 1rem; margin-bottom: 0; font-weight: 500;">
                    Com carinho,<br>
                    Dra. Daniela Lima 🌿
                </p>
            </div>

            <!-- Contato de Emergência -->
            <section class="tutorial-section">
                <h2>📞 Precisa de Ajuda Imediata?</h2>
                <p>Se você tiver qualquer dificuldade técnica antes ou durante a consulta:</p>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="https://wa.me/5511999999999" target="_blank" class="btn btn-large" style="display: inline-block;">
                        💬 Entre em Contato pelo WhatsApp
                    </a>
                </div>
                <p style="text-align: center; margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                    Ou envie um e-mail para: contato@terapiaebemestar.com.br
                </p>
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

<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php?page=admin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Google Meet - Painel Administrativo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
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
        
        .tip-box {
            background: rgba(173, 216, 230, 0.2);
            border-left: 4px solid var(--secondary-color);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
        }
        
        .tip-box strong {
            color: var(--secondary-color);
        }
        
        .warning-box {
            background: rgba(255, 138, 101, 0.1);
            border-left: 4px solid var(--warning-color);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
        }
        
        .warning-box strong {
            color: var(--warning-color);
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
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .quick-link {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 154, 139, 0.3);
        }
        
        .admin-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-btn {
            background: white;
            color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>📱 Tutorial Google Meet para Profissionais</h1>
            <a href="dashboard.php" class="back-btn">← Voltar ao Painel</a>
        </div>

        <!-- Introdução -->
        <div class="tutorial-section">
            <h2>👋 Bem-vinda, Dra. Daniela!</h2>
            <p style="line-height: 1.8; color: var(--text-light);">
                Este guia foi criado especialmente para você conduzir suas consultas online pelo Google Meet 
                diretamente do seu celular. Siga os passos abaixo para criar reuniões, gerenciar consultas 
                e garantir atendimentos de qualidade.
            </p>
        </div>

        <!-- Preparação Inicial -->
        <div class="tutorial-section">
            <h2>📋 Preparação Inicial (Fazer Uma Vez)</h2>
            
            <div class="step">
                <div class="step-title">
                    <span class="step-number">1</span>
                    Instalar o Aplicativo Google Meet
                </div>
                <div class="step-content">
                    <p><strong>No seu celular:</strong></p>
                    <ul class="checklist">
                        <li><strong>Android:</strong> Abra a Google Play Store</li>
                        <li><strong>iPhone:</strong> Abra a App Store</li>
                        <li>Pesquise por "Google Meet"</li>
                        <li>Toque em "Instalar" ou "Obter"</li>
                        <li>Aguarde o download e instalação</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">2</span>
                    Fazer Login com sua Conta Google
                </div>
                <div class="step-content">
                    <p><strong>Após instalar:</strong></p>
                    <ul class="checklist">
                        <li>Abra o app Google Meet</li>
                        <li>Toque em "Fazer login"</li>
                        <li>Digite seu email do Gmail</li>
                        <li>Digite sua senha</li>
                        <li>Permita o acesso à câmera e microfone quando solicitado</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Dica Importante:</strong> Use sempre a mesma conta Google para todas as consultas. 
                        Isso facilita o gerenciamento do seu histórico e configurações.
                    </div>
                </div>
            </div>
        </div>

        <!-- Como Criar Reunião -->
        <div class="tutorial-section">
            <h2>🎥 Como Criar uma Reunião para Consulta</h2>
            
            <div class="step">
                <div class="step-title">
                    <span class="step-number">1</span>
                    Abrir o Google Meet
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Toque no ícone do Google Meet no seu celular</li>
                        <li>Aguarde o app abrir</li>
                        <li>Você verá a tela inicial com suas opções</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">2</span>
                    Criar Nova Reunião
                </div>
                <div class="step-content">
                    <p><strong>Escolha uma das opções:</strong></p>
                    <ul class="checklist">
                        <li><strong>Opção 1:</strong> Toque em "Nova reunião" (botão verde)</li>
                        <li><strong>Opção 2:</strong> Toque no botão "+" (mais) no canto inferior</li>
                    </ul>
                    
                    <p style="margin-top: 1rem;"><strong>Depois, escolha:</strong></p>
                    <ul class="checklist">
                        <li><strong>"Criar reunião instantânea"</strong> - Para iniciar agora</li>
                        <li><strong>"Criar reunião para depois"</strong> - Para agendar</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Recomendação:</strong> Para consultas agendadas, crie a reunião com alguns minutos 
                        de antecedência e envie o link ao cliente pelo WhatsApp.
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">3</span>
                    Compartilhar o Link com o Cliente
                </div>
                <div class="step-content">
                    <p><strong>Após criar a reunião:</strong></p>
                    <ul class="checklist">
                        <li>Toque em "Compartilhar link de participação"</li>
                        <li>Escolha "WhatsApp" na lista de apps</li>
                        <li>Selecione o contato do cliente</li>
                        <li>Envie o link (o cliente receberá algo como: meet.google.com/abc-defg-hij)</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Mensagem Sugerida:</strong><br>
                        "Olá! Seguem os dados da sua consulta:<br>
                        🗓️ Data: [data]<br>
                        🕐 Horário: [horário]<br>
                        🔗 Link: [cole o link aqui]<br><br>
                        Clique no link na hora da consulta. Nos vemos em breve! 😊"
                    </div>
                </div>
            </div>
        </div>

        <!-- Entrar na Reunião -->
        <div class="tutorial-section">
            <h2>🚪 Como Entrar na Reunião no Horário da Consulta</h2>
            
            <div class="step">
                <div class="step-title">
                    <span class="step-number">1</span>
                    Acessar o Google Meet
                </div>
                <div class="step-content">
                    <p><strong>Você pode entrar de duas formas:</strong></p>
                    <ul class="checklist">
                        <li><strong>Forma 1:</strong> Abra o app Google Meet e toque na reunião que você criou</li>
                        <li><strong>Forma 2:</strong> Toque no link que você enviou ao cliente (do WhatsApp)</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">2</span>
                    Configurar Câmera e Microfone
                </div>
                <div class="step-content">
                    <p><strong>Antes de entrar:</strong></p>
                    <ul class="checklist">
                        <li>Você verá sua imagem na tela (preview)</li>
                        <li>Verifique se sua câmera está funcionando</li>
                        <li>Verifique se o microfone está ativado (ícone verde)</li>
                        <li>Ajuste sua posição e iluminação se necessário</li>
                    </ul>
                    
                    <div class="warning-box">
                        <strong>⚠️ Atenção:</strong> Sempre teste sua câmera e microfone ANTES da consulta. 
                        Entre na reunião 2-3 minutos antes do horário agendado.
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">3</span>
                    Participar da Reunião
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Toque no botão <strong>"Participar"</strong> ou <strong>"Entrar agora"</strong></li>
                        <li>Aguarde o cliente entrar (você verá "Aguardando outros participantes")</li>
                        <li>Quando o cliente entrar, a consulta inicia automaticamente</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Durante a Consulta -->
        <div class="tutorial-section">
            <h2>⚙️ Durante a Consulta - Controles Importantes</h2>
            
            <div class="step">
                <div class="step-title">
                    <span class="step-number">1</span>
                    Botões na Tela
                </div>
                <div class="step-content">
                    <p><strong>Controles principais (na parte inferior):</strong></p>
                    <ul class="checklist">
                        <li><strong>🎤 Microfone:</strong> Toque para desligar/ligar seu áudio</li>
                        <li><strong>📹 Câmera:</strong> Toque para desligar/ligar sua câmera</li>
                        <li><strong>📱 Girar:</strong> Toque no ícone para alternar entre câmera frontal/traseira</li>
                        <li><strong>🔴 Telefone vermelho:</strong> Encerrar a consulta</li>
                        <li><strong>⋯ Três pontos:</strong> Mais opções (ver abaixo)</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">2</span>
                    Opções Adicionais (Menu ⋯)
                </div>
                <div class="step-content">
                    <p><strong>Toque nos três pontos para acessar:</strong></p>
                    <ul class="checklist">
                        <li><strong>Legendas:</strong> Ativar legendas automáticas (útil para acessibilidade)</li>
                        <li><strong>Desfocar fundo:</strong> Deixa o fundo desfocado (mais privacidade)</li>
                        <li><strong>Alterar layout:</strong> Mudar como as câmeras aparecem na tela</li>
                        <li><strong>Configurações:</strong> Ajustar qualidade de vídeo e áudio</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Privacidade:</strong> Use "Desfocar fundo" se estiver em ambiente doméstico 
                        e quiser manter privacidade sobre o local onde está.
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">3</span>
                    Encerrar a Consulta
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Ao final da sessão, toque no botão <strong>vermelho (telefone)</strong></li>
                        <li>Toque em <strong>"Sair da reunião"</strong></li>
                        <li>A consulta será encerrada para todos os participantes</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Boas Práticas:</strong> Sempre avise o cliente alguns minutos antes de encerrar 
                        a sessão ("Estamos encerrando nossa consulta de hoje...").
                    </div>
                </div>
            </div>
        </div>

        <!-- Solução de Problemas -->
        <div class="tutorial-section">
            <h2>🔧 Solução de Problemas Comuns</h2>
            
            <div class="step">
                <div class="step-title">
                    <span class="step-number">❗</span>
                    Cliente não consegue ouvir você
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Verifique se seu microfone está ligado (ícone verde)</li>
                        <li>Toque no ícone do microfone se estiver vermelho/riscado</li>
                        <li>Peça ao cliente verificar o volume do celular dele</li>
                        <li>Saia e entre novamente na reunião</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">❗</span>
                    Cliente não consegue ver você
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Verifique se sua câmera está ligada (ícone verde)</li>
                        <li>Toque no ícone da câmera se estiver vermelho/riscado</li>
                        <li>Feche outros apps que possam estar usando a câmera</li>
                        <li>Reinicie o app Google Meet</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">❗</span>
                    Conexão ruim / vídeo travando
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Verifique sua conexão WiFi ou dados móveis</li>
                        <li>Peça aos outros em casa para pausar downloads/streaming</li>
                        <li>Desligue sua câmera temporariamente (economiza internet)</li>
                        <li>Reduza a qualidade do vídeo nas configurações</li>
                    </ul>
                    
                    <div class="tip-box">
                        <strong>💡 Alternativa:</strong> Se a conexão estiver muito ruim, você pode continuar apenas 
                        com áudio (câmera desligada) ou reagendar a consulta.
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-title">
                    <span class="step-number">❗</span>
                    Link não funciona ou reunião expirou
                </div>
                <div class="step-content">
                    <ul class="checklist">
                        <li>Links de reunião gratuitos expiram após 24h sem uso</li>
                        <li>Crie uma nova reunião seguindo os passos anteriores</li>
                        <li>Envie o novo link ao cliente pelo WhatsApp</li>
                        <li>Para evitar: crie o link no máximo 1h antes da consulta</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Checklist Rápido -->
        <div class="tutorial-section">
            <h2>✅ Checklist Rápido Antes de Cada Consulta</h2>
            
            <div style="background: rgba(139, 154, 139, 0.05); padding: 2rem; border-radius: 10px; margin-top: 1rem;">
                <ul class="checklist" style="font-size: 1.1rem;">
                    <li>Internet estável (WiFi preferencialmente)</li>
                    <li>Celular com bateria carregada (mínimo 50%)</li>
                    <li>App Google Meet instalado e atualizado</li>
                    <li>Ambiente silencioso e com boa iluminação</li>
                    <li>Criar reunião e enviar link ao cliente</li>
                    <li>Entrar na reunião 2-3 minutos antes</li>
                    <li>Testar câmera e microfone</li>
                    <li>Ativar "Desfocar fundo" se necessário</li>
                </ul>
            </div>
        </div>

        <!-- Dicas Profissionais -->
        <div class="tutorial-section">
            <h2>💼 Dicas para Atendimento Profissional Online</h2>
            
            <div style="display: grid; gap: 1.5rem; margin-top: 1rem;">
                <div class="tip-box">
                    <strong>🎯 Posicionamento:</strong> Coloque o celular na altura dos olhos, use um suporte se possível. 
                    Mantenha uma distância adequada (cerca de 50cm).
                </div>
                
                <div class="tip-box">
                    <strong>💡 Iluminação:</strong> Fique de frente para uma janela ou luz. Evite luz forte atrás de você 
                    (fica escuro).
                </div>
                
                <div class="tip-box">
                    <strong>🎧 Áudio:</strong> Use fones de ouvido com microfone para melhor qualidade de áudio e evitar eco.
                </div>
                
                <div class="tip-box">
                    <strong>🏠 Ambiente:</strong> Escolha um local tranquilo, sem interrupções. Avise familiares sobre 
                    o horário da consulta.
                </div>
                
                <div class="tip-box">
                    <strong>📱 Modo Avião:</strong> Ative o "Não Perturbe" no celular para evitar ligações durante a consulta 
                    (mantenha WiFi/dados ligados).
                </div>
                
                <div class="tip-box">
                    <strong>🔒 Privacidade:</strong> Use a função "Desfocar fundo" ou escolha um fundo neutro (parede clara).
                </div>
            </div>
        </div>

        <!-- Links Rápidos -->
        <div class="tutorial-section">
            <h2>🔗 Links Rápidos Úteis</h2>
            <div class="quick-links">
                <a href="https://meet.google.com/" target="_blank" class="quick-link">
                    🌐 Abrir Google Meet Web
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.meetings" target="_blank" class="quick-link">
                    📱 Download Google Meet (Android)
                </a>
                <a href="https://apps.apple.com/br/app/google-meet/id1013231476" target="_blank" class="quick-link">
                    📱 Download Google Meet (iPhone)
                </a>
                <a href="dashboard.php" class="quick-link">
                    🏠 Voltar ao Painel Admin
                </a>
            </div>
        </div>

        <!-- Rodapé -->
        <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: rgba(139, 154, 139, 0.05); border-radius: 10px;">
            <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                <strong>📚 Tutorial criado para Dra. Daniela Lima</strong>
            </p>
            <p style="color: var(--text-light); font-size: 0.9rem;">
                Terapia e Bem Estar - Atendimento Psicológico Online<br>
                Em caso de dúvidas técnicas, consulte o suporte técnico do Google Meet
            </p>
        </div>
    </div>
</body>
</html>

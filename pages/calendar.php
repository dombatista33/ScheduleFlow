<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo"><img src="assets/images/logo-dra-daniela.png" alt="Dra. Daniela Lima - Psicóloga"></a>
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
            <?php
            global $pdo;
            $selected_date = $_GET['date'] ?? null;
            $selected_time = $_GET['time'] ?? null;
            ?>

            <!-- Progress Indicator -->
            <div style="max-width: 800px; margin: 2rem auto 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
                    <div style="position: absolute; top: 50%; left: 0; right: 0; height: 2px; background: #e0e0e0; z-index: 0;"></div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">1</div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary-color);">Encontre Seu Horário</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">2</div>
                        <span style="font-size: 0.85rem; color: #999;">Seus Dados</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; align-items: center; z-index: 1;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem;">3</div>
                        <span style="font-size: 0.85rem; color: #999;">Revisão</span>
                    </div>
                </div>
            </div>

            <section class="hero">
                <h1 style="color: var(--primary-color); font-size: 2.2rem; margin-bottom: 0.75rem;">Escolha Data e Horário</h1>
                <p class="subtitle" style="color: var(--text-light); font-size: 1.15rem; max-width: 750px; margin: 0 auto 1rem; line-height: 1.6;">
                    Selecione a data e o horário que melhor funcionam para você. É rápido e simples.
                </p>
                <div style="max-width: 850px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 20px; height: 20px; background: #4CAF50; border-radius: 4px;"></div>
                        <span style="color: var(--text-dark); font-size: 0.95rem;">Verde = Disponível</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 20px; height: 20px; background: #2196F3; border-radius: 4px;"></div>
                        <span style="color: var(--text-dark); font-size: 0.95rem;">Azul = Horários Livres</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 20px; height: 20px; background: #ccc; border-radius: 4px;"></div>
                        <span style="color: var(--text-dark); font-size: 0.95rem;">Cinza = Indisponível</span>
                    </div>
                </div>
            </section>

            <div class="calendar-grid-2col" style="gap: 2rem; max-width: 1200px; margin: 0 auto;">
                <!-- Left Column: Choose Date -->
                <section class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="color: var(--primary-color); margin-bottom: 0.5rem; font-size: 1.4rem; font-weight: 600;">📅 Selecione a Data</h2>
                        <p style="color: var(--text-light); margin: 0; line-height: 1.6; font-size: 0.95rem;">
                            Clique em qualquer dia verde
                        </p>
                    </div>
                    <div id="calendar-container">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                </section>
                
                <!-- Right Column: Choose Time -->
                <section class="card" style="<?php if ($selected_date): ?>background: linear-gradient(to bottom, rgba(33, 150, 243, 0.08), white); border: 2px solid rgba(33, 150, 243, 0.3);<?php endif; ?>">
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="color: #2196F3; margin-bottom: 0.5rem; font-size: 1.4rem; font-weight: 600;">⏰ Selecione o Horário</h2>
                        <?php if ($selected_date): ?>
                            <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #2196F3; margin-bottom: 1rem;">
                                <p style="color: var(--text-dark); margin: 0; font-size: 1rem;">
                                    <strong><?= date('d/m/Y', strtotime($selected_date)) ?></strong>
                                </p>
                            </div>
                            <p style="color: var(--text-light); margin: 0; line-height: 1.6; font-size: 0.95rem;">
                                Clique em um horário azul disponível
                            </p>
                        <?php else: ?>
                            <p style="color: var(--text-light); margin: 0; line-height: 1.6; font-size: 0.95rem;">
                                Primeiro selecione uma data ao lado
                            </p>
                        <?php endif; ?>
                    </div>
                    <div id="time-slots-container">
                        <?php
                        // Only show time slots if a date is selected
                        if ($selected_date && isset($pdo)) {
                        try {
                            $stmt = $pdo->prepare("
                                SELECT t.time 
                                FROM time_slots t 
                                LEFT JOIN appointments a ON t.date = a.appointment_date AND t.time = a.appointment_time AND a.status != 'cancelled'
                                WHERE t.date = ? AND t.is_available = true AND a.id IS NULL
                                ORDER BY t.time ASC
                            ");
                            $stmt->execute([$selected_date]);
                            $available_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            
                            if (count($available_times) > 0): 
                                // Organize times by period
                                $morning = [];
                                $afternoon = [];
                                $evening = [];
                                
                                foreach($available_times as $time) {
                                    $hour = (int)date('H', strtotime($time));
                                    if ($hour < 12) {
                                        $morning[] = $time;
                                    } elseif ($hour < 18) {
                                        $afternoon[] = $time;
                                    } else {
                                        $evening[] = $time;
                                    }
                                }
                                ?>
                                <div class="time-periods">
                                    <?php if (!empty($morning)): ?>
                                        <div class="time-period">
                                            <h4 style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">🌅 Manhã</h4>
                                            <div class="time-slots">
                                                <?php foreach($morning as $time): ?>
                                                    <div class="time-slot <?= $selected_time == $time ? 'selected' : '' ?>" 
                                                         onclick="selectTime('<?= $time ?>')">
                                                        <?= date('H:i', strtotime($time)) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($afternoon)): ?>
                                        <div class="time-period">
                                            <h4 style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">☀️ Tarde</h4>
                                            <div class="time-slots">
                                                <?php foreach($afternoon as $time): ?>
                                                    <div class="time-slot <?= $selected_time == $time ? 'selected' : '' ?>" 
                                                         onclick="selectTime('<?= $time ?>')">
                                                        <?= date('H:i', strtotime($time)) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($evening)): ?>
                                        <div class="time-period">
                                            <h4 style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">🌙 Noite</h4>
                                            <div class="time-slots">
                                                <?php foreach($evening as $time): ?>
                                                    <div class="time-slot <?= $selected_time == $time ? 'selected' : '' ?>" 
                                                         onclick="selectTime('<?= $time ?>')">
                                                        <?= date('H:i', strtotime($time)) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($selected_time): ?>
                                    <div style="margin-top: 2rem; text-align: center; padding: 2.5rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05)); border-radius: 15px; border: 2px solid var(--success-color);">
                                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: var(--success-color); border-radius: 50%; margin-bottom: 1rem;">
                                            <span style="font-size: 2rem; color: white;">✓</span>
                                        </div>
                                        <h3 style="color: var(--success-color); margin: 0 0 0.5rem 0; font-size: 1.4rem; font-weight: 600;">Perfeito!</h3>
                                        <p style="color: var(--text-dark); margin: 0 0 1.5rem 0; font-size: 1.1rem;">
                                            <strong><?= date('d/m/Y', strtotime($selected_date)) ?> às <?= date('H:i', strtotime($selected_time)) ?></strong>
                                        </p>
                                        <a href="index.php?page=booking&date=<?= urlencode($selected_date) ?>&time=<?= urlencode($selected_time) ?><?= isset($_GET['service_id']) ? '&service_id=' . urlencode($_GET['service_id']) : '' ?>" 
                                           class="btn btn-large" style="font-size: 1.15rem; padding: 1.2rem 3rem;">
                                            Continuar →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="text-align: center; padding: 2.5rem; background: rgba(255, 138, 101, 0.1); border-radius: 15px; border: 2px solid rgba(255, 138, 101, 0.3);">
                                    <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
                                    <p style="color: var(--text-dark); margin: 0 0 0.5rem 0; font-size: 1.15rem; font-weight: 600;">Ops! Esta data está cheia</p>
                                    <p style="color: var(--text-light); margin: 0; font-size: 1rem;">Não se preocupe, escolha outra data verde no calendário acima.</p>
                                </div>
                            <?php endif;
                        } catch(PDOException $e) {
                            echo '<div style="text-align: center; padding: 2rem; background: rgba(255, 138, 101, 0.1); border-radius: 10px;"><p style="color: var(--warning-color);">Erro ao carregar horários disponíveis.</p></div>';
                        }
                        } elseif (!$selected_date) {
                            // Show neutral prompt when no date is selected
                            echo '<div style="text-align: center; padding: 3rem 2rem; background: rgba(139, 154, 139, 0.05); border-radius: 15px; border: 2px dashed var(--primary-color);">
                                    <div style="font-size: 3rem; margin-bottom: 1rem;">📆</div>
                                    <p style="color: var(--text-dark); margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 500;">Aguardando seleção de data</p>
                                    <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">Clique em um dia verde no calendário ao lado para ver os horários disponíveis</p>
                                </div>';
                        } else {
                            echo '<div style="text-align: center; padding: 2rem; background: rgba(255, 138, 101, 0.1); border-radius: 10px;"><p style="color: var(--warning-color);">Erro de conexão com banco de dados.</p></div>';
                        }
                        ?>
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
        // Calendar functionality
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize calendar
            generateCalendar();
        });
        
        function generateCalendar() {
            const container = document.getElementById('calendar-container');
            if (!container) return;
            
            const today = new Date();
            
            let calendarHTML = `
                <div class="calendar">
                    <div class="calendar-header">
                        <button class="calendar-nav" onclick="changeMonth(-1)">&lt;</button>
                        <h3 id="month-year" style="color: var(--primary-color); font-weight: 600;">${getMonthName(currentMonth)} ${currentYear}</h3>
                        <button class="calendar-nav" onclick="changeMonth(1)">&gt;</button>
                    </div>
                    <div class="calendar-grid">
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Dom</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Seg</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Ter</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Qua</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Qui</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Sex</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem; text-align: center;">Sáb</div>
            `;
            
            // Generate calendar days
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                
                const isCurrentMonth = date.getMonth() === currentMonth;
                const isPast = date < today.setHours(0,0,0,0);
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const dateStr = date.toISOString().split('T')[0];
                
                let classes = 'calendar-day';
                if (!isCurrentMonth) classes += ' text-light';
                if (isPast || isWeekend) classes += ' unavailable';
                if (<?= json_encode($selected_date ?? '') ?> === dateStr) classes += ' selected';
                
                const clickable = isCurrentMonth && !isPast && !isWeekend;
                
                calendarHTML += `
                    <div class="${classes}" ${clickable ? `onclick="selectDate('${dateStr}')"` : ''}>
                        ${date.getDate()}
                    </div>
                `;
            }
            
            calendarHTML += '</div></div>';
            container.innerHTML = calendarHTML;
        }
        
        function getMonthName(month) {
            const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            return months[month];
        }
        
        function selectDate(date) {
            // Preserve existing query parameters (like service_id)
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', 'calendar');
            urlParams.set('date', date);
            urlParams.delete('time'); // Reset time when selecting new date
            
            // Build absolute URL
            const newUrl = window.location.origin + window.location.pathname + '?' + urlParams.toString();
            window.location.href = newUrl;
        }
        
        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar();
        }
        
        function selectTime(time) {
            // Get date from PHP or from current URL
            let date = <?= json_encode($selected_date ?? '') ?>;
            
            // If no date from PHP, try to get from current URL
            if (!date) {
                const urlParams = new URLSearchParams(window.location.search);
                date = urlParams.get('date');
            }
            
            if (date) {
                // Preserve existing query parameters (like service_id)
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('page', 'calendar');
                urlParams.set('date', date);
                urlParams.set('time', time);
                
                // Build absolute URL
                const newUrl = window.location.origin + window.location.pathname + '?' + urlParams.toString();
                window.location.href = newUrl;
            } else {
                console.error('Nenhuma data selecionada para escolher horário');
                alert('Por favor, selecione uma data primeiro.');
            }
        }
    </script>
</body>
</html>

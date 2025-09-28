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
            
            // Get service information if service_id is provided
            $service = null;
            if ($service_id && isset($pdo)) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
                    $stmt->execute([$service_id]);
                    $service = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    $error = "Erro ao carregar informações do serviço.";
                }
            }
            ?>

            <section class="hero">
                <h1>Agenda Online</h1>
                <?php if ($service): ?>
                    <p class="subtitle">Agendando: <?= htmlspecialchars($service['name']) ?> - R$ <?= number_format($service['price'], 2, ',', '.') ?></p>
                <?php else: ?>
                    <p class="subtitle">Selecione um serviço para começar seu agendamento</p>
                <?php endif; ?>
            </section>

            <?php if (!$service_id): ?>
                <!-- Service Selection -->
                <section class="card">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">1. Escolha o Serviço</h2>
                    <div class="services-grid">
                        <?php
                        if (isset($pdo)) {
                        try {
                            $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
                            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach($services as $svc): ?>
                                <div class="card service-card">
                                    <h3><?= htmlspecialchars($svc['name']) ?></h3>
                                    <p><?= htmlspecialchars($svc['description']) ?></p>
                                    <div class="service-info">
                                        <span class="duration"><?= $svc['duration'] ?> minutos</span>
                                        <span class="price">R$ <?= number_format($svc['price'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="mt-2">
                                        <a href="index.php?page=calendar&service_id=<?= $svc['id'] ?>" class="btn">Selecionar</a>
                                    </div>
                                </div>
                            <?php endforeach;
                        } catch(PDOException $e) {
                            echo '<div class="card"><p>Erro ao carregar serviços. Tente novamente mais tarde.</p></div>';
                        }
                        } else {
                            echo '<div class="card"><p>Erro de conexão com banco de dados.</p></div>';
                        }
                        ?>
                    </div>
                </section>
            <?php else: ?>
                <!-- Day, Date and Time Selection -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    <!-- Step 2: Choose Day of Week -->
                    <section class="card">
                        <h2 style="color: var(--primary-color); margin-bottom: 1rem;">2. Escolha o Dia da Semana</h2>
                        <p style="margin-bottom: 2rem; color: var(--text-light);">Selecione o dia da semana de sua preferência:</p>
                        <div class="day-selector">
                            <div class="day-option" data-day="1" onclick="selectDayOfWeek(1)">
                                <div class="day-icon">📅</div>
                                <div class="day-name">Segunda-feira</div>
                            </div>
                            <div class="day-option" data-day="2" onclick="selectDayOfWeek(2)">
                                <div class="day-icon">📅</div>
                                <div class="day-name">Terça-feira</div>
                            </div>
                            <div class="day-option" data-day="3" onclick="selectDayOfWeek(3)">
                                <div class="day-icon">📅</div>
                                <div class="day-name">Quarta-feira</div>
                            </div>
                            <div class="day-option" data-day="4" onclick="selectDayOfWeek(4)">
                                <div class="day-icon">📅</div>
                                <div class="day-name">Quinta-feira</div>
                            </div>
                            <div class="day-option" data-day="5" onclick="selectDayOfWeek(5)">
                                <div class="day-icon">📅</div>
                                <div class="day-name">Sexta-feira</div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Step 3: Choose Specific Date -->
                    <section class="card" id="date-selection-card" style="display: none;">
                        <h2 style="color: var(--primary-color); margin-bottom: 1rem;">3. Escolha a Data</h2>
                        <p style="margin-bottom: 2rem; color: var(--text-light);">Datas disponíveis para <span id="selected-day-name"></span>:</p>
                        <div id="calendar-container">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </section>
                        
                        <?php if ($selected_date): ?>
                            <h3 style="color: var(--primary-color); margin: 2rem 0 1rem 0;">4. Escolha o Horário</h3>
                            <div id="time-slots-container">
                                <?php
                                // Get available time slots for the selected date
                                if (isset($pdo)) {
                                try {
                                    $stmt = $pdo->prepare("
                                        SELECT t.time 
                                        FROM time_slots t 
                                        LEFT JOIN appointments a ON t.date = a.appointment_date AND t.time = a.appointment_time AND a.status != 'cancelled'
                                        WHERE t.date = ? AND t.is_available = 1 AND a.id IS NULL
                                        ORDER BY t.time ASC
                                    ");
                                    $stmt->execute([$selected_date]);
                                    $available_times = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    
                                    if (count($available_times) > 0): ?>
                                        <div class="time-slots">
                                            <?php foreach($available_times as $time): ?>
                                                <div class="time-slot <?= $selected_time == $time ? 'selected' : '' ?>" 
                                                     onclick="selectTime('<?= $time ?>')">
                                                    <?= date('H:i', strtotime($time)) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <?php if ($selected_time): ?>
                                            <div class="mt-2 text-center">
                                                <a href="index.php?page=booking&service_id=<?= $service_id ?>&date=<?= $selected_date ?>&time=<?= $selected_time ?>" 
                                                   class="btn btn-large">Continuar para Dados Pessoais</a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p>Não há horários disponíveis para esta data. Por favor, escolha outra data.</p>
                                    <?php endif;
                                } catch(PDOException $e) {
                                    echo '<p>Erro ao carregar horários disponíveis.</p>';
                                }
                                } else {
                                    echo '<p>Erro de conexão com banco de dados.</p>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            <?php endif; ?>
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
        let selectedDayOfWeek = null;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize calendar
            const urlParams = new URLSearchParams(window.location.search);
            const dayOfWeek = urlParams.get('day_of_week');
            const serviceId = urlParams.get('service_id');
            
            if (dayOfWeek && serviceId) {
                // New flow: day already selected
                selectDayOfWeek(parseInt(dayOfWeek));
            } else if (serviceId) {
                // Old flow: show calendar immediately for backwards compatibility
                document.getElementById('date-selection-card').style.display = 'block';
                document.getElementById('selected-day-name').textContent = 'qualquer dia da semana';
                generateCalendar();
            }
        });
        
        function selectDayOfWeek(dayNum) {
            selectedDayOfWeek = dayNum;
            
            // Update visual selection
            document.querySelectorAll('.day-option').forEach(day => {
                day.classList.remove('selected');
            });
            document.querySelector(`[data-day="${dayNum}"]`).classList.add('selected');
            
            // Show selected day name
            const dayNames = ['', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];
            document.getElementById('selected-day-name').textContent = dayNames[dayNum];
            
            // Show date selection card
            document.getElementById('date-selection-card').style.display = 'block';
            
            // Generate calendar for selected day
            generateCalendar();
            
            // Update URL
            const serviceId = <?= $service_id ?? 'null' ?>;
            if (serviceId) {
                window.history.replaceState({}, '', `index.php?page=calendar&service_id=${serviceId}&day_of_week=${dayNum}`);
            }
        }
        
        function generateCalendar() {
            const container = document.getElementById('calendar-container');
            if (!container) return;
            
            const today = new Date();
            
            let calendarHTML = `
                <div class="calendar">
                    <div class="calendar-header">
                        <button class="calendar-nav" onclick="changeMonth(-1)">&lt;</button>
                        <h3 id="month-year">${getMonthName(currentMonth)} ${currentYear}</h3>
                        <button class="calendar-nav" onclick="changeMonth(1)">&gt;</button>
                    </div>
                    <div class="calendar-grid">
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Dom</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Seg</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Ter</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Qua</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Qui</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Sex</div>
                        <div style="font-weight: bold; background: var(--primary-color); color: white; padding: 0.5rem;">Sáb</div>
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
                const isSelectedDayOfWeek = selectedDayOfWeek === null || date.getDay() === selectedDayOfWeek;
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const dateStr = date.toISOString().split('T')[0];
                
                let classes = 'calendar-day';
                if (!isCurrentMonth) classes += ' text-light';
                if (isPast || isWeekend || (selectedDayOfWeek !== null && !isSelectedDayOfWeek)) classes += ' unavailable';
                if ('<?= $selected_date ?>' === dateStr) classes += ' selected';
                
                const clickable = isCurrentMonth && !isPast && !isWeekend && isSelectedDayOfWeek;
                
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
            const serviceId = <?= $service_id ?? 'null' ?>;
            if (serviceId && selectedDayOfWeek) {
                window.location.href = `index.php?page=calendar&service_id=${serviceId}&day_of_week=${selectedDayOfWeek}&date=${date}`;
            }
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
            const serviceId = <?= $service_id ?? 'null' ?>;
            const date = '<?= $selected_date ?>';
            if (serviceId && date) {
                window.location.href = `index.php?page=calendar&service_id=${serviceId}&date=${date}&time=${time}`;
            }
        }
    </script>
</body>
</html>
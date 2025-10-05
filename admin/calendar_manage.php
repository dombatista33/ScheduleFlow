<?php
require_once __DIR__ . '/config.php';

// Handle time slot updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['block_slots'])) {
        $dates = $_POST['dates'] ?? [];
        $times = $_POST['times'] ?? [];
        
        try {
            foreach ($dates as $date) {
                foreach ($times as $time) {
                    $stmt = $pdo->prepare("UPDATE time_slots SET is_available = FALSE WHERE date = ? AND time = ?");
                    $stmt->execute([$date, $time]);
                }
            }
            $success_message = "Horários bloqueados com sucesso!";
        } catch(PDOException $e) {
            $error_message = "Erro ao bloquear horários.";
        }
    } elseif (isset($_POST['unblock_slots'])) {
        $dates = $_POST['dates'] ?? [];
        $times = $_POST['times'] ?? [];
        
        try {
            foreach ($dates as $date) {
                foreach ($times as $time) {
                    $stmt = $pdo->prepare("UPDATE time_slots SET is_available = TRUE WHERE date = ? AND time = ?");
                    $stmt->execute([$date, $time]);
                }
            }
            $success_message = "Horários desbloqueados com sucesso!";
        } catch(PDOException $e) {
            $error_message = "Erro ao desbloquear horários.";
        }
    } elseif (isset($_POST['add_slots'])) {
        $new_dates = $_POST['new_dates'] ?? [];
        $new_times = $_POST['new_times'] ?? [];
        
        try {
            foreach ($new_dates as $date) {
                foreach ($new_times as $time) {
                    $stmt = $pdo->prepare("
                        INSERT INTO time_slots (date, time, is_available) 
                        VALUES (?, ?, TRUE) 
                        ON CONFLICT (date, time) DO UPDATE SET is_available = TRUE
                    ");
                    $stmt->execute([$date, $time]);
                }
            }
            $success_message = "Novos horários adicionados com sucesso!";
        } catch(PDOException $e) {
            $error_message = "Erro ao adicionar horários.";
        }
    }
}

// Get current week view
$current_date = $_GET['week'] ?? date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));
$week_end = date('Y-m-d', strtotime('sunday this week', strtotime($current_date)));

try {
    // Get time slots for the week
    $stmt = $pdo->prepare("
        SELECT ts.*, 
               CASE WHEN a.id IS NOT NULL THEN 'booked' ELSE 
                    CASE WHEN ts.is_available THEN 'available' ELSE 'blocked' END 
               END as slot_status,
               c.full_name as client_name
        FROM time_slots ts
        LEFT JOIN appointments a ON ts.date = a.appointment_date AND ts.time = a.appointment_time AND a.status != 'cancelled'
        LEFT JOIN clients c ON a.client_id = c.id
        WHERE ts.date BETWEEN ? AND ?
        ORDER BY ts.date, ts.time
    ");
    $stmt->execute([$week_start, $week_end]);
    $time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize slots by date and time
    $schedule = [];
    foreach ($time_slots as $slot) {
        $schedule[$slot['date']][$slot['time']] = $slot;
    }
} catch(PDOException $e) {
    $error_message = "Erro ao carregar agenda.";
    $schedule = [];
}

$times = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00'];
?>

<section class="card">
    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Gerenciar Agenda</h2>
    
    <?php if (isset($success_message)): ?>
        <div style="background: rgba(124, 179, 66, 0.2); color: var(--success-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Week Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <a href="index.php?page=admin&action=calendar&week=<?= date('Y-m-d', strtotime('-1 week', strtotime($week_start))) ?>" 
           class="btn btn-secondary">← Semana Anterior</a>
        
        <h3 style="margin: 0; color: var(--primary-color);">
            <?= date('d/m/Y', strtotime($week_start)) ?> - <?= date('d/m/Y', strtotime($week_end)) ?>
        </h3>
        
        <a href="index.php?page=admin&action=calendar&week=<?= date('Y-m-d', strtotime('+1 week', strtotime($week_start))) ?>" 
           class="btn btn-secondary">Próxima Semana →</a>
    </div>
    
    <!-- Weekly Calendar -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
            <thead>
                <tr style="background: var(--primary-color); color: white;">
                    <th style="padding: 1rem; text-align: left;">Horário</th>
                    <?php 
                    for ($i = 0; $i < 7; $i++) {
                        $date = date('Y-m-d', strtotime($week_start . " +$i days"));
                        $day_name = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'][date('w', strtotime($date))];
                        echo "<th style='padding: 1rem; text-align: center;'>$day_name<br><small>" . date('d/m', strtotime($date)) . "</small></th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($times as $time): ?>
                    <tr>
                        <td style="padding: 0.5rem; border: 1px solid var(--border-color); font-weight: bold;">
                            <?= date('H:i', strtotime($time)) ?>
                        </td>
                        <?php 
                        for ($i = 0; $i < 7; $i++) {
                            $date = date('Y-m-d', strtotime($week_start . " +$i days"));
                            $slot = $schedule[$date][$time] ?? null;
                            $is_weekend = date('w', strtotime($date)) == 0 || date('w', strtotime($date)) == 6;
                            
                            if ($is_weekend) {
                                echo "<td style='padding: 0.5rem; border: 1px solid var(--border-color); background: #f5f5f5; text-align: center;'>-</td>";
                            } else {
                                $status = $slot ? $slot['slot_status'] : 'none';
                                $bg_color = match($status) {
                                    'available' => 'rgba(124, 179, 66, 0.2)',
                                    'blocked' => 'rgba(255, 138, 101, 0.2)',
                                    'booked' => 'rgba(139, 154, 139, 0.2)',
                                    default => 'rgba(200, 200, 200, 0.2)'
                                };
                                
                                $text = match($status) {
                                    'available' => 'Disponível',
                                    'blocked' => 'Bloqueado',
                                    'booked' => $slot['client_name'] ?? 'Agendado',
                                    default => 'N/A'
                                };
                                
                                echo "<td style='padding: 0.5rem; border: 1px solid var(--border-color); background: $bg_color; text-align: center; font-size: 0.8rem;'>$text</td>";
                            }
                        }
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Legend -->
    <div style="display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 20px; height: 20px; background: rgba(124, 179, 66, 0.2); border-radius: 3px;"></div>
            <span>Disponível</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 20px; height: 20px; background: rgba(139, 154, 139, 0.2); border-radius: 3px;"></div>
            <span>Agendado</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 20px; height: 20px; background: rgba(255, 138, 101, 0.2); border-radius: 3px;"></div>
            <span>Bloqueado</span>
        </div>
    </div>
</section>

<!-- Quick Actions -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
    <!-- Block Time Slots -->
    <section class="card">
        <h3 style="color: var(--warning-color); margin-bottom: 1rem;">Bloquear Horários</h3>
        <form method="POST">
            <div class="form-group">
                <label>Datas</label>
                <input type="date" name="dates[]" multiple style="width: 100%; padding: 0.5rem;">
                <small>Para múltiplas datas, adicione mais campos conforme necessário</small>
            </div>
            <div class="form-group">
                <label>Horários</label>
                <select name="times[]" multiple style="width: 100%; padding: 0.5rem; height: 120px;">
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time ?>"><?= date('H:i', strtotime($time)) ?></option>
                    <?php endforeach; ?>
                </select>
                <small>Segure Ctrl/Cmd para selecionar múltiplos horários</small>
            </div>
            <button type="submit" name="block_slots" class="btn" style="background: var(--warning-color);">Bloquear Horários</button>
        </form>
    </section>
    
    <!-- Unblock Time Slots -->
    <section class="card">
        <h3 style="color: var(--success-color); margin-bottom: 1rem;">Desbloquear Horários</h3>
        <form method="POST">
            <div class="form-group">
                <label>Datas</label>
                <input type="date" name="dates[]" style="width: 100%; padding: 0.5rem;">
            </div>
            <div class="form-group">
                <label>Horários</label>
                <select name="times[]" multiple style="width: 100%; padding: 0.5rem; height: 120px;">
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time ?>"><?= date('H:i', strtotime($time)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="unblock_slots" class="btn" style="background: var(--success-color);">Desbloquear Horários</button>
        </form>
    </section>
</div>
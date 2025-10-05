<?php
require_once __DIR__ . '/config.php';

// Handle appointment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $appointment_id]);
        $success_message = "Status do agendamento atualizado com sucesso!";
    } catch(PDOException $e) {
        $error_message = "Erro ao atualizar status do agendamento.";
    }
}

// Get appointments with filters
$filter_status = $_GET['filter_status'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';

$where_conditions = [];
$params = [];

if ($filter_status) {
    $where_conditions[] = "a.status = ?";
    $params[] = $filter_status;
}

if ($filter_date) {
    $where_conditions[] = "a.appointment_date = ?";
    $params[] = $filter_date;
}

$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.full_name, c.email, c.whatsapp, s.name as service_name, s.price, s.duration
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        JOIN services s ON a.service_id = s.id
        $where_clause
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Erro ao carregar agendamentos.";
    $appointments = [];
}
?>

<!-- Tutorial Quick Access -->
<div style="background: linear-gradient(135deg, #ADD8E6 0%, #87CEEB 100%); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h3 style="margin: 0 0 0.5rem 0; color: #333;">📱 Como Entrar nas Consultas pelo Celular?</h3>
        <p style="margin: 0; color: #555;">Acesse o tutorial completo sobre como usar o Google Meet no celular para suas consultas online.</p>
    </div>
    <a href="admin/google_meet_guide.php" target="_blank" style="background: white; color: #333; padding: 0.75rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 600; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.3s ease;">
        Ver Tutorial Completo →
    </a>
</div>

<section class="card">
    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Gerenciar Agendamentos</h2>
    
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
    
    <!-- Filters -->
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <input type="hidden" name="page" value="admin">
        <input type="hidden" name="action" value="appointments">
        
        <select name="filter_status" style="padding: 0.5rem; border-radius: 5px; border: 1px solid var(--border-color);">
            <option value="">Todos os Status</option>
            <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pendente</option>
            <option value="confirmed" <?= $filter_status === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
            <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
            <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Concluído</option>
        </select>
        
        <input type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>" 
               style="padding: 0.5rem; border-radius: 5px; border: 1px solid var(--border-color);">
        
        <button type="submit" class="btn" style="padding: 0.5rem 1rem;">Filtrar</button>
        <a href="index.php?page=admin&action=appointments" class="btn btn-secondary" style="padding: 0.5rem 1rem; text-decoration: none;">Limpar</a>
    </form>
    
    <!-- Appointments Table -->
    <?php if (count($appointments) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Contato</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($appointments as $apt): ?>
                        <tr>
                            <td>
                                <?= date('d/m/Y', strtotime($apt['appointment_date'])) ?><br>
                                <small><?= date('H:i', strtotime($apt['appointment_time'])) ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($apt['full_name']) ?></strong><br>
                                <small><?= htmlspecialchars($apt['email']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($apt['service_name']) ?><br>
                                <small><?= $apt['duration'] ?> min</small>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $apt['whatsapp']) ?>" target="_blank" style="color: var(--primary-color);">
                                    <?= htmlspecialchars($apt['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $apt['status'] ?>">
                                    <?php
                                    $status_labels = [
                                        'pending' => 'Pendente',
                                        'confirmed' => 'Confirmado',
                                        'cancelled' => 'Cancelado',
                                        'completed' => 'Concluído'
                                    ];
                                    echo $status_labels[$apt['status']] ?? $apt['status'];
                                    ?>
                                </span>
                            </td>
                            <td>R$ <?= number_format($apt['price'], 2, ',', '.') ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?= $apt['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding: 0.3rem; font-size: 0.8rem;">
                                        <option value="pending" <?= $apt['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="confirmed" <?= $apt['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                                        <option value="cancelled" <?= $apt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                        <option value="completed" <?= $apt['status'] === 'completed' ? 'selected' : '' ?>>Concluído</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            Nenhum agendamento encontrado com os filtros selecionados.
        </p>
    <?php endif; ?>
</section>

<!-- Summary Statistics -->
<section class="card">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Estatísticas Rápidas</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <?php
        try {
            $stats_query = "
                SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(s.price) as total_value
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.appointment_date >= CURRENT_DATE
                GROUP BY status
            ";
            $stmt = $pdo->query($stats_query);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($stats as $stat):
        ?>
            <div style="background: rgba(139, 154, 139, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--primary-color);">
                    <?= $status_labels[$stat['status']] ?? $stat['status'] ?>
                </h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;">
                    <?= $stat['count'] ?>
                </p>
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                    R$ <?= number_format($stat['total_value'], 2, ',', '.') ?>
                </p>
            </div>
        <?php endforeach; } catch(PDOException $e) { /* Ignore stats errors */ } ?>
    </div>
</section>
<?php
require_once __DIR__ . '/config.php';

// Get clients with their appointment counts
try {
    $stmt = $pdo->query("
        SELECT c.*, 
               COUNT(a.id) as total_appointments,
               MAX(a.appointment_date) as last_appointment,
               SUM(CASE WHEN a.status = 'completed' THEN s.price ELSE 0 END) as total_spent
        FROM clients c
        LEFT JOIN appointments a ON c.id = a.client_id
        LEFT JOIN services s ON a.service_id = s.id
        GROUP BY c.id, c.full_name, c.email, c.whatsapp, c.created_at
        ORDER BY c.created_at DESC
    ");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Erro ao carregar clientes.";
    $clients = [];
}
?>

<section class="card">
    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Lista de Clientes</h2>
    
    <?php if (isset($error_message)): ?>
        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($clients) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Contato</th>
                        <th>Total de Consultas</th>
                        <th>Última Consulta</th>
                        <th>Total Gasto</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $client): ?>
                        <tr>
                            <td data-label="Cliente">
                                <strong><?= htmlspecialchars($client['full_name']) ?></strong>
                            </td>
                            <td data-label="Contato">
                                <div><?= htmlspecialchars($client['email']) ?></div>
                                <div>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $client['whatsapp']) ?>" target="_blank" style="color: var(--primary-color);">
                                        <?= htmlspecialchars($client['whatsapp']) ?>
                                    </a>
                                </div>
                            </td>
                            <td data-label="Total de Consultas" style="text-align: center;">
                                <?= $client['total_appointments'] ?>
                            </td>
                            <td data-label="Última Consulta">
                                <?php if ($client['last_appointment']): ?>
                                    <?= date('d/m/Y', strtotime($client['last_appointment'])) ?>
                                <?php else: ?>
                                    <span style="color: var(--text-light);">Nunca</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Total Gasto" style="text-align: right;">
                                R$ <?= number_format($client['total_spent'], 2, ',', '.') ?>
                            </td>
                            <td data-label="Cadastro">
                                <?= date('d/m/Y', strtotime($client['created_at'])) ?>
                            </td>
                            <td data-label="Ações">
                                <a href="index.php?page=admin&action=appointments&filter_client=<?= $client['id'] ?>" 
                                   class="btn-small" style="color: var(--primary-color); text-decoration: none; font-size: 0.9rem;">
                                    Ver Agendamentos
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            Nenhum cliente cadastrado ainda.
        </p>
    <?php endif; ?>
</section>

<!-- Client Statistics -->
<section class="card">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Estatísticas de Clientes</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <?php
        try {
            // Total clients
            $total_clients = count($clients);
            
            // Active clients (with appointments in last 30 days)
            $stmt = $pdo->query("
                SELECT COUNT(DISTINCT client_id) 
                FROM appointments 
                WHERE appointment_date >= CURRENT_DATE - INTERVAL '30 days'
            ");
            $active_clients = $stmt->fetchColumn();
            
            // New clients this month
            $stmt = $pdo->query("
                SELECT COUNT(*) 
                FROM clients 
                WHERE created_at >= DATE_TRUNC('month', CURRENT_DATE)
            ");
            $new_clients = $stmt->fetchColumn();
            
            // Average revenue per client
            $total_revenue = array_sum(array_column($clients, 'total_spent'));
            $avg_revenue = $total_clients > 0 ? $total_revenue / $total_clients : 0;
        ?>
            <div style="background: rgba(139, 154, 139, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--primary-color);">Total de Clientes</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= $total_clients ?></p>
            </div>
            
            <div style="background: rgba(168, 200, 236, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--secondary-color);">Clientes Ativos</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= $active_clients ?></p>
                <p style="margin: 0; color: var(--text-light); font-size: 0.8rem;">Últimos 30 dias</p>
            </div>
            
            <div style="background: rgba(212, 185, 150, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--accent-color);">Novos este Mês</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= $new_clients ?></p>
            </div>
            
            <div style="background: rgba(184, 160, 130, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--earth-tone);">Receita Média</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;">R$ <?= number_format($avg_revenue, 0, ',', '.') ?></p>
                <p style="margin: 0; color: var(--text-light); font-size: 0.8rem;">Por cliente</p>
            </div>
        <?php } catch(PDOException $e) { /* Ignore stats errors */ } ?>
    </div>
</section>
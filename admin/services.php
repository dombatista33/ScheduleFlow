<?php
// Handle service CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        try {
            switch ($action) {
                case 'create':
                    $stmt = $pdo->prepare("INSERT INTO services (name, description, duration, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['duration'],
                        $_POST['price']
                    ]);
                    $success_message = "Serviço criado com sucesso!";
                    break;
                    
                case 'update':
                    $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, duration = ?, price = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['duration'],
                        $_POST['price'],
                        $_POST['service_id']
                    ]);
                    $success_message = "Serviço atualizado com sucesso!";
                    break;
                    
                case 'delete':
                    // Check if service has appointments
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE service_id = ?");
                    $stmt->execute([$_POST['service_id']]);
                    $appointment_count = $stmt->fetchColumn();
                    
                    if ($appointment_count > 0) {
                        $error_message = "Não é possível excluir este serviço pois existem agendamentos associados.";
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                        $stmt->execute([$_POST['service_id']]);
                        $success_message = "Serviço excluído com sucesso!";
                    }
                    break;
            }
        } catch(PDOException $e) {
            $error_message = "Erro ao processar operação: " . $e->getMessage();
        }
    }
}

// Get all services
try {
    $stmt = $pdo->query("
        SELECT s.*, 
               COUNT(a.id) as total_appointments,
               SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
               SUM(CASE WHEN a.status = 'completed' THEN s.price ELSE 0 END) as total_revenue
        FROM services s
        LEFT JOIN appointments a ON s.id = a.service_id
        GROUP BY s.id, s.name, s.description, s.duration, s.price, s.created_at
        ORDER BY s.name
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Erro ao carregar serviços.";
    $services = [];
}

// Get service for editing if requested
$edit_service = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_message = "Erro ao carregar serviço para edição.";
    }
}
?>

<section class="card">
    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Gerenciar Serviços</h2>
    
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
    
    <!-- Add/Edit Service Form -->
    <div style="background: rgba(139, 154, 139, 0.1); padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
            <?= $edit_service ? 'Editar Serviço' : 'Adicionar Novo Serviço' ?>
        </h3>
        
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <input type="hidden" name="action" value="<?= $edit_service ? 'update' : 'create' ?>">
            <?php if ($edit_service): ?>
                <input type="hidden" name="service_id" value="<?= $edit_service['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Nome do Serviço *</label>
                <input type="text" id="name" name="name" required 
                       value="<?= htmlspecialchars($edit_service['name'] ?? '') ?>"
                       placeholder="Ex: Consulta Inicial">
            </div>
            
            <div class="form-group">
                <label for="duration">Duração (minutos) *</label>
                <input type="number" id="duration" name="duration" required min="15" max="180"
                       value="<?= htmlspecialchars($edit_service['duration'] ?? '') ?>"
                       placeholder="Ex: 60">
            </div>
            
            <div class="form-group">
                <label for="price">Preço (R$) *</label>
                <input type="number" id="price" name="price" required min="0" step="0.01"
                       value="<?= htmlspecialchars($edit_service['price'] ?? '') ?>"
                       placeholder="Ex: 150.00">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Descrição detalhada do serviço..."><?= htmlspecialchars($edit_service['description'] ?? '') ?></textarea>
            </div>
            
            <div style="grid-column: 1 / -1; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-large">
                    <?= $edit_service ? 'Atualizar Serviço' : 'Criar Serviço' ?>
                </button>
                
                <?php if ($edit_service): ?>
                    <a href="index.php?page=admin&action=services" class="btn btn-secondary">Cancelar Edição</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Services Table -->
    <?php if (count($services) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Duração</th>
                        <th>Preço</th>
                        <th>Agendamentos</th>
                        <th>Concluídos</th>
                        <th>Receita Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($services as $service): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($service['name']) ?></strong>
                                <?php if ($service['description']): ?>
                                    <br><small style="color: var(--text-light);"><?= htmlspecialchars($service['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $service['duration'] ?> min</td>
                            <td>R$ <?= number_format($service['price'], 2, ',', '.') ?></td>
                            <td style="text-align: center;"><?= $service['total_appointments'] ?></td>
                            <td style="text-align: center;"><?= $service['completed_appointments'] ?></td>
                            <td>R$ <?= number_format($service['total_revenue'], 2, ',', '.') ?></td>
                            <td style="white-space: nowrap;">
                                <a href="index.php?page=admin&action=services&edit=<?= $service['id'] ?>" 
                                   style="color: var(--primary-color); text-decoration: none; margin-right: 1rem;">
                                    Editar
                                </a>
                                
                                <?php if ($service['total_appointments'] == 0): ?>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este serviço?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--warning-color); cursor: pointer;">
                                            Excluir
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--text-light); font-size: 0.9rem;">
                                        (Tem agendamentos)
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            Nenhum serviço cadastrado ainda.
        </p>
    <?php endif; ?>
</section>

<!-- Services Statistics -->
<section class="card">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Estatísticas dos Serviços</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <?php
        try {
            $total_services = count($services);
            $total_revenue = array_sum(array_column($services, 'total_revenue'));
            $total_appointments = array_sum(array_column($services, 'total_appointments'));
            $avg_price = $total_services > 0 ? array_sum(array_column($services, 'price')) / $total_services : 0;
        ?>
            <div style="background: rgba(139, 154, 139, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--primary-color);">Total de Serviços</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= $total_services ?></p>
            </div>
            
            <div style="background: rgba(168, 200, 236, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--secondary-color);">Receita Total</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;">R$ <?= number_format($total_revenue, 0, ',', '.') ?></p>
            </div>
            
            <div style="background: rgba(212, 185, 150, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--accent-color);">Total Agendamentos</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= $total_appointments ?></p>
            </div>
            
            <div style="background: rgba(184, 160, 130, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
                <h4 style="margin: 0; color: var(--earth-tone);">Preço Médio</h4>
                <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;">R$ <?= number_format($avg_price, 0, ',', '.') ?></p>
            </div>
        <?php } catch(Exception $e) { /* Ignore stats errors */ } ?>
    </div>
</section>
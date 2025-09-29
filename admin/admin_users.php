<?php
// Handle admin user CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        try {
            switch ($action) {
                case 'create':
                    // Validate password strength
                    $password = $_POST['password'];
                    if (strlen($password) < 6) {
                        $error_message = "A senha deve ter pelo menos 6 caracteres.";
                        break;
                    }
                    
                    // Check if username already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
                    $stmt->execute([$_POST['username']]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = "Nome de usuário já existe.";
                        break;
                    }
                    
                    // Check if email already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE email = ?");
                    $stmt->execute([$_POST['email']]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = "Este email já está cadastrado.";
                        break;
                    }
                    
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['username'],
                        $password_hash,
                        $_POST['full_name'],
                        $_POST['email']
                    ]);
                    $success_message = "Usuário administrativo criado com sucesso!";
                    break;
                    
                case 'update':
                    // Check if username already exists (except current user)
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ? AND id != ?");
                    $stmt->execute([$_POST['username'], $_POST['user_id']]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = "Nome de usuário já existe.";
                        break;
                    }
                    
                    // Check if email already exists (except current user)
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE email = ? AND id != ?");
                    $stmt->execute([$_POST['email'], $_POST['user_id']]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = "Este email já está cadastrado.";
                        break;
                    }
                    
                    if (!empty($_POST['password'])) {
                        // Update with new password
                        if (strlen($_POST['password']) < 6) {
                            $error_message = "A senha deve ter pelo menos 6 caracteres.";
                            break;
                        }
                        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, password_hash = ?, full_name = ?, email = ? WHERE id = ?");
                        $stmt->execute([
                            $_POST['username'],
                            $password_hash,
                            $_POST['full_name'],
                            $_POST['email'],
                            $_POST['user_id']
                        ]);
                    } else {
                        // Update without changing password
                        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, full_name = ?, email = ? WHERE id = ?");
                        $stmt->execute([
                            $_POST['username'],
                            $_POST['full_name'],
                            $_POST['email'],
                            $_POST['user_id']
                        ]);
                    }
                    $success_message = "Usuário administrativo atualizado com sucesso!";
                    break;
                    
                case 'delete':
                    // Prevent deleting self
                    if ($_POST['user_id'] == $_SESSION['admin_id']) {
                        $error_message = "Você não pode excluir sua própria conta.";
                        break;
                    }
                    
                    // Check if there's more than one admin
                    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
                    $admin_count = $stmt->fetchColumn();
                    
                    if ($admin_count <= 1) {
                        $error_message = "Não é possível excluir o último usuário administrativo.";
                        break;
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                    $stmt->execute([$_POST['user_id']]);
                    $success_message = "Usuário administrativo excluído com sucesso!";
                    break;
            }
        } catch(PDOException $e) {
            $error_message = "Erro ao processar operação: " . $e->getMessage();
        }
    }
}

// Get all admin users
try {
    $stmt = $pdo->query("
        SELECT * FROM admin_users 
        ORDER BY created_at DESC
    ");
    $admin_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Erro ao carregar usuários administrativos.";
    $admin_users = [];
}

// Get user for editing if requested
$edit_user = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_message = "Erro ao carregar usuário para edição.";
    }
}
?>

<section class="card">
    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Gerenciar Usuários Administrativos</h2>
    
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
    
    <!-- Security Notice -->
    <div style="background: rgba(168, 200, 236, 0.2); color: var(--secondary-color); padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
        <h4 style="margin: 0 0 0.5rem 0;">⚠️ Aviso de Segurança</h4>
        <p style="margin: 0; font-size: 0.9rem;">
            Esta seção gerencia usuários com acesso total ao sistema. Tenha cuidado ao criar, editar ou excluir contas administrativas.
        </p>
    </div>
    
    <!-- Add/Edit User Form -->
    <div style="background: rgba(139, 154, 139, 0.1); padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
            <?= $edit_user ? 'Editar Usuário Administrativo' : 'Adicionar Novo Usuário Administrativo' ?>
        </h3>
        
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <input type="hidden" name="action" value="<?= $edit_user ? 'update' : 'create' ?>">
            <?php if ($edit_user): ?>
                <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Nome de Usuário *</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($edit_user['username'] ?? '') ?>"
                       placeholder="Ex: admin, dr.daniela">
            </div>
            
            <div class="form-group">
                <label for="full_name">Nome Completo *</label>
                <input type="text" id="full_name" name="full_name" required 
                       value="<?= htmlspecialchars($edit_user['full_name'] ?? '') ?>"
                       placeholder="Ex: Dra. Daniela Lima">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($edit_user['email'] ?? '') ?>"
                       placeholder="Ex: admin@terapiaebemestar.com.br">
            </div>
            
            <div class="form-group">
                <label for="password">
                    Senha <?= $edit_user ? '(deixe em branco para manter a atual)' : '*' ?>
                </label>
                <input type="password" id="password" name="password" 
                       <?= $edit_user ? '' : 'required' ?>
                       placeholder="Mínimo 6 caracteres">
                <small style="color: var(--text-light);">
                    <?= $edit_user ? 'Deixe em branco para não alterar a senha atual' : 'Use uma senha forte com pelo menos 6 caracteres' ?>
                </small>
            </div>
            
            <div style="grid-column: 1 / -1; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-large">
                    <?= $edit_user ? 'Atualizar Usuário' : 'Criar Usuário' ?>
                </button>
                
                <?php if ($edit_user): ?>
                    <a href="index.php?page=admin&action=admin_users" class="btn btn-secondary">Cancelar Edição</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Admin Users Table -->
    <?php if (count($admin_users) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Criado em</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admin_users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                                <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                    <br><small style="color: var(--primary-color);">(Você)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                            <td>
                                <span style="color: var(--success-color); font-weight: bold;">Ativo</span>
                            </td>
                            <td style="white-space: nowrap;">
                                <a href="index.php?page=admin&action=admin_users&edit=<?= $user['id'] ?>" 
                                   style="color: var(--primary-color); text-decoration: none; margin-right: 1rem;">
                                    Editar
                                </a>
                                
                                <?php if ($user['id'] != $_SESSION['admin_id'] && count($admin_users) > 1): ?>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('ATENÇÃO: Tem certeza que deseja excluir este usuário administrativo? Esta ação não pode ser desfeita.')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--warning-color); cursor: pointer;">
                                            Excluir
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                        <span style="color: var(--text-light); font-size: 0.9rem;">
                                            (Sua conta)
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-size: 0.9rem;">
                                            (Último admin)
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            Nenhum usuário administrativo encontrado.
        </p>
    <?php endif; ?>
</section>

<!-- Admin Users Statistics -->
<section class="card">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Informações de Segurança</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div style="background: rgba(139, 154, 139, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
            <h4 style="margin: 0; color: var(--primary-color);">Total de Admins</h4>
            <p style="margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold;"><?= count($admin_users) ?></p>
        </div>
        
        <div style="background: rgba(168, 200, 236, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
            <h4 style="margin: 0; color: var(--secondary-color);">Admin Atual</h4>
            <p style="margin: 0.5rem 0; font-size: 1.2rem; font-weight: bold;"><?= htmlspecialchars($_SESSION['admin_name']) ?></p>
        </div>
        
        <div style="background: rgba(212, 185, 150, 0.1); padding: 1rem; border-radius: 10px; text-align: center;">
            <h4 style="margin: 0; color: var(--accent-color);">Último Login</h4>
            <p style="margin: 0.5rem 0; font-size: 1.2rem; font-weight: bold;"><?= date('d/m/Y H:i') ?></p>
        </div>
    </div>
    
    <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.5); border-radius: 10px;">
        <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">💡 Dicas de Segurança</h4>
        <ul style="margin: 0; padding-left: 1rem; color: var(--text-light);">
            <li>Use senhas fortes com pelo menos 8 caracteres</li>
            <li>Não compartilhe credenciais administrativas</li>
            <li>Mantenha sempre pelo menos um usuário administrativo ativo</li>
            <li>Revise regularmente os acessos administrativos</li>
        </ul>
    </div>
</section>
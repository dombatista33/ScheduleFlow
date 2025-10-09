<?php
require_once __DIR__ . '/config.php';

$login_error = '';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, email FROM admin_users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_email'] = $user['email'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $login_error = 'Usuário ou senha incorretos';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $login_error = 'Erro ao processar login. Tente novamente.';
        }
    } else {
        $login_error = 'Por favor, preencha todos os campos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Terapia e Bem Estar</a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="hero">
                <h1>Acesso Administrativo</h1>
                <p class="subtitle">Entre com suas credenciais para acessar o painel</p>
            </section>

            <div style="max-width: 400px; margin: 0 auto;">
                <div class="card">
                    <?php if (isset($login_error)): ?>
                        <div style="background: rgba(255, 138, 101, 0.2); color: var(--warning-color); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                            <?= htmlspecialchars($login_error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Usuário</label>
                            <input type="text" id="username" name="username" required placeholder="Digite seu usuário">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password" required placeholder="Digite sua senha">
                        </div>
                        
                        <div style="text-align: center;">
                            <button type="submit" class="btn btn-large">Entrar</button>
                        </div>
                    </form>
                </div>
                
                <div style="text-align: center; margin-top: 1rem; color: var(--text-light);">
                    <small>Entre com seu usuário e senha de administrador</small>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
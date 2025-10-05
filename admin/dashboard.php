<?php
// Database-based admin authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            // Check credentials against admin_users table
            $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, email FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $admin['email'];
                header('Location: index.php?page=admin');
                exit;
            } else {
                $login_error = "Usuário ou senha incorretos.";
            }
        } catch(PDOException $e) {
            $login_error = "Erro interno do servidor. Tente novamente.";
        }
    }
    
    // Show login form
    include 'admin/login.php';
    return;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$action = $_GET['action'] ?? 'appointments';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <h1 style="color: white; margin: 0;">Painel Administrativo</h1>
                <div>
                    <span style="color: #ccc;">Olá, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrador') ?></span>
                    <a href="index.php?page=admin&logout=1" style="color: white; margin-left: 1rem; text-decoration: none;">[Sair]</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <nav class="admin-nav">
                <a href="index.php?page=admin&action=appointments" class="<?= $action === 'appointments' ? 'active' : '' ?>">Agendamentos</a>
                <a href="index.php?page=admin&action=calendar" class="<?= $action === 'calendar' ? 'active' : '' ?>">Gerenciar Agenda</a>
                <a href="index.php?page=admin&action=clients" class="<?= $action === 'clients' ? 'active' : '' ?>">Clientes</a>
                <a href="index.php?page=admin&action=services" class="<?= $action === 'services' ? 'active' : '' ?>">Serviços</a>
                <a href="index.php?page=admin&action=admin_users" class="<?= $action === 'admin_users' ? 'active' : '' ?>">Usuários Admin</a>
                <a href="admin/google_meet_guide.php" target="_blank" style="background: #ADD8E6; color: #333;">📱 Tutorial Google Meet</a>
                <a href="index.php" target="_blank">Ver Site</a>
            </nav>

            <?php
            switch($action) {
                case 'appointments':
                    include 'admin/appointments.php';
                    break;
                case 'calendar':
                    include 'admin/calendar_manage.php';
                    break;
                case 'clients':
                    include 'admin/clients.php';
                    break;
                case 'services':
                    include 'admin/services.php';
                    break;
                case 'admin_users':
                    include 'admin/admin_users.php';
                    break;
                default:
                    include 'admin/appointments.php';
            }
            ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Terapia e Bem Estar - Painel Administrativo</p>
        </div>
    </footer>
</body>
</html>
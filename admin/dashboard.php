<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Dra. Daniela Lima</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php
    // Simple admin authentication check
    if (!isset($_SESSION['admin_logged_in'])) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                header('Location: index.php?page=admin');
                exit;
            } else {
                $login_error = "Usuário ou senha incorretos.";
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

    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <h1 style="color: white; margin: 0;">Painel Administrativo</h1>
                <div>
                    <span style="color: #ccc;">Olá, Dra. Daniela Lima</span>
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
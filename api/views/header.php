<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/admin.js" defer></script>
</head>
<body>
    <!-- <?php
        // Временный код для отладки
        echo "isAdmin() returns: ";
        var_dump(isAdmin());
    ?> -->
    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <a href="/" class="logo">&lt;практика&gt;</a>
                
                <div class="nav-links">
                    <div class="auth-section">
                        <?php if (isAuthenticated()): ?>
                            <div class="user-menu">
                                <div class="user-menu-trigger">
                                    <span class="username"><?php echo escape($_SESSION['username']); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="dropdown-menu">
                                    <?php if (isAdmin()): ?>
                                        <a href="/admin" class="admin-link">Админ-панель</a>
                                        <div class="dropdown-divider"></div>
                                    <?php endif; ?>
                                    <a href="/profile">Мои курсы</a>
                                    <a href="/handlers/logout.php" class="logout-btn" onclick="return confirm('Вы уверены, что хотите выйти?')">Выйти</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="/auth" class="auth-btn">Войти</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?php echo $type; ?>" id="flash-message">
                <?php echo $message; ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <main class="main-content">
        <div class="container"> 
    <script>
    // Автоматическое скрытие сообщений
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.style.opacity = '0';
                setTimeout(function() {
                    flashMessage.remove();
                }, 300); // Время анимации исчезновения
            }, 3000); // 3 секунды до начала исчезновения
        }
    });
    </script>

    <style>
    .alert {
        padding: 15px;
        margin: 0 20px;
        position: fixed;
        text-align: center;
        top: 55px;
        right: 0px;
        left: 0px;
        z-index: 1000;
        min-width: 200px;
        transition: opacity 0.3s ease-in-out;
    }

    .alert-success {
        background: rgba(98, 245, 109, 0.2);
        backdrop-filter: blur(10px);
        color: #388e3c;
    }

    .alert-error {
        background-color: rgba(245, 98, 98, 0.2);
        backdrop-filter: blur(10px);
        color: #721c24;
    }
    </style> 
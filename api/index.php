<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

// Подключаем header
include 'views/header.php';

// Маршрутизация
switch (true) {
    case $path === '' || $path === 'index.php':
        include 'views/courses.php';
        break;

    case $path === 'auth':
        include 'views/auth.php';
        break;

    case $path === 'admin':
        if (!isAdmin()) {
            setFlashMessage('error', 'Доступ запрещен');
            header('Location: /');
            exit;
        }
        include 'views/admin/dashboard.php';
        break;

    case $path === 'admin/courses/new':
        if (!isAdmin()) {
            setFlashMessage('error', 'Доступ запрещен');
            header('Location: /');
            exit;
        }
        include 'views/admin/edit-course.php';
        break;

    case preg_match('/^admin\/courses\/edit\/(\d+)$/', $path, $matches):
        if (!isAdmin()) {
            setFlashMessage('error', 'Доступ запрещен');
            header('Location: /');
            exit;
        }
        include 'views/admin/edit-course.php';
        break;

    case $path === 'profile':
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Необходимо войти в систему');
            header('Location: /auth');
            exit;
        }
        include 'views/profile.php';
        break;

    case preg_match('/^course\/(\d+)$/', $path, $matches) === 1:
        $course_id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();
        
        if (!$course) {
            http_response_code(404);
            include 'views/404.php';
            break;
        }
        
        include 'views/course.php';
        break;

    default:
        http_response_code(404);
        include 'views/404.php';
        break;
}

// Подключаем footer
include 'views/footer.php';
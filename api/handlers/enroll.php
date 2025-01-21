<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAuthenticated()) {
    setFlashMessage('error', 'Необходимо войти в систему');
    header('Location: /auth');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    try {
        // Проверяем существование курса
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            throw new Exception('Курс не найден');
        }

        // Проверяем, не записан ли уже пользователь на этот курс
        $stmt = $pdo->prepare("SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$_SESSION['user_id'], $course_id]);
        
        if ($stmt->fetch()) {
            throw new Exception('Вы уже записаны на этот курс');
        }

        // Записываем пользователя на курс
        $stmt = $pdo->prepare("INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $course_id]);

        setFlashMessage('success', 'Вы успешно записались на курс');
        header('Location: /course/' . $course_id); // Перенаправляем обратно на страницу курса
        exit;

    } catch (Exception $e) {
        setFlashMessage('error', $e->getMessage());
        header('Location: /course/' . $course_id); // В случае ошибки тоже возвращаемся на страницу курса
        exit;
    }
}

// Если что-то пошло не так, возвращаемся на главную
header('Location: /');
exit;
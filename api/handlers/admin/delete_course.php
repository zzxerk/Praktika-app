<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;

    if (!$course_id) {
        throw new Exception('ID курса не указан');
    }

    // Проверяем существование курса
    $stmt = $pdo->prepare("SELECT image_path FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        throw new Exception('Курс не найден');
    }

    // Удаляем изображение курса, если оно есть
    if ($course['image_path']) {
        $image_path = $_SERVER['DOCUMENT_ROOT'] . $course['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Удаляем записи пользователей на курс
    $stmt = $pdo->prepare("DELETE FROM user_courses WHERE course_id = ?");
    $stmt->execute([$course_id]);

    // Удаляем сам курс
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);

    echo json_encode(['success' => true, 'message' => 'Курс успешно удален']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
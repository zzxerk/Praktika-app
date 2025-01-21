<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Проверяем, что запрос AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    exit('Direct access not permitted');
}

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$query = "SELECT * FROM courses WHERE 1=1";
$params = [];

// Применяем фильтры
if ($category !== 'all') {
    if ($category === 'free') {
        $query .= " AND is_free = 1";
    } else {
        $query .= " AND category = ?";
        $params[] = $category;
    }
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Отправляем JSON ответ
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Ошибка при получении курсов'
    ]);
} 
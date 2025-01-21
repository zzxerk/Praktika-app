<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!isAdmin()) {
    setFlashMessage('error', 'Доступ запрещен');
    header('Location: /');
    exit;
}

try {
    $material_id = isset($_POST['material_id']) ? (int)$_POST['material_id'] : null;
    $course_id = (int)$_POST['course_id'];
    
    $data = [
        'course_id' => $course_id,
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'order_number' => (int)$_POST['order_number']
    ];

    if ($material_id) {
        // Обновление существующего материала
        $sql = "UPDATE course_materials SET 
                title = :title, 
                content = :content, 
                order_number = :order_number 
                WHERE id = :id AND course_id = :course_id";
        $data['id'] = $material_id;
    } else {
        // Создание нового материала
        $sql = "INSERT INTO course_materials (course_id, title, content, order_number) 
                VALUES (:course_id, :title, :content, :order_number)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    setFlashMessage('success', $material_id ? 'Материал обновлен' : 'Материал добавлен');
    header('Location: /admin/courses/edit/' . $course_id);
    exit;

} catch (Exception $e) {
    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
    header('Location: /admin/courses/edit/' . $course_id);
    exit;
} 
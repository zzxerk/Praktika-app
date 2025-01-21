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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin');
}

try {
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'duration' => $_POST['duration'],
        'is_free' => $is_free,
        'price_monthly' => $is_free ? 0 : $_POST['price_monthly'],
        'price_full' => $is_free ? 0 : $_POST['price_full']
    ];

    // Обработка загруженного изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/courses/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $data['image_path'] = '/uploads/courses/' . $new_filename;
        }
    }

    if ($course_id) {
        // Обновление существующего курса
        $sql_parts = [];
        foreach ($data as $key => $value) {
            $sql_parts[] = "$key = :$key";
        }
        $sql = "UPDATE courses SET " . implode(', ', $sql_parts) . " WHERE id = :id";
        $data['id'] = $course_id;
    } else {
        // Создание нового курса
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map(function($item) { return ":$item"; }, array_keys($data)));
        $sql = "INSERT INTO courses ($columns) VALUES ($values)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    setFlashMessage('success', $course_id ? 'Курс успешно обновлен' : 'Курс успешно создан');
    header('Location: /admin');
    exit;

} catch (Exception $e) {
    setFlashMessage('error', 'Ошибка: ' . $e->getMessage());
    header('Location: /admin');
    exit;
} 
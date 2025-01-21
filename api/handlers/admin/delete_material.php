<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $material_id = isset($data['material_id']) ? (int)$data['material_id'] : 0;

    if (!$material_id) {
        throw new Exception('ID материала не указан');
    }

    $stmt = $pdo->prepare("DELETE FROM course_materials WHERE id = ?");
    $stmt->execute([$material_id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
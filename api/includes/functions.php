<?php
/**
 * Файл с вспомогательными функциями
 */

/**
 * Проверка авторизации пользователя
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Получение информации о текущем пользователе
 */
function getCurrentUser() {
    global $pdo;
    if (!isAuthenticated()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Безопасный вывод данных
 */
function escape($str) {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Получение списка курсов с фильтрацией
 */
function getCourses($category = null, $is_free = null) {
    global $pdo;
    
    $where = [];
    $params = [];
    
    if ($category) {
        $where[] = "category = ?";
        $params[] = $category;
    }
    
    if ($is_free !== null) {
        $where[] = "is_free = ?";
        $params[] = $is_free;
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $stmt = $pdo->prepare("SELECT * FROM courses {$whereClause}");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Получение курсов пользователя
 */
function getUserCourses($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT c.*, uc.progress 
        FROM courses c 
        JOIN user_courses uc ON c.id = uc.course_id 
        WHERE uc.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Обновление прогресса по курсу
 */
function updateCourseProgress($user_id, $course_id, $progress) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE user_courses 
        SET progress = ? 
        WHERE user_id = ? AND course_id = ?
    ");
    return $stmt->execute([$progress, $user_id, $course_id]);
}

/**
 * Записать пользователя на курс
 */
function enrollUserToCourse($user_id, $course_id) {
    global $pdo;
    
    // Проверяем, не записан ли уже пользователь на курс
    $stmt = $pdo->prepare("
        SELECT id FROM user_courses 
        WHERE user_id = ? AND course_id = ?
    ");
    $stmt->execute([$user_id, $course_id]);
    
    if ($stmt->rowCount() > 0) {
        return false;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO user_courses (user_id, course_id, progress) 
        VALUES (?, ?, 0)
    ");
    return $stmt->execute([$user_id, $course_id]);
}

/**
 * Форматирование цены
 */
function formatPrice($price) {
    return number_format($price, 0, '', ' ') . ' ₽';
}

/**
 * Генерация URL для страницы
 */
function generateUrl($path = '', $params = []) {
    $url = SITE_URL . '/' . ltrim($path, '/');
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

/**
 * Редирект на другую страницу
 */
function redirect($path = '', $params = []) {
    header('Location: ' . generateUrl($path, $params));
    exit;
}

/**
 * Установка флеш-сообщения
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Получение и удаление флеш-сообщения
 */
function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Проверка прав доступа к курсу
 */
function canAccessCourse($user_id, $course_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT c.is_free, uc.id as enrollment_id
        FROM courses c
        LEFT JOIN user_courses uc ON c.id = uc.course_id AND uc.user_id = ?
        WHERE c.id = ?
    ");
    $stmt->execute([$user_id, $course_id]);
    $result = $stmt->fetch();
    
    return $result['is_free'] || !empty($result['enrollment_id']);
}

/**
 * Валидация данных формы
 */
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        if (isset($rule['required']) && $rule['required'] && empty($data[$field])) {
            $errors[$field] = 'Поле обязательно для заполнения';
        }
        
        if (isset($rule['min']) && strlen($data[$field]) < $rule['min']) {
            $errors[$field] = "Минимальная длина поля {$rule['min']} символов";
        }
        
        if (isset($rule['max']) && strlen($data[$field]) > $rule['max']) {
            $errors[$field] = "Максимальная длина поля {$rule['max']} символов";
        }
    }
    
    return $errors;
}

/**
 * Проверка прав доступа пользователя
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
} 
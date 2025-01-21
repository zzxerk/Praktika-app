<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'login') {
            if (empty($username) || empty($password)) {
                throw new Exception('Все поля обязательны для заполнения');
            }

            $stmt = $pdo->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                throw new Exception('Неверный логин или пароль');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'] == 1;

            setFlashMessage('success', 'Вы успешно вошли в систему');
            header('Location: /');
            exit;
        } elseif ($action === 'register') {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = 'Пользователь уже существует';
                header('Location: /auth');
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: /');
            exit;
        }
    } catch (Exception $e) {
        setFlashMessage('error', $e->getMessage());
        header('Location: /auth');
        exit;
    }
}

header('Location: /');
exit;
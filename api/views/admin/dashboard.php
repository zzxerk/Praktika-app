<?php
if (!isAdmin()) {
    setFlashMessage('error', 'Доступ запрещен');
    header('Location: /');
    exit;
}
?>

<div class="admin-dashboard">
    <div class="container">
        <h1>Панель администратора</h1>
        
        <div class="admin-actions">
            <a href="/admin/courses/new" class="btn btn-primary">Добавить новый курс</a>
        </div>

        <div class="courses-list">
            <h2>Управление курсами</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена (месяц)</th>
                        <th>Цена (полная)</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
                    $courses = $stmt->fetchAll();
                    
                    foreach ($courses as $course):
                    ?>
                    <tr>
                        <td><?php echo $course['id']; ?></td>
                        <td><?php echo escape($course['title']); ?></td>
                        <td><?php echo escape($course['category']); ?></td>
                        <td><?php echo $course['is_free'] ? 'Бесплатно' : escape($course['price_monthly']) . ' ₽'; ?></td>
                        <td><?php echo $course['is_free'] ? 'Бесплатно' : escape($course['price_full']) . ' ₽'; ?></td>
                        <td>
                            <a href="/admin/courses/edit/<?php echo $course['id']; ?>" class="btn btn-sm btn-edit">Редактировать</a>
                            <button onclick="deleteCourse(<?php echo $course['id']; ?>)" class="btn btn-sm btn-danger">Удалить</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 20px 0;
}

.admin-actions {
    margin-bottom: 20px;
    margin-top: 20px;
}

.courses-list {
    color: #262626;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
}

.admin-table th,
.admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px dashed #e9ecef;
}

.admin-table th {
    background-color: #fff;
    font-weight: bold;
}

.btn-sm {
    padding: 8px 0;
    font-size: 14px;
    margin-right: 5px;
}

.btn-edit {
    background-color: #fff;
    color: #388e3c;
    border: none;
    text-decoration: none;
}

.btn-danger {
    background-color: #fff;
    color: #262626;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: #262626;
    color: white;
    text-decoration: none;
    padding: 12px 24px;
    display: inline-block;
    font-size: 16px;
    font-weight: 500;
}
</style> 
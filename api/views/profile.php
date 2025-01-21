<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth');
    exit;
}

// Получаем курсы пользователя
$stmt = $pdo->prepare("
    SELECT c.*, uc.progress 
    FROM courses c 
    JOIN user_courses uc ON c.id = uc.course_id 
    WHERE uc.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$userCourses = $stmt->fetchAll();
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Мои курсы</h1>
        <!-- <div class="profile-actions">
            <a href="#" class="edit-profile">Редактировать</a>
            <a href="/handlers/logout.php" class="logout">Выйти</a>
        </div> -->
    </div>

    <div class="user-courses">
        <?php if (empty($userCourses)): ?>
            <p>У вас пока нет курсов. <a href="/">Выберите курс</a></p>
        <?php else: ?>
            <?php foreach ($userCourses as $course): ?>
                <div class="course-card">
                    <div class="course-info">
                        <span class="category"><?php echo $course['category']; ?> • <?php echo $course['duration']; ?></span>
                        <h3 class="course-title"><?php echo $course['title']; ?></h3>
                        <!-- <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $course['progress']; ?>%"></div>
                        </div> -->
                        <a href="/course/<?php echo $course['id']; ?>" class="continue-btn">Продолжить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div> 
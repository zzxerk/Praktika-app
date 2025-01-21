<?php
if (!isset($course_id)) {
    $course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

// Получаем информацию о курсе и статус записи
$stmt = $pdo->prepare("
    SELECT c.*, 
           CASE WHEN uc.user_id IS NOT NULL THEN true ELSE false END as is_enrolled,
           uc.progress
    FROM courses c
    LEFT JOIN user_courses uc ON c.id = uc.course_id AND uc.user_id = ?
    WHERE c.id = ?
");
$stmt->execute([isAuthenticated() ? $_SESSION['user_id'] : null, $course_id]);
$course = $stmt->fetch();

if (!$course) {
    setFlashMessage('error', 'Курс не найден');
    redirect('');
}

// Получаем материалы курса, если пользователь записан
$materials = [];
if ($course['is_enrolled']) {
    $stmt = $pdo->prepare("
        SELECT * FROM course_materials 
        WHERE course_id = ? 
        ORDER BY order_number
    ");
    $stmt->execute([$course_id]);
    $materials = $stmt->fetchAll();
}
?>

<div class="course-page">
    <div class="course-header">
            <h1><?php echo escape($course['title']); ?></h1>
            <div class="course-meta">
                <span class="category"><?php echo $course['category']; ?> • <?php echo $course['duration']; ?></span>
            </div>
    </div>

    
        <div class="course-content">
            <?php if ($course['is_enrolled']): ?>
                <!-- Контент для записанных пользователей -->
                <div class="course-materials">
                    <!-- <div class="course-progress">
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $course['progress']; ?>%"></div>
                        </div>
                        <span class="progress-text"><?php echo $course['progress']; ?>% пройдено</span>
                    </div> -->

                    <div class="materials-list">
                        <!-- <h2>Материалы курса</h2> -->
                        <?php foreach ($materials as $material): ?>
                            <div class="material-item">
                                <h3 class="material-title"><?php echo escape($material['title']); ?></h3>
                                <div class="material-content">
                                    <?php echo nl2br(escape($material['content'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Информация о курсе для незаписанных пользователей -->
                <div class="course-info-wrapper">
                    <div class="course-description">
                        <h2>О курсе</h2>
                        <p><?php echo nl2br(escape($course['description'] ?? 'Описание курса пока не добавлено.')); ?></p>
                    </div>
                    
                    <div class="course-enrollment">
                        <?php if ($course['is_free']): ?>
                            <div class="price">Бесплатно</div>
                        <?php else: ?>
                            <div class="price">
                                <div class="monthly">от <?php echo formatPrice($course['price_monthly']); ?>/мес</div>
                                <div class="full-price">или <?php echo formatPrice($course['price_full']); ?> сразу</div>
                            </div>
                        <?php endif; ?>

                        <?php if (isAuthenticated()): ?>
                            <form action="/handlers/enroll.php" method="POST">
                                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                <button type="submit" class="enroll-btn">Записаться на курс</button>
                            </form>
                        <?php else: ?>
                            <a href="/auth" class="enroll-btn">Войдите, чтобы записаться</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
</div> 
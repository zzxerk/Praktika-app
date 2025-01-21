<?php
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$query = "SELECT * FROM courses WHERE 1=1";

if ($category !== 'all') {
    $query .= " AND category = :category";
}

$stmt = $pdo->prepare($query);
if ($category !== 'all') {
    $stmt->bindParam(':category', $category);
}
$stmt->execute();
$courses = $stmt->fetchAll();
?>

<div class="courses-container">
    <h1>Курсы для новичков</h1>
    
    <!-- Фильтры -->
    <div class="filters">
        <button class="filter-btn active" data-category="all">Все курсы</button>
        <button class="filter-btn" data-category="Программирование">Программирование</button>
        <button class="filter-btn" data-category="Дизайн">Дизайн</button>
        <button class="filter-btn" data-category="Анализ данных">Анализ данных</button>
        <button class="filter-btn" data-category="Маркетинг">Маркетинг</button>
        <button class="filter-btn" data-category="free">Бесплатно</button>
    </div>

    <!-- Курсы -->
    <div class="courses-grid">
        <?php foreach ($courses as $course): ?>
            <a href="/course/<?php echo $course['id']; ?>" class="course-card" data-category="<?php echo escape($course['category']); ?>">
                <div class="course-info">
                    <div class="course-meta">
                        <span class="duration"><?php echo escape($course['duration']); ?></span>
                        <span class="category-badge">
                            <?php
                            $categories = [
                                'Программирование' => 'Программирование',
                                'Дизайн' => 'Дизайн',
                                'Анализ данных' => 'Анализ данных',
                                'Маркетинг' => 'Маркетинг'
                            ];
                            echo escape($categories[$course['category']] ?? $course['category']);
                            ?>
                        </span>
                    </div>
                    <h3 class="course-title"><?php echo escape($course['title']); ?></h3>
                    <?php if ($course['is_free']): ?>
                        <div class="price">бесплатно</div>
                    <?php else: ?>
                        <div class="price">
                            от <?php echo formatPrice($course['price_monthly']); ?>/мес
                            <div class="full-price">
                                или сразу <?php echo formatPrice($course['price_full']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div> 
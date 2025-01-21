<?php
if (!isAdmin()) {
    setFlashMessage('error', 'Доступ запрещен');
    header('Location: /');
    exit;
}

$course_id = isset($matches[1]) ? (int)$matches[1] : null;
$course = null;

if ($course_id) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        setFlashMessage('error', 'Курс не найден');
        header('Location: /admin');
        exit;
    }
}
?>

<div class="admin-edit-course">
    
        <h1><?php echo $course ? 'Редактирование курса' : 'Создание курса'; ?></h1>
        
        <form action="/handlers/admin/save_course.php" method="POST" enctype="multipart/form-data">
            <?php if ($course): ?>
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Название курса</label>
                <input type="text" name="title" value="<?php echo $course ? escape($course['title']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Описание</label>
                <textarea name="description" required rows="5"><?php echo $course ? escape($course['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label>Категория</label>
                <select name="category" required>
                    <?php
                    $categories = [
                        'Программирование' => 'Программирование',
                        'Дизайн' => 'Дизайн',
                        'Анализ данных' => 'Анализ данных',
                        'Маркетинг' => 'Маркетинг'
                    ];
                    foreach ($categories as $value => $label):
                        $selected = $course && $course['category'] === $value ? 'selected' : '';
                    ?>
                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Длительность (например: "3 месяца")</label>
                <input type="text" name="duration" value="<?php echo $course ? escape($course['duration']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_free" <?php echo $course && $course['is_free'] ? 'checked' : ''; ?>>
                    Бесплатный курс
                </label>
            </div>

            <div class="price-fields" <?php echo $course && $course['is_free'] ? 'style="display:none;"' : ''; ?>>
                <div class="form-group">
                    <label>Цена в месяц</label>
                    <input type="number" name="price_monthly" value="<?php echo $course ? $course['price_monthly'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Полная стоимость</label>
                    <input type="number" name="price_full" value="<?php echo $course ? $course['price_full'] : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Изображение курса</label>
                <?php if ($course && $course['image_path']): ?>
                    <div class="current-image">
                        <img src="<?php echo $course['image_path']; ?>" alt="Текущее изображение" style="max-width: 200px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" <?php echo !$course ? 'required' : ''; ?>>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="/admin" class="btn btn-secondary">Отмена</a>
            </div>
        </form>

        <?php if ($course): ?>
        <div class="course-materials">
            <h2>Материалы курса</h2>
            
            <form action="/handlers/admin/save_material.php" method="POST" class="material-form">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                <div class="form-group">
                    <label>Название материала</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Содержание</label>
                    <textarea name="content" required rows="10"></textarea>
                </div>
                <div class="form-group">
                    <label>Порядковый номер</label>
                    <input type="number" name="order_number" value="0" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Добавить материал</button>
            </form>

            <div class="materials-list">
                <h3>Существующие материалы</h3>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM course_materials WHERE course_id = ? ORDER BY order_number");
                $stmt->execute([$course['id']]);
                $materials = $stmt->fetchAll();
                
                if ($materials):
                ?>
                    <div class="materials-grid">
                        <?php foreach ($materials as $material): ?>
                        <div class="materials-item" data-id="<?php echo $material['id']; ?>">
                            <div class="material-header">
                                <span class="material-title"><?php echo escape($material['title']); ?></span>
                                <div class="material-actions">
                                    <button type="button" class="btn btn-sm btn-edit" onclick="editMaterial(<?php echo $material['id']; ?>)">
                                        Редактировать
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteMaterial(<?php echo $material['id']; ?>)">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                            <div class="material-content" style="display: none;">
                                <?php echo nl2br(escape($material['content'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Материалы пока не добавлены</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
</div>

<style>
.admin-edit-course {
    padding: 0 0;
    color: #262626;
}

.admin-edit-course h1 {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px dashed #e9ecef;
    outline: none;
    background: none;
}

.form-actions {
    margin-top: 30px;
}

.btn {
    padding: 10px 24px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-right: 10px;
}

.btn-primary {
    background-color: #262626;
    color: white;
    font-size: 16px;
    font-weight: 500;
}

.btn-secondary {
    background-color: none;
    color: #262626;
}

.current-image {
    margin: 10px 0;
}

.course-materials {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed #e9ecef;
    background: none;
}

.material-form {
    background: #f8f9fa;
    padding: 20px 0;
    border-radius: 8px;
    margin-bottom: 30px;
}

.materials-grid {
    display: grid;
    gap: 20px;
}

.materials-list h3 {
    margin-bottom: 20px;
    color: #262626;
}

.materials-item {
    border: 1px dashed #e9ecef;
    padding: 15px;
    background: white;
}

.material-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.material-title {
    font-weight: bold;
    font-size: 1.1em;
}

.material-actions {
    display: flex;
    gap: 10px;
}

.material-content {
    /* padding: 10px; */
    /* background: #f8f9fa; */
    margin-top: 10px;
}
</style>

<script>
function editMaterial(materialId) {
    fetch(`/handlers/admin/get_material.php?id=${materialId}`)
        .then(response => response.json())
        .then(material => {
            const form = document.querySelector('.material-form');
            form.querySelector('input[name="title"]').value = material.title;
            form.querySelector('textarea[name="content"]').value = material.content;
            form.querySelector('input[name="order_number"]').value = material.order_number;
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'material_id';
            hiddenInput.value = material.id;
            form.appendChild(hiddenInput);
            
            form.scrollIntoView({ behavior: 'smooth' });
        });
}

function deleteMaterial(materialId) {
    if (confirm('Вы уверены, что хотите удалить этот материал?')) {
        fetch('/handlers/admin/delete_material.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ material_id: materialId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.material-title').forEach(title => {
        title.addEventListener('click', function() {
            const content = this.closest('.materials-item').querySelector('.material-content');
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });
    });
});
</script> 
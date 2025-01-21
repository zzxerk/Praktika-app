function deleteCourse(courseId) {
    if (confirm('Вы уверены, что хотите удалить этот курс?')) {
        fetch('/handlers/admin/delete_course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ course_id: courseId })
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

// Обработка переключения полей цены при изменении checkbox is_free
document.addEventListener('DOMContentLoaded', function () {
    const isFreeCheckbox = document.querySelector('input[name="is_free"]');
    const priceFields = document.querySelector('.price-fields');

    if (isFreeCheckbox && priceFields) {
        isFreeCheckbox.addEventListener('change', function () {
            priceFields.style.display = this.checked ? 'none' : 'block';
        });
    }
}); 
document.addEventListener('DOMContentLoaded', function () {
    // Обработка форм авторизации
    const showLoginBtn = document.getElementById('showLogin');
    const showRegisterBtn = document.getElementById('showRegister');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Проверяем наличие форм перед добавлением обработчиков
    if (loginForm && registerForm && showLoginBtn && showRegisterBtn) {
        const loginFormContainer = loginForm.parentElement;
        const registerFormContainer = registerForm.parentElement;

        showLoginBtn.addEventListener('click', function (e) {
            e.preventDefault();
            registerFormContainer.style.display = 'none';
            loginFormContainer.style.display = 'block';
        });

        showRegisterBtn.addEventListener('click', function (e) {
            e.preventDefault();
            loginFormContainer.style.display = 'none';
            registerFormContainer.style.display = 'block';
        });
    }

    // Обработка меню пользователя
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        const dropdownMenu = userMenu.querySelector('.dropdown-menu');

        userMenu.addEventListener('click', function (e) {
            e.stopPropagation(); // Предотвращаем всплытие события
            this.classList.toggle('active');
            if (dropdownMenu) {
                dropdownMenu.style.display = this.classList.contains('active') ? 'block' : 'none';
            }
        });

        // Закрытие меню при клике вне его
        document.addEventListener('click', function (e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
                if (dropdownMenu) {
                    dropdownMenu.style.display = 'none';
                }
            }
        });
    }

    // Фильтрация курсов
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Убираем активный класс у всех кнопок
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Добавляем активный класс текущей кнопке
                this.classList.add('active');

                const category = this.dataset.category;

                // Отправляем AJAX запрос для получения отфильтрованных курсов
                fetch(`/handlers/filter_courses.php?category=${category}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const coursesGrid = document.querySelector('.courses-grid');
                        if (coursesGrid) {
                            coursesGrid.innerHTML = ''; // Очищаем текущие курсы

                            // Добавляем отфильтрованные курсы
                            data.courses.forEach(course => {
                                const priceHtml = course.is_free
                                    ? '<div class="price">бесплатно</div>'
                                    : `<div class="price">
                                        от ${formatPrice(course.price_monthly)}/мес
                                        <div class="full-price">
                                            или сразу ${formatPrice(course.price_full)}
                                        </div>
                                      </div>`;

                                // Оборачиваем карточку курса в ссылку
                                coursesGrid.innerHTML += `
                                    <a href="/course/${course.id}" class="course-card" data-category="${course.category}">
                                        
                                        <div class="course-info">
                                            <div class="course-meta">
                                                <span class="duration">${course.duration || ''}</span>
                                                <span class="category-badge">${getCategoryName(course.category)}</span>
                                            </div>
                                            <h3 class="course-title">${course.title}</h3>
                                            ${priceHtml}
                                        </div>
                                    </a>
                                `;
                            });
                        }
                    })
                    .catch(error => console.error('Ошибка:', error));
            });
        });
    }

    // Функция форматирования цены
    function formatPrice(price) {
        return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
    }

    // Добавим функцию для получения названия категории
    function getCategoryName(category) {
        const categories = {
            'Программирование': 'Программирование',
            'Дизайн': 'Дизайн',
            'Анализ данных': 'Анализ данных',
            'Маркетинг': 'Маркетинг'
        };
        return categories[category] || category;
    }
}); 
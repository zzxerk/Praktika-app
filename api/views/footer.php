        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <a href="/" class="logo">&lt;практика&gt;</a>
                    <p class="copyright">&copy; <?php echo date('Y'); ?> Все права защищены</p>
                </div>
                
                <div class="footer-nav">
                    <div class="footer-col">
                        <h4>Курсы</h4>
                        <ul>
                            <li><a href="/?category=programming">Программирование</a></li>
                            <li><a href="/?category=analysis">Анализ данных</a></li>
                            <li><a href="/?category=design">Дизайн</a></li>
                            <li><a href="/?category=free">Бесплатные курсы</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-col">
                        <h4>Информация</h4>
                        <ul>
                            <li><a href="/about">О нас</a></li>
                            <li><a href="/contact">Контакты</a></li>
                            <li><a href="/terms">Условия использования</a></li>
                            <li><a href="/privacy">Политика конфиденциальности</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="/assets/js/main.js"></script>
    
    <?php if (isAuthenticated()): ?>
    <script>
        // Дополнительный JavaScript для авторизованных пользователей
        document.addEventListener('DOMContentLoaded', function() {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu) {
                userMenu.addEventListener('click', function(e) {
                    this.classList.toggle('active');
                });

                // Закрытие меню при клике вне его
                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target)) {
                        userMenu.classList.remove('active');
                    }
                });
            }
        });
    </script>
    <?php endif; ?>

</body>
</html> 
<div class="auth-modal">
    <h2>Регистрация</h2>
    <form id="registerForm" method="POST" action="/handlers/auth_handler.php">
        <input type="text" name="username" placeholder="Имя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" name="action" value="register">Зарегистрироваться</button>
    </form>
    <p>Уже есть профиль? <a href="#" id="showLogin">Войти</a></p>
</div>

<div class="auth-modal" style="display: none;">
    <h2>Вход</h2>
    <form id="loginForm" method="POST" action="/handlers/auth_handler.php">
        <input type="text" name="username" placeholder="Имя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" name="action" value="login">Войти</button>
    </form>
    <p>Нет профиля? <a href="#" id="showRegister">Зарегистрироваться</a></p>
</div> 
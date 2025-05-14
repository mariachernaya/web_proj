<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>Задание_5</title>
  </head>

  <body>
    <footer class="footer">
    <div class="container">
        <div>

              <div class="form-buttons">
        <buttonf id="showRegisterBtn">Регистрация</buttonf>
        <buttonf id="showLoginBtn">Войти</buttonf>
    </div>

    <!-- Форма регистрации -->
    <form id="registerForm" class="form-section active-form">
        <h2 style="margin-top: 5px">Регистрация</h2>
        <div class="form-group">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="userMessage">Сообщение:</label>
            <textarea id="userMessage" name="message" rows="4" required></textarea>
        </div>
        
        <button type="submit" style=" background-color: #2a2c3a; color: white; padding: 10px 15px; border: none;
            border-radius: 45%; cursor: pointer; margin-right: 10px;">Зарегистрироваться</button>
    </form>
		    <!-- Форма входа -->
    <form id="loginForm" class="form-section">
        <h2 style="margin-top: 5px">Вход</h2>
        <div class="form-group">
            <label for="loginUsername">Логин:</label>
            <input type="text" id="loginUsername" name="login" required>
        </div>
        
        <div class="form-group">
            <label for="loginPassword">Пароль:</label>
            <input type="password" id="loginPassword" name="password" required>
        </div>
        
        <button type="submit" style=" background-color: #2a2c3a; color: white; padding: 10px 15px; border: none;
            border-radius: 45%; cursor: pointer; margin-right: 10px;">Войти</button>
    </form>
    
    <!-- Форма редактирования -->
    <form id="editForm" class="form-section">
        <h2 style="margin-top: 5px">Редактирование данных</h2>
        <div class="form-group">
            <label for="editName">Имя:</label>
            <input type="text" id="editName" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="editMessage">Сообщение:</label>
            <textarea id="editMessage" name="message" rows="4" required></textarea>
        </div>
        
        <button type="submit" style=" background-color: #2a2c3a; color: white; padding: 10px 15px; border: none;
            border-radius: 45%; cursor: pointer; margin-right: 10px;">Сохранить изменения</button>
        <buttonf type="button" id="logoutBtn">Выйти</buttonf>
    </form>
		    <div id="responseContainer"></div>

    <script>
        
    </script>
            <section id="block-copyright" class="block clear">
                <h6>&nbsp;</h6>
                <div class="fpt-56   "><p>Проект ООО «Инитлаб», Краснодар, Россия. <br>
                    Drupal является зарегистрированной торговой маркой Dries Buytaert.</p></div>
            </section>


        </div>

    </div>
</footer>
	  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="form.js">
	
</script>
  </body>
</html>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>Задание_6</title>
    <!-- Добавляем jQuery для удобства работы с DOM и AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <form id="mainForm" class="form">
        <div class="head">
            <h2><b>Форма обратной связи</b></h2>
        </div>

        <!-- Блоки для сообщений -->
        <div class="mess" id="successMessage"></div>
        <div class="mess mess_info" id="infoMessage"></div>
        
        <!-- Поля формы (остаются без изменений, но добавляем ID для удобства) -->
        <div>
            <label> 
                <input name="fio" id="fio" class="input" type="text" placeholder="ФИО" /> 
            </label>
            <div class="error" id="fioError"></div>
        </div>
    <div>
      <label> 
        <input name="number" id="number" class="input" type="tel" placeholder="Номер телефона" /> 
      </label>
      <div class="error" id="numberError"></div>
    </div>

    <div>
      <label> <input name="email" class="input" type="text" placeholder="Почта" /> 
      </label>
      <div class="error" id="emailError"></div>
    </div>
    <div>
      <label>
        <input name="date" class="input" type="date" />
      </label>
       <div class="error" id="dateError"></div>
    </div>

    <div>
      <div>Пол</div>
      <div class="mb-1">
        <label>
          <input name="radio" class="ml-2" type="radio" value="M"/>
          <span> Мужской </span>
        </label>
        <label>
          <input name="radio" class="ml-4" type="radio" value="W"/>
          <span> Женский </span>
        </label>
      </div>
       <div class="error" id="radioError"></div>
    </div>

    <div>
      <div>Любимый язык программирования</div>
      <select class="my-2" name="language[]" multiple="multiple">
        <option value="Pascal" >Pascal</option>
        <option value="C" >C</option>
        <option value="C++">C++</option>
        <option value="JavaScript">JavaScript</option>
        <option value="PHP">PHP</option>
        <option value="Python">Python</option>
        <option value="Java">Java</option>
        <option value="Haskel">Haskel</option>
        <option value="Clojure">Clojure</option>
        <option value="Scala">Scala</option>
      </select>
      <div class="error" id="languageError"></div>
    </div>

    <div class="my-2">
      <div>Биография</div>
      <label>
        <textarea name="bio" class="input"placeholder="Биография"> </textarea>
      </label>
       <div class="error" id="bioError"></div>
    </div>

    <div>
      <label>
          <input name="check" type="checkbox" value="1" />
        С контрактом ознакомлен(а)
      </label>
       <div class="error" id="checkError"></div>
    </div>

        <button class="button" type="submit">Отправить</button>
        <!-- Кнопки входа/выхода обрабатываются через JS -->
    <div id="authButtons">
    <?php
    if ($log)
        echo '<button class="button edbut" type="button" onclick="logout()">Выйти</button>';
    else
        echo '<a class="btnlike" href="login.php">Войти</a>';
    ?>
</div>
    </form>

   <script>
    $(document).ready(function() {
        // Функция для обновления сообщений и полей
        function updateUI() {
            // Заполняем поля из cookies
            $('#fio').val(getCookie('fio_value') || '');
            $('#number').val(getCookie('number_value') || '');
            $('input[name="email"]').val(getCookie('email_value') || '');
            $('input[name="date"]').val(getCookie('date_value') || '');
            // Радио-кнопки
            const radioValue = getCookie('radio_value');
            if (radioValue) $(`input[name="radio"][value="${radioValue}"]`).prop('checked', true);
            // Множественный выбор языков
            const langs = (getCookie('language_value') || '').split(',');
            $('select[name="language[]"] option').each(function() {
                $(this).prop('selected', langs.includes(this.value));
            });
            // Биография
            $('textarea[name="bio"]').val(getCookie('bio_value') || '');
            // Чекбокс
            $('input[name="check"]').prop('checked', !!getCookie('check_value'));
        }

        // Обработка отправки формы
        $('#mainForm').submit(async function(e) {
            e.preventDefault();
            
            // Собираем данные формы
            const formData = new FormData(this);
            
            // Для чекбокса (если не отмечен - добавляем пустое значение)
            if (!$('input[name="check"]').prop('checked')) {
                formData.set('check', '');
            }

            try {
                
const response = await fetch('index.php', {
    method: 'POST',
    body: formData,
    credentials: 'include',
    headers: {
        'X-Requested-With': 'XMLHttpRequest' // Добавьте эту строку
    }
});
                if (response.redirected) {
                    window.location.href = response.url; // Редирект при успехе
                } else {
                    const text = await response.text();
                    parseCookies();
                    showMessages();
                    updateUI();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        function parseCookies() {
            // Ошибки
            const fields = ['fio', 'number', 'email', 'date', 'radio', 'language', 'bio', 'check'];
            fields.forEach(field => {
                const error = getCookie(`${field}_error`);
                if (error) $(`#${field}Error`).text(error);
            });

            // Успешные сообщения
            if (getCookie('save')) {
                $('#successMessage').text('Спасибо, результаты сохранены.');
                if (getCookie('pass')) {
                    $('#infoMessage').html(
                        `Вы можете <a href="login.php">войти</a> с логином <strong>${getCookie('login')}</strong> и паролем <strong>${getCookie('pass')}</strong>`
                    );
                }
            }
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            return parts.length === 2 ? decodeURIComponent(parts.pop().split(';').shift()) : null;
        }

        // Инициализация
        updateUI();
        parseCookies();
    });

    function logout() {
        fetch('index.php', {
            method: 'POST',
            body: new URLSearchParams({ 'logout_form': '1' }),
            credentials: 'include'
        }).then(() => window.location.reload());
    }
</script>
</body>
</html>

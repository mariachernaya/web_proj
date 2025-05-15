<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>project</title>
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
            <label> <input name="fio"  id="fio"  class="input <?php echo ($errors['fio'] != NULL) ? 'red' : ''; ?>"
          value="<?php echo $values['fio']; ?>" type="text" placeholder="ФИО" /> 
            </label>
            <div class="error" id="fioError"></div>
        </div>
    <div>
        <label> <input name="number" id="number" class="input <?php echo ($errors['number'] != NULL) ? 'red' : ''; ?>"
          value="<?php echo $values['number']; ?>" type="tel" placeholder="Номер телефона" /> </label>
      
      <div class="error" id="numberError"></div>
    </div>

    <div>
         <label> <input name="email" id="email" class="input <?php echo ($errors['email'] != NULL) ? 'red' : ''; ?>"
          value="<?php echo $values['email']; ?>" type="text" placeholder="Почта" /> </label>
  
  
      <div class="error" id="emailError"></div>
    </div>
    <div>
       <label>
        <input name="date" class="input <?php echo ($errors['dat'] != NULL) ? 'red' : ''; ?>" value="<?php if (strtotime($values['dat']) > 100000)
                   echo $values['dat']; ?>" type="date" />
       
      </label>
       <div class="error" id="dateError"></div>
    </div>

    <div>
      <div>Пол</div>
      <div class="mb-1">
        <label>
          <input name="radio" class="ml-2" type="radio" value="M" <?php if ($values['radio'] == 'M')
            echo 'checked'; ?> />
          <span class="<?php echo ($errors['radio'] != NULL) ? 'error' : ''; ?>"> Мужской </span>
        </label>
        <label>
          <input name="radio" class="ml-4" type="radio" value="W" <?php if ($values['radio'] == 'W')
            echo 'checked'; ?> />
          <span class="<?php echo ($errors['radio'] != NULL) ? 'error' : ''; ?>"> Женский </span>
        </label>
      </div>
       <div class="error" id="radioError"></div>
    </div>

    <div>
      <div>Любимый язык программирования</div>
     <select class="my-2 <?php echo ($errors['language'] != NULL) ? 'red' : ''; ?>" name="language[]"
        multiple="multiple">
        <option value="Pascal" <?php echo (in_array('Pascal', $languages)) ? 'selected' : ''; ?>>Pascal</option>
        <option value="C" <?php echo (in_array('C', $languages)) ? 'selected' : ''; ?>>C</option>
        <option value="C++" <?php echo (in_array('C++', $languages)) ? 'selected' : ''; ?>>C++</option>
        <option value="JavaScript" <?php echo (in_array('JavaScript', $languages)) ? 'selected' : ''; ?>>JavaScript
        </option>
        <option value="PHP" <?php echo (in_array('PHP', $languages)) ? 'selected' : ''; ?>>PHP</option>
        <option value="Python" <?php echo (in_array('Python', $languages)) ? 'selected' : ''; ?>>Python</option>
        <option value="Java" <?php echo (in_array('Java', $languages)) ? 'selected' : ''; ?>>Java</option>
        <option value="Haskel" <?php echo (in_array('Haskel', $languages)) ? 'selected' : ''; ?>>Haskel</option>
        <option value="Clojure" <?php echo (in_array('Clojure', $languages)) ? 'selected' : ''; ?>>Clojure</option>
        <option value="Scala" <?php echo (in_array('Scala', $languages)) ? 'selected' : ''; ?>>Scala</option>
      </select>
      <div class="error" id="languageError"></div>
    </div>

    <div class="my-2">
      <div>Биография</div>
       <label>
        <textarea name="bio" class="input <?php echo ($errors['bio'] != NULL) ? 'red' : ''; ?>"
          placeholder="Биография"><?php echo $values['bio']; ?></textarea>
      </label>
       <div class="error" id="bioError"></div>
    </div>

    <div>
      <label>
        <input name="check" type="checkbox" <?php echo ($values['check'] != NULL) ? 'checked' : ''; ?> />
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
            
            
            $('#mainForm')[0].reset();
    
    // Заполнение даты
    $('input[name="date"]').val(getCookie('date_value') || '');
    
    // Чекбокс
    $('input[name="check"]').prop('checked', getCookie('check_value') === '1');
    
    // Множественный выбор языков
    const langs = (getCookie('language_value') || '').split(',');
    $('select[name="language[]"]').val(langs);
            
    // Сброс всех полей перед заполнением
  
             // $('#fio').val(getCookie('fio_value') || '');
             // $('#number').val(getCookie('number_value') || '');
             // $('#email').val(getCookie('email_value') || '');
             // $('#radio').val(getCookie('radio_value') || '');
             // $('#bio').val(getCookie('bio_value') || '');

             console.log("Загружаемые куки:");
    console.log("fio_value:", getCookie('fio_value'));
    console.log("number_value:", getCookie('number_value'));
             console.log("email_value:", getCookie('email_value'));
             console.log("date_value:", getCookie('date_value'));
             console.log("radio_value:", getCookie('radio_value'));
             console.log("bio_value:", getCookie('bio_value'));
             console.log("language_value:", getCookie('language_value'));
            console.log("check_value:", getCookie('check_value'));
    // Заполнение из кук
    const cookiesToFields = {
        'fio_value': 'input[name="fio"]',
        'number_value': 'input[name="number"]',
        'email_value': 'input[name="email"]',
        'date_value': 'input[name="date"]',
        'radio_value': 'input[name="radio"]',
        'language_value': 'select[name="language[]"]',
        'bio_value': 'textarea[name="bio"]',
        'check_value': 'input[name="check"]'
    };

    Object.entries(cookiesToFields).forEach(([cookie, selector]) => {
        const value = getCookie(cookie);
        if (!value) return;

        if (cookie === 'radio_value') {
            $(`${selector}[value="${value}"]`).prop('checked', true);
        } else if (cookie === 'language_value') {
            const langs = value.split(',');
            $(selector).val(langs);
        } else if (cookie === 'check_value') {
            $(selector).prop('checked', value === '1');
        } else {
            $(selector).val(value);
        }
    });
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
             $('.error').text('');
    
    // Обновление ошибок
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
    }).then(() => {
        // Явное удаление всех кук
        document.cookie.split(";").forEach(cookie => {
            const eqPos = cookie.indexOf("=");
            const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            document.cookie = `${name}=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;`;
        });
        window.location.reload();
    });
}
</script>
</body>
</html>

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
        <input name="check" type="checkbox"/>
        С контрактом ознакомлен(а)
      </label>
       <div class="error" id="checkError"></div>
    </div>

        <button class="button" type="submit">Отправить</button>
        <!-- Кнопки входа/выхода обрабатываются через JS -->
        <div id="authButtons"></div>
    </form>

    <script>
        $(document).ready(function() {
            // Функция для обновления сообщений и полей
            function updateUI() {
                // Очищаем сообщения
                $('.error').text('');
                $('#successMessage, #infoMessage').text('');

                // Заполняем поля из cookies (если есть)
                $('#fio').val(getCookie('fio_value') || '');
               $('#number').val(getCookie('number_value') || '');
               $('#email').val(getCookie('email_value') || '');
               $('#date').val(getCookie('date_value') || '');
               $('#radio').val(getCookie('radio_value') || '');
               $('#language').val(getCookie('language_value') || '');
               $('#bio').val(getCookie('bio_value') || '');
            }

            // Обработка отправки формы
            $('#mainForm').submit(function(e) {
                e.preventDefault();
                
                // Собираем данные формы
                const formData = new FormData(this);
                
                // Для полей с multiple (языки)
                const languages = Array.from($('[name="language[]"]'))
                    .filter(option => option.selected)
                    .map(option => option.value);
                formData.delete('language[]');
                languages.forEach(lang => formData.append('language[]', lang));

                // Отправляем запрос
                fetch('index.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include' // Для сохранения cookies
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text().then(text => {
                            updateUI();
                            // Парсим новые куки
                            parseCookies();
                            showMessages();
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Функции для работы с cookies
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            function parseCookies() {
                // Обновляем сообщения об ошибках
                document.querySelectorAll('[id$="Error"]').forEach(element => {
                    const field = element.id.replace('Error', '');
                    const error = getCookie(`${field}_error`);
                    if (error) element.textContent = error;
                });

                // Показываем успешные сообщения
                if (getCookie('save')) {
                    $('#successMessage').text('Спасибо, результаты сохранены.');
                }
            }

            // Инициализация при загрузке
            updateUI();
            parseCookies();
        });
    </script>
</body>
</html>

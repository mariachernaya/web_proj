<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>Задание_8</title>
  </head>

  <body>
    <form action="" method="post" class="form">
      <div class="head">
        <h2><b>Форма обратной связи</b></h2>
      </div>
<div class="mess"></div>
<div class="mess mess_info"></div>

      <div>
        <label> <input name="fio" class="input " value="" type="text" placeholder="ФИО" /> </label>
        
          <div class="error" data-field="fio"></div>

      </div>
      
      <div>
        <label> <input name="number" class="input" value="" type="tel" placeholder="Номер телефона" /> </label>
        <div class="error" data-field="number"> </div>
      </div>
      
      <div>
        <label> <input name="email" class="input" value="" type="text" placeholder="Почта" /> </label>
        <div class="error" data-field="email">  </div>
      </div>
      
      <div>
        <label>
          <input name="date" class="input " value="" type="date" />
          <div class="error" data-field="date"> </div>
        </label>
      </div>
      
      <div>
        <div>Пол</div>
        <div class="mb-1">
          <label>
            <input name="radio" class="ml-2" type="radio" value="M" />
            <span class=""> Мужской </span>
          </label>
          <label>
            <input name="radio" class="ml-4" type="radio" value="W" />
            <span class=""> Женский </span>
          </label>
        </div>
        <div class="error" data-field="radio"> </div>
      </div>
      
      <div>
        <div>Любимый язык программирования</div>
        <select class="my-2 " name="language[]" multiple="multiple">
          <option value="Pascal">Pascal</option>
          <option value="C">C</option>
          <option value="C++">C++</option>
          <option value="JavaScript">JavaScript</option>
          <option value="PHP">PHP</option>
          <option value="Python">Python</option>
          <option value="Java">Java</option>
          <option value="Haskel">Haskel</option>
          <option value="Clojure">Clojure</option>
          <option value="Scala">Scala</option>
        </select>
        <div class="error" data-field="language[]"></div>
      </div>
      
      <div class="my-2">
        <div>Биография</div>
        <label>
          <textarea name="bio" class="input" placeholder="Биография"></textarea>
          <div class="error" data-field="bio"></div>
        </label>
      </div>
      
      <div>
        <label>
            <input name="check" type="checkbox"/>
              С контрактом ознакомлен(а)
          <div class="error" data-field="check"></div>
        </label>
      </div>

       <?php
          if($log) echo '<button class="button edbut" type="submit">Изменить</button>';
          else echo '<button class="button" type="submit">Отправить</button>';
          if($log) echo '<button class="button" type="submit" name="logout_form">Выйти</button>'; 
          else echo '<a class="btnlike" href="login.php" name="logout_form">Войти</a>';
        ?>
    </form>
<script>
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        // Проверяем сырой ответ
        const rawText = await response.text();
        console.log('Raw response:', rawText);
        
        // Пытаемся распарсить JSON
        let data;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            throw new Error(`Неверный формат ответа: ${rawText.substring(0, 100)}...`);
        }

        // Обработка ответа
        if (data.status === 'success') {
            // Успешная обработка
            showSuccessMessage(data.messages.success);
            updateFormFields(form, data);
        } else {
            // Ошибки валидации
            showFormErrors(form, data);
        }
    } catch (error) {
        console.error('Ошибка:', error);
        showErrorMessage(error.message);
    }
});

// Вспомогательные функции
function showSuccessMessage(msg) {
    const el = document.querySelector('.mess');
    el.innerHTML = msg || 'Данные успешно сохранены';
    el.style.color = 'green';
}

function showErrorMessage(msg) {
    const el = document.querySelector('.mess');
    el.innerHTML = msg || 'Ошибка при отправке формы';
    el.style.color = 'red';
}

function updateFormFields(form, data) {
    // Обновляем обычные поля
    Object.entries(data.values || {}).forEach(([name, value]) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) return;
        
        if (field.type === 'checkbox') {
            field.checked = !!value;
        } else if (field.type === 'radio') {
            field.checked = (field.value === value);
        } else {
            field.value = value || '';
        }
    });
    
    // Обновляем мультиселект
    if (data.languages && form.querySelector('select[name="language[]"]')) {
        const select = form.querySelector('select[name="language[]"]');
        Array.from(select.options).forEach(option => {
            option.selected = data.languages.includes(option.value);
        });
    }
}

function showFormErrors(form, data) {
    Object.entries(data.errors || {}).forEach(([field, hasError]) => {
        if (!hasError) return;
        
        const errorElement = document.querySelector(`.error[data-field="${field}"]`);
        if (errorElement) {
            errorElement.innerHTML = data.messages[field] || 'Ошибка';
            const input = form.querySelector(`[name="${field}"]`);
            if (input) input.classList.add('red');
        }
    });
}
</script>
  </body>
</html>

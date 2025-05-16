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

      <div class="mess"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
      <div class="mess mess_info"><?php if(isset($messages['info'])) echo $messages['info']; ?></div>
      <div>
        <label> <input name="fio" class="input <?php echo ($errors['fio'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['fio']; ?>" type="text" placeholder="ФИО" /> </label>
        
          <div class="error" data-field="fio"><?php echo $messages['fio']?></div>

      </div>
      
      <div>
        <label> <input name="number" class="input <?php echo ($errors['number'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['number']; ?>" type="tel" placeholder="Номер телефона" /> </label>
        <div class="error" data-field="number"> <?php echo $messages['number']?> </div>
      </div>
      
      <div>
        <label> <input name="email" class="input <?php echo ($errors['email'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['email']; ?>" type="text" placeholder="Почта" /> </label>
        <div class="error" data-field="email"> <?php echo $messages['email']?> </div>
      </div>
      
      <div>
        <label>
          <input name="date" class="input <?php echo ($errors['date'] != NULL) ? 'red' : ''; ?>" value="<?php if(strtotime($values['date']) > 100000) echo $values['date']; ?>" type="date" />
          <div class="error" data-field="date"> <?php echo $messages['date']?> </div>
        </label>
      </div>
      
      <div>
        <div>Пол</div>
        <div class="mb-1">
          <label>
            <input name="radio" class="ml-2" type="radio" value="M" <?php if($values['radio'] == 'M') echo 'checked'; ?>/>
            <span class="<?php echo ($errors['radio'] != NULL) ? 'error' : ''; ?>"> Мужской </span>
          </label>
          <label>
            <input name="radio" class="ml-4" type="radio" value="W" <?php if($values['radio'] == 'W') echo 'checked'; ?>/>
            <span class="<?php echo ($errors['radio'] != NULL) ? 'error' : ''; ?>"> Женский </span>
          </label>
        </div>
        <div class="error" data-field="radio"> <?php echo $messages['radio']?> </div>
      </div>
      
      <div>
        <div>Любимый язык программирования</div>
        <select class="my-2 <?php echo ($errors['language'] != NULL) ? 'red' : ''; ?>" name="language[]" multiple="multiple">
          <option value="Pascal" <?php echo (in_array('Pascal', $languages)) ? 'selected' : ''; ?>>Pascal</option>
          <option value="C" <?php echo (in_array('C', $languages)) ? 'selected' : ''; ?>>C</option>
          <option value="C++" <?php echo (in_array('C++', $languages)) ? 'selected' : ''; ?>>C++</option>
          <option value="JavaScript" <?php echo (in_array('JavaScript', $languages)) ? 'selected' : ''; ?>>JavaScript</option>
          <option value="PHP" <?php echo (in_array('PHP', $languages)) ? 'selected' : ''; ?>>PHP</option>
          <option value="Python" <?php echo (in_array('Python', $languages)) ? 'selected' : ''; ?>>Python</option>
          <option value="Java" <?php echo (in_array('Java', $languages)) ? 'selected' : ''; ?>>Java</option>
          <option value="Haskel" <?php echo (in_array('Haskel', $languages)) ? 'selected' : ''; ?>>Haskel</option>
          <option value="Clojure" <?php echo (in_array('Clojure', $languages)) ? 'selected' : ''; ?>>Clojure</option>
          <option value="Scala" <?php echo (in_array('Scala', $languages)) ? 'selected' : ''; ?>>Scala</option>
        </select>
        <div class="error" data-field="language[]"> <?php echo $messages['language']?> </div>
      </div>
      
      <div class="my-2">
        <div>Биография</div>
        <label>
          <textarea name="bio" class="input <?php echo ($errors['bio'] != NULL) ? 'red' : ''; ?>" placeholder="Биография"><?php echo $values['bio']; ?></textarea>
          <div class="error" data-field="bio"> <?php echo $messages['bio']?> </div>
        </label>
      </div>
      
      <div>
        <label>
            <input name="check" type="checkbox" <?php echo ($values['check'] != NULL) ? 'checked' : ''; ?>/>
              С контрактом ознакомлен(а)
          <div class="error" data-field="check"> <?php echo $messages['check']?> </div>
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
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Показываем загрузку
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...';
    
    // Очищаем предыдущие сообщения
    document.querySelectorAll('.error').forEach(el => el.innerHTML = '');
    document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
    document.querySelector('.mess').innerHTML = '';
    document.querySelector('.mess_info').innerHTML = '';

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        // Проверяем Content-Type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Invalid content type: ${contentType}. Response: ${text}`);
        }

        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Server error');
        }

        if (result.status === 'success') {
            // Успешная отправка
            document.querySelector('.mess').innerHTML = result.messages.success || 'Данные успешно сохранены';
            document.querySelector('.mess_info').innerHTML = result.messages.info || '';
            
            // Обновляем значения полей
            updateFormFields(form, result);
            
        } else {
            // Ошибки валидации
            showFormErrors(form, result);
        }
        
    } catch (error) {
        console.error('Fetch error:', error);
        document.querySelector('.mess').innerHTML = `Ошибка: ${error.message}`;
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
});

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

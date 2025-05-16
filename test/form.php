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
    const formData = new FormData(form);

    // Очищаем предыдущие ошибки
    document.querySelectorAll('.error').forEach(el => el.innerHTML = '');
    document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error('Network error');
        
        const data = await response.json();

        // Обновляем сообщения
        if (data.messages.success) {
            document.querySelector('.mess').innerHTML = data.messages.success;
        }
        if (data.messages.info) {
            document.querySelector('.mess_info').innerHTML = data.messages.info;
        }

        // Обновляем ошибки
        Object.keys(data.errors).forEach(field => {
            const errorElement = document.querySelector(`.error[data-field="${field}"]`);
            if (errorElement && data.errors[field]) {
                errorElement.innerHTML = data.messages[field] || '';
                const input = form.querySelector(`[name="${field}"]`);
                if (input) input.classList.add('red');
            }
        });

        // Если успешно, обновляем значения
        if (data.success) {
            if (data.values) {
                Object.keys(data.values).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = data.values[field];
                        } else {
                            input.value = data.values[field] || '';
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error('Ошибка:', error);
        document.querySelector('.mess').innerHTML = 'Произошла ошибка при отправке формы';
    }
});
</script>
  </body>
</html>

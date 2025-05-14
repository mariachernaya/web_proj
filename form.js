console.log("form.js подключен");

$(document).ready(function () {
  console.log("Документ готов");

  const $form = $('.form');
  console.log("Форма найдена?", $form.length); // Должно быть 1

  $form.on('submit', function (e) {
    e.preventDefault();
    console.log("Форма отправлена");

    var formData = new FormData(this);
$.ajax({
  type: 'POST',
  url: 'index.php',
  data: formData,
  processData: false,
  contentType: false,
  dataType: 'json', // Указываем, что ожидаем JSON
  success: function (response) {
    if (response.status === 'success') {
      console.log("Успешный ответ от сервера");
      // Если форма отправлена успешно, показываем логин и сообщение
      $('#message-container').html('<p>' + response.message + '</p>');
      $('#login-container').html('<p>Ваш логин: ' + response.login + '</p>');
      $('#password-container').html('<p>Ваш пароль: ' + response.password + '</p>');

      // Очищаем поля формы
      $('form')[0].reset();
    } else {
      // В случае ошибки, показываем сообщение об ошибке
      $('#message-container').html('<p>' + response.message + '</p>');
    }
  },
  error: function (xhr, status, error) {
    console.error("Ошибка AJAX:", error);
    $('#message-container').html('<p>Произошла ошибка. Попробуйте позже.</p>');
  }
});

    
  });
});


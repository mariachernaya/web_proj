$(document).ready(function () {
  // Отправка формы
  $('.form').on('submit', function (e) {
    e.preventDefault(); // Остановить стандартную отправку формы

    var formData = new FormData(this); // Собираем данные формы

    $.ajax({
      type: 'POST',
      url: 'form.php', // тот же файл
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        // Заменяем всю форму (снаружи до </form>)
        const newForm = $(response).find('#form-container').html();
        $('#form-container').html(newForm);
      },
      error: function (xhr, status, error) {
        alert('Ошибка при отправке формы: ' + error);
      }
    });
  });
});

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
      dataType: 'html',
      success: function (response) {
        console.log("Успешный ответ от сервера");
        const newForm = $(response).find('#form-container').html();
        $('#form-container').html(newForm);
      },
      error: function (xhr, status, error) {
        console.error("Ошибка AJAX", error);
      }
    });
  });
});
// $(document).ready(function () {
//   // Отправка формы
//   $('.form').on('submit', function (e) {
//     e.preventDefault(); // Остановить стандартную отправку формы

//     var formData = new FormData(this); // Собираем данные формы

//     $.ajax({
//       type: 'POST',
//       url: 'form.php', // тот же файл
//       data: formData,
//       processData: false,
//       contentType: false,
//       success: function (response) {
//         // Заменяем всю форму (снаружи до </form>)
//         const newForm = $(response).find('#form-container').html();
//         $('#form-container').html(newForm);
//       },
//       error: function (xhr, status, error) {
//         alert('Ошибка при отправке формы: ' + error);
//       }
//     });
//   });
// });

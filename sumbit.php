<?php
header('Content-Type: text/plain');

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Здесь можно добавить валидацию данных
    
    // Например, сохраняем данные в файл
    $data = "Имя: $name\nEmail: $email\nСообщение: $message\n\n";
    file_put_contents('form_submissions.txt', $data, FILE_APPEND);
    
    // Или отправляем письмо
    // mail('your@email.com', 'Новое сообщение с формы', $data);
    
    echo "Данные получены успешно!";
} else {
    http_response_code(405);
    echo "Метод не разрешен";
}
?>

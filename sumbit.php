<?php
header('Content-Type: application/json');

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

// Получаем и очищаем данные
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Валидация данных
$errors = [];
if (empty($name)) {
    $errors['name'] = 'Имя обязательно для заполнения';
}
if (empty($email)) {
    $errors['email'] = 'Email обязателен для заполнения';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Некорректный формат email';
}
if (empty($message)) {
    $errors['message'] = 'Сообщение обязательно для заполнения';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Настройки подключения к БД

$user = 'u68790'; 
$pass = '4247220'; 


try {
    // Подключаемся к MySQL
  
    $pdo = new PDO("mysql:host=localhost;dbname=u68790;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Подготавливаем SQL запрос
    $stmt = $pdo->prepare("INSERT INTO usersi (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
    
    // Выполняем запрос с параметрами
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':message' => $message
    ]);

    // Возвращаем успешный ответ
    echo json_encode(['success' => true, 'message' => 'Данные успешно сохранены']);
    
} catch (PDOException $e) {
    // Обработка ошибок БД
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>

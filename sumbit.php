<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

// Обработка входных данных
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Валидация
$errors = [];
if (empty($name)) $errors['name'] = 'Имя обязательно';
if (empty($email)) $errors['email'] = 'Email обязателен';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный email';
if (empty($message)) $errors['message'] = 'Сообщение обязательно';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Настройки БД
$user = 'u68790'; 
$pass = '4247220'; 


try {
  
    $pdo = new PDO("mysql:host=localhost;dbname=u68790;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $pdo->beginTransaction();

    // 1. Сохраняем основную информацию о пользователе
    $stmtUser = $pdo->prepare("INSERT INTO usersi (name, email, message, created_at) 
                              VALUES (:name, :email, :message, NOW())");
    $stmtUser->execute([
        ':name' => $name,
        ':email' => $email,
        ':message' => $message
    ]);
    
    $userId = $pdo->lastInsertId();

    // 2. Генерируем и сохраняем учетные данные
    $login = uniqid();
    $password = uniqid();
    $passwordHash = md5($password);

    $stmtCred = $pdo->prepare("INSERT INTO user_credentials 
                              (user_id, login, password_hash, created_at) 
                              VALUES (:user_id, :login, :password_hash, NOW())");
    $stmtCred->execute([
        ':user_id' => $userId,
        ':login' => $login,
        ':password_hash' => $passwordHash
    ]);

  
    $pdo->commit();

    // Возвращаем ответ
    echo json_encode([
        'success' => true,
        'message' => 'Регистрация успешна!',
        'credentials' => [
            'login' => $login,
            'password' => $password
        ]
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}



?>

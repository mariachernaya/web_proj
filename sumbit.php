<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name)) $errors['name'] = 'Имя обязательно';
if (!preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $name))  $errors['name'] = 'Можно использовать только кириллицу';
if (empty($email)) $errors['email'] = 'Email обязателен';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный email';
if (empty($message)) $errors['message'] = 'Сообщение обязательно';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Настройка БД
$user = 'u68790'; 
$pass = '4247220'; 


try {
  
    $pdo = new PDO("mysql:host=localhost;dbname=u68790;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $pdo->beginTransaction();

    
    $stmtUser = $pdo->prepare("INSERT INTO usersi (name, email, message, created_at) 
                              VALUES (:name, :email, :message, NOW())");
    $stmtUser->execute([
        ':name' => $name,
        ':email' => $email,
        ':message' => $message
    ]);
    
    $userId = $pdo->lastInsertId();

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
}



?>

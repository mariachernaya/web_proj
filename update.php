<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Настройки БД
$user = 'u68790'; 
$pass = '4247220'; 


try {
  
    $pdo = new PDO("mysql:host=localhost;dbname=u68790;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Обновление данных пользователя
    $stmt = $pdo->prepare("UPDATE usersi SET name = :name, email = :email, message = :message WHERE email = :email");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Данные успешно обновлены']);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>

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
    
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Поиск пользователя
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.email, u.message 
        FROM usersi u
        JOIN user_credentials uc ON u.id = uc.user_id
        WHERE uc.login = :login AND uc.password_hash = SHA2(:password, 256)
    ");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['error' => 'Неверный логин или пароль']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>

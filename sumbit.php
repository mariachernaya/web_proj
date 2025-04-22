<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$response = ['success' => false, 'message' => ''];

// Получение данных из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);

// Проверка обязательных полей
if (empty($data['name'])  empty($data['phone'])  empty($data['email']) || !isset($data['agree'])) {
    $response['message'] = 'Не все обязательные поля заполнены';
    echo json_encode($response);
    exit;
}


// Подключение к базе данных MySQL
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "userdb";

    $user = 'u68791'; 
	$pass = '1609462'; 
	$conn = new PDO('mysql:host=localhost;dbname=u68791', $user, $pass,
	[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 

try {
    //$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Подготовка SQL-запроса
    // $stmt = $conn->prepare("INSERT INTO usersi (name, phone, email, bio, agree, created_at) 
    //                        VALUES (:name, :phone, :email, :bio, :agree, NOW())");
     $stmt = $conn->prepare("INSERT INTO usersi (name, phone, email, bio) 
                           VALUES (:name, :phone, :email, :bio)");
    // Привязка параметров
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':bio', $data['bio']);
   // $stmt->bindParam(':agree', $data['agree'], PDO::PARAM_BOOL);
    
    // Выполнение запроса
    $stmt->execute();
    
    $response['success'] = true;
    $response['message'] = 'Данные успешно сохранены';
} catch(PDOException $e) {
    $response['message'] = 'Ошибка базы данных: ' . $e->getMessage();
}

echo json_encode($response);
?>

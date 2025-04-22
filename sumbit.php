<?php
$db;
include ('database.php');
//header("Content-Type: text/html; charset=UTF-8");
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$bio = $data['bio'];

$stmt = $db->prepare("SELECT * FROM usersi WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $db->prepare("UPDATE usersi SET name=?, phone=?, bio=? WHERE email=?");
    $stmt->execute([$name, $phone, $bio, $email]);

    echo json_encode(['message' => 'Данные обновлены.']);
} else {
    
    $login = uniqid('user_');
    $password = bin2hex(random_bytes(4)); //пароль сделать
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO usersi (name, email, phone, bio, login, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $bio, $login, $hashed]);

    echo json_encode([
        'message' => 'Пользователь зарегистрирован.',
        'login' => $login,
        'password' => $password
    ]);
}

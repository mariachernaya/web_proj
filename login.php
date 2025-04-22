<?php
header('Content-Type: application/json');
session_start();
include ('database.php'); 

$data = json_decode(file_get_contents("php://input"), true);

$login = $data['login'] ?? '';
$password = $data['password'] ?? '';

if (empty($login) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit();
}

try {
    $stmt = $db->prepare("SELECT id, password FROM usersi WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && md5($password) === $user['password']) {
        $_SESSION['login'] = $login;
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверный логин или пароль']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 2rem; }
    form { max-width: 400px; margin: auto; }
    input, button { display: block; width: 100%; padding: 0.5rem; margin-bottom: 1rem; }
    .message { text-align: center; font-weight: bold; color: red; }
  </style>
</head>
<body>

  <h2>Вход в систему</h2>
  <form id="loginForm">
    <input type="text" name="login" placeholder="Логин" required />
    <input type="password" name="password" placeholder="Пароль" required />
    <button type="submit">Войти</button>
  </form>

  <div class="message" id="message"></div>

  <script>
    const form = document.getElementById('loginForm');
    const message = document.getElementById('message');

    form.addEventListener('submit', function(event) {
      event.preventDefault();

      const formData = new FormData(form);
      const data = {
        login: formData.get('login'),
        password: formData.get('password')
      };

      fetch('login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          message.style.color = 'green';
          message.textContent = 'Успешный вход! Перенаправление...';
          setTimeout(() => {
            window.location.href = 'test.html'; // или куда угодно
          }, 1500);
        } else {
          message.style.color = 'red';
          message.textContent = response.message;
        }
      })
      .catch(error => {
        message.textContent = 'Ошибка при подключении к серверу';
        console.error(error);
      });
    });
  </script>

</body>
</html>

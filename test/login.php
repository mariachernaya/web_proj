<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: ./');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require ('database.php');
    $login = $_POST['login'];
    $password = md5($_POST['password']);
    try {
        $stmt = $db->prepare("SELECT id FROM users WHERE login = ? and password = ?");
        $stmt->execute([$login, $password]);
        $its = $stmt->rowCount();
        if ($its) {
            $uid = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
             $_SESSION['login'] = $_POST['login'];
    $_SESSION['user_id'] = $uid;
            
           

   // $errorCookies = ['fio_error', 'number_error', 'email_error', 'date_error', 'radio_error', 'language_error', 'bio_error', 'check_error'];
   //  foreach ($errorCookies as $cookie) {
   //      setcookie($cookie, '', time() - 3600, '/');
   //  }

    // Временный вывод для отладки
    // echo "<pre>Cookies после входа:\n";
    // print_r($_COOKIE);
    // echo "</pre>";
    // exit(); // Удалите эту строку после отладки         
           
    header('Location: ./');
    exit();
} else
            $error = 'Неверный логин или пароль';
    } catch (PDOException $e) {
        print ('Error : ' . $e->getMessage());
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>project</title>
</head>

<body>
    <form action="" method="post" class="form">
        <div class="mess" style="color: red;"><?php echo $error; ?></div>
        <h2>Авторизация</h2>
        <div> <input class="input" style="width: 100%;" type="text" name="login" placeholder="Логин"> </div>
        <div> <input class="input" style="width: 100%;" type="text" name="password" placeholder="Пароль"> </div>
        <button class="button" type="submit">Войти</button>
    </form>
</body>

</html>

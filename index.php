<?php
ob_start();
$db;
include ('database.php');
session_start();

$messages = ['success' => '', 'info' => ''];
$errors = [];
$values = [];
$languages = [];
$log = !empty($_SESSION['login']);
$error = false;

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($is_ajax) {
    header('Content-Type: application/json; charset=UTF-8');
} else header("Content-Type: text/html; charset=UTF-8");

$error = false;
$log = !empty($_SESSION['login']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fio = isset($_POST['fio']) ? $_POST['fio'] : '';
    $number = isset($_POST['number']) ? $_POST['number'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $radio = isset($_POST['radio']) ? $_POST['radio'] : '';
    $language = isset($_POST['language']) ? $_POST['language'] : [];
    $bio = isset($_POST['bio']) ? $_POST['bio'] : '';
    $check = isset($_POST['check']) ? $_POST['check'] : '';

	    if (isset($_POST['logout_form'])) {
		    // Очищаем все куки формы
		    $cookies = ['fio_value', 'number_value', 'email_value', 'date_value', 'radio_value', 
		               'language_value', 'bio_value', 'check_value', 'login', 'pass'];
		    foreach ($cookies as $name) {
		        setcookie($name, '', time() - 3600, '/');
		    }
		    
		    // Очищаем сессию
		    $_SESSION = [];
		    
		    // Уничтожаем сессию
		    if (ini_get("session.use_cookies")) {
		        $params = session_get_cookie_params();
		        setcookie(session_name(), '', time() - 42000,
		            $params["path"], $params["domain"],
		            $params["secure"], $params["httponly"]
		        );
		    }
		    session_destroy();
		
		    if ($is_ajax) {
		        echo json_encode([
		            'logout' => true,
		            'log' => false,
		            'messages' => ['success' => 'Вы успешно вышли из системы']
		        ]);
		        exit();
		    } else {
		        header('Location: index.php');
		        exit();
		    }
		}

    // Валидация полей
    $error = false;
    $messages = [];
    $errors = [];

    // Функция для валидации полей
    function validate_field($fieldName, $errorMessage, $condition) {
        global $errors, $messages, $error;
        
        if ($condition) {
            $errors[$fieldName] = true;
            $messages[$fieldName] = $errorMessage;
            $error = true;
            return true;
        }
        return false;
    }

    // Валидация ФИО
    if (validate_field('fio', 'Это поле пустое', empty($fio))) {
    } else {
        validate_field('fio', 'Неправильный формат: Имя Фамилия, только кириллица', !preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $fio));
    }

    // Валидация телефона
    if (validate_field('number', 'Это поле пустое', empty($number))) {
    } else {
        validate_field('number', 'Поле должно содержать 11 цифр, начиная с 8', strlen($number) != 11 || substr($number, 0, 1) != '8');
    }

    // Валидация email
    if (validate_field('email', 'Это поле пустое', empty($email))) {
    } else {
        validate_field('email', 'Неправильный формат email', !filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    // Валидация даты
    if (validate_field('date', 'Это поле пустое', empty($date))) {
    } else {
        validate_field('date', 'Неверная дата', strtotime($date) > time());
    }

    // Валидация пола
    validate_field('radio', "Не выбран пол", empty($radio) || !preg_match('/^(M|W)$/', $radio));

    // Валидация языков
    validate_field('language', 'Не выбран язык', empty($language));

    // Валидация биографии
    if (validate_field('bio', 'Это поле пустое', empty($bio))) {
    } else {
        validate_field('bio', 'Слишком длинная биография', strlen($bio) > 65535);
    }

    // Валидация чекбокса
    validate_field('check', 'Не ознакомлены с контрактом', empty($check));

    if (!$error) {
        if ($log) {
            // Обновление данных для авторизованного пользователя
            try {
                $db->beginTransaction();

                // Обновляем основные данные
                $stmt = $db->prepare("UPDATE form_data SET fio = ?, number = ?, email = ?, dat = ?, radio = ?, bio = ? WHERE user_id = ?");
                $stmt->execute([$fio, $number, $email, $date, $radio, $bio, $_SESSION['user_id']]);

                // Удаляем старые языки
                $stmt = $db->prepare("DELETE FROM form_data_lang WHERE id_form = ?");
                $stmt->execute([$_SESSION['form_id']]);

                // Добавляем новые языки
                $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
                foreach ($languages as $lang) {
                    $stmtLang = $db->prepare("SELECT id FROM languages WHERE name = ?");
                    $stmtLang->execute([$lang]);
                    $langId = $stmtLang->fetchColumn();
                    if ($langId) {
                        $stmt1->execute([$_SESSION['form_id'], $langId]);
                    }
                }

                $db->commit();

                // Обновляем значения в сессии
                $_SESSION['form_data'] = [
                    'fio' => $fio,
                    'number' => $number,
                    'email' => $email,
                    'date' => $date,
                    'radio' => $radio,
                    'bio' => $bio
                ];

                $response = [
                    'messages' => [
                        'success' => 'Данные успешно обновлены!'
                    ],
                    'log' => $log,
                    'success' => true
                ];

            } catch (PDOException $e) {
                $db->rollBack();
                $response = [
                    'messages' => [
                        'error' => 'Ошибка при обновлении данных: ' . $e->getMessage()
                    ],
                    'success' => false
                ];
            }
	}else {
            // Создание нового пользователя
            $login = uniqid();
            $pass = uniqid();
            $mpass = md5($pass);

            try {
                $db->beginTransaction();

                // Создаем пользователя
                $stmt = $db->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
                $stmt->execute([$login, $mpass]);
                $user_id = $db->lastInsertId();

                // Сохраняем данные формы
                $stmt = $db->prepare("INSERT INTO form_data (user_id, fio, number, email, dat, radio, bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $fio, $number, $email, $date, $radio, $bio]);
                $fid = $db->lastInsertId();

                // Сохраняем выбранные языки
                $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
                foreach ($languages as $lang) {
                    $stmtLang = $db->prepare("SELECT id FROM languages WHERE name = ?");
                    $stmtLang->execute([$lang]);
                    $langId = $stmtLang->fetchColumn();
                    if ($langId) {
                        $stmt1->execute([$fid, $langId]);
                    }
                }

                $db->commit();

                // Устанавливаем куки
                setcookie('login', $login, time() + 3600 * 24 * 30, '/');
                setcookie('pass', $pass, time() + 3600 * 24 * 30, '/');

                $response = [
                    'messages' => [
                        'success' => 'Спасибо, результаты сохранены.',
                        'info' => sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.', $login, $pass)
                    ],
                    'generated' => [
                        'login' => $login,
                        'pass' => $pass
                    ],
                    'log' => false,
                    'success' => true
                ];

            } catch (PDOException $e) {
                $db->rollBack();
                $response = [
                    'messages' => [
                        'error' => 'Ошибка при сохранении данных'
                    ],
                    'success' => false
                ];
            }
        }
    } else {
        $response = [
            'messages' => $messages,
            'errors' => $errors,
            'success' => false
        ];
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    } else {
        // Для не-AJAX запросов
        if (isset($response['messages']['success'])) {
            $_SESSION['success_message'] = $response['messages']['success'];
        }
        if (isset($response['messages']['info'])) {
            $_SESSION['info_message'] = $response['messages']['info'];
        }
        header('Location: index.php');
        exit();
    }
} else {
    $fio = !empty($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : '';
    $number = !empty($_COOKIE['number_error']) ? $_COOKIE['number_error'] : '';
    $email = !empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '';
    $date = !empty($_COOKIE['date_error']) ? $_COOKIE['date_error'] : '';
    $radio = !empty($_COOKIE['radio_error']) ? $_COOKIE['radio_error'] : '';
    $language = !empty($_COOKIE['language_error']) ? $_COOKIE['language_error'] : '';
    $bio = !empty($_COOKIE['bio_error']) ? $_COOKIE['bio_error'] : '';
    $check = !empty($_COOKIE['check_error']) ? $_COOKIE['check_error'] : '';

    $errors = array();
    $messages = array();
    $values = array();
    $error = true;

    function set_val($str, $pole)
    {
        global $values;
        $values[$str] = empty($pole) ? '' : strip_tags($pole);
    }

    function check_field($str, $pole)
    {
        global $errors, $messages, $values, $error;
        if ($error)
            $error = empty($_COOKIE[$str . '_error']);
        $errors[$str] = !empty($_COOKIE[$str . '_error']);
        $messages[$str] = "<div class=\"error\">$pole</div>";
        $values[$str] = empty($_COOKIE[$str . '_value']) ? '' : strip_tags($_COOKIE[$str . '_value']);
        setcookie($str . '_error', '', time() - 30 * 24 * 60 * 60);
        return;
    }
	if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        $messages['success'] = 'Спасибо, результаты сохранены.';
    }

    check_field('fio', $fio);
    check_field('number', $number);
    check_field('email', $email);
    check_field('date', $date);
    check_field('radio', $radio);
    check_field('language', $language);
    check_field('bio', $bio);
    check_field('check', $check);

    $languages = explode(',', $values['language']);
    if ($error && !empty($_SESSION['login'])) {
        try {
			
            $dbLangs = $db->prepare("SELECT * FROM form_data WHERE user_id = ?");
            $dbLangs->execute([$_SESSION['user_id']]);
            $user_inf = $dbLangs->fetchAll(PDO::FETCH_ASSOC)[0];

            $form_id = $user_inf['id'];
            $_SESSION['form_id'] = $form_id;

            $dbL = $db->prepare("SELECT l.name FROM form_data_lang f
                                JOIN languages l ON l.id = f.id_lang
                                WHERE f.id_form = ?");

            $dbL->execute([$form_id]);

            $languages = [];
            foreach ($dbL->fetchAll(PDO::FETCH_ASSOC) as $item)
                $languages[] = $item['name'];

          
		if ($error && !empty($_SESSION['login'])) {
      set_val('fio', $user_inf['fio']);
            set_val('number', $user_inf['number']);
            set_val('email', $user_inf['email']);
            set_val('date', $user_inf['dat']);
            set_val('radio', $user_inf['radio']);
            set_val('language', $language);
            set_val('bio', $user_inf['bio']);
            set_val('check', "1");
} else {
 
    foreach ($values as $key => $val) {
        $values[$key] = !empty($_COOKIE[$key.'_value']) ? $_COOKIE[$key.'_value'] : '';
    }
}
        } catch (PDOException $e) {
            exit();
        }
    }

	if ($is_ajax) {
    ob_end_clean();
    header('Content-Type: application/json');
    
    $response = [
        'messages' => $messages,
        'errors' => $errors,
        'values' => $values,
        'languages' => $languages,
        'log' => $log,
        'success' => !$error
    ];
    
    if (!$error && isset($generated)) {
        $response['generated'] = $generated;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
} else  include('form.php');
}?>

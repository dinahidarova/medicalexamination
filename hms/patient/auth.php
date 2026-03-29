<?php
session_start();

// Включаем отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Правильный путь к конфигурации
$config_path = __DIR__ . '/../include/config.php';

// Проверяем существование файла
if (!file_exists($config_path)) {
    die("Файл конфигурации не найден: " . $config_path);
}

// Подключаем конфигурацию
require_once $config_path;

// Проверяем, что подключение установлено
if (!isset($con) || !$con) {
    die("Ошибка подключения к базе данных: переменная \$con не определена или подключение не установлено");
}

// Проверяем, что таблица существует
$check_table = mysqli_query($con, "SHOW TABLES LIKE 'tblpatient'");
if (!$check_table || mysqli_num_rows($check_table) == 0) {
    die("Ошибка: таблица 'tblpatient' не найдена в базе данных");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $password = $_POST['password'];
    
    // Ищем пациента
    $query = "SELECT * FROM tblpatient 
              WHERE (PatientEmail = '$login' 
                     OR PatientContno = '$login')";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die("Ошибка запроса: " . mysqli_error($con));
    }
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Проверка пароля
        if (empty($row['passwords'])) {
            // Временный пароль - дата рождения
            $temp_password = date('Ymd', strtotime($row['PatientDOB']));
            if ($password == $temp_password) {
                $_SESSION['id'] = $row['ID'];
                $_SESSION['name'] = $row['PatientName'];
                $_SESSION['role'] = 'patient';
                $_SESSION['email'] = $row['PatientEmail'];
                
                header("Location: index.php?first_login=1");
                exit();
            } else {
                header("Location: ../../index.php?error=invalid");
                exit();
            }
        } else {
            // Проверка хешированного пароля
            if (password_verify($password, $row['passwords'])) {
                $_SESSION['id'] = $row['ID'];
                $_SESSION['name'] = $row['PatientName'];
                $_SESSION['role'] = 'patient';
                $_SESSION['email'] = $row['PatientEmail'];
                
                header("Location: index.php");
                exit();
            } else {
                header("Location: ../../index.php?error=invalid");
                exit();
            }
        }
    } else {
        header("Location: ../../index.php?error=invalid");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
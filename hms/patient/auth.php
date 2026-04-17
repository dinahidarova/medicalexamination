<?php
session_start();

// Подключаем конфигурацию
require_once __DIR__ . '/../include/config.php';

// Проверяем подключение к БД
if (!isset($con) || !$con) {
    die("Ошибка подключения к базе данных");
}

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Получаем данные из формы
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    // Защита от SQL-инъекций
    $login = mysqli_real_escape_string($con, $login);
    
    // Ищем пациента по email или телефону
    $query = "SELECT * FROM tblpatient WHERE PatientEmail = '$login' OR PatientContno = '$login'";
    $result = mysqli_query($con, $query);
    
    // Если пациент найден
    if ($row = mysqli_fetch_assoc($result)) {
        
        // Проверяем пароль
        if (password_verify($password, $row['passwords'])) {
            
            // Успешный вход - сохраняем в сессию
            $_SESSION['id'] = $row['ID'];
            $_SESSION['name'] = $row['PatientName'];
            $_SESSION['role'] = 'patient';
            $_SESSION['email'] = $row['PatientEmail'];
            
            // Перенаправляем в личный кабинет
            header("Location: dashboard.php");
            exit();
        } else {
            // Неверный пароль
            header("Location: ../../index.php?error=invalid");
            exit();
        }
        
    } else {
        // Пациент не найден
        header("Location: ../../index.php?error=invalid");
        exit();
    }
    
} else {
    // Не POST запрос - перенаправляем на главную
    header("Location: ../../index.php");
    exit();
}
?>
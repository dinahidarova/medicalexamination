<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../include/config.php';

if (!isset($con) || !$con) {
    die("Ошибка подключения к БД");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $password = $_POST['password'];
    
    // Поиск по телефону или email
    $query = "SELECT * FROM tblpatient 
              WHERE PatientContno = '$login' OR PatientEmail = '$login'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) == 0) {
        header("Location: ../../index.php?error=invalid");
        exit();
    }
    
    $row = mysqli_fetch_assoc($result);
    
    // Проверка пароля
    $expected_password = date('Ymd', strtotime($row['PatientDOB']));
    $auth_success = false;
    
    if (!empty($row['passwords']) && password_verify($password, $row['passwords'])) {
        $auth_success = true;
    } elseif ($password == $expected_password) {
        $auth_success = true;
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($con, "UPDATE tblpatient SET passwords = '$hashed' WHERE ID = '{$row['ID']}'");
    }
    
    if ($auth_success) {
        $_SESSION['id'] = $row['ID'];
        $_SESSION['name'] = $row['PatientName'];
        $_SESSION['role'] = 'patient';
        
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: ../../index.php?error=invalid");
        exit();
    }
} else {
    header("Location: ../../index.php");
    exit();
}
?>
<?php
session_start();
require_once __DIR__ . '/../include/config.php';

echo "<h2>Отладка авторизации</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    echo "Логин: $login<br>";
    echo "Пароль: $password<br><br>";
    
    $query = "SELECT * FROM tblpatient WHERE PatientContno = '$login' OR PatientEmail = '$login'";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "✅ Пациент найден: " . $row['PatientName'] . "<br>";
        echo "Пароль в БД: " . $row['passwords'] . "<br>";
        
        if (password_verify($password, $row['passwords'])) {
            echo "<span style='color:green'>✅ ПАРОЛЬ ВЕРНЫЙ!</span><br>";
            $_SESSION['id'] = $row['ID'];
            $_SESSION['name'] = $row['PatientName'];
            $_SESSION['role'] = 'patient';
            echo "Перенаправление на dashboard.php...";
            // header("Location: dashboard.php");
            // exit();
        } else {
            echo "<span style='color:red'>❌ ПАРОЛЬ НЕВЕРНЫЙ!</span><br>";
        }
    } else {
        echo "❌ Пациент не найден<br>";
    }
} else {
    // Показываем форму для теста
    ?>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин" value="9600875907">
        <input type="text" name="password" placeholder="Пароль" value="20040412">
        <button type="submit">Проверить</button>
    </form>
    <?php
}
?>
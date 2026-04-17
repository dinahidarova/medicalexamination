<?php
require_once 'hms/include/config.php';

echo "<h2>Отладка входа</h2>";

// Данные для теста (Хайдарова Дина)
$login = '9600875907';
$password = '20040412';

echo "Проверяем логин: $login<br>";
echo "Проверяем пароль: $password<br><br>";

// Ищем пациента
$query = "SELECT ID, PatientName, PatientEmail, PatientContno, passwords FROM tblpatient WHERE PatientContno = '$login'";
$result = mysqli_query($con, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo "✅ Пациент найден: " . $row['PatientName'] . "<br>";
    echo "Пароль в БД: " . $row['passwords'] . "<br>";
    echo "Длина пароля в БД: " . strlen($row['passwords']) . "<br>";
    
    // Проверяем password_verify
    if (password_verify($password, $row['passwords'])) {
        echo "<span style='color:green'>✅ ПАРОЛЬ ВЕРНЫЙ! password_verify() работает.</span><br>";
    } else {
        echo "<span style='color:red'>❌ ПАРОЛЬ НЕВЕРНЫЙ! password_verify() не прошел.</span><br>";
        
        // Проверяем, может быть пароль в MD5?
        if (md5($password) == $row['passwords']) {
            echo "Но пароль совпадает с MD5 хешем<br>";
        }
    }
} else {
    echo "❌ Пациент с логином '$login' не найден<br>";
}

// Проверим всех пациентов
echo "<h3>Все пациенты в системе:</h3>";
$all = mysqli_query($con, "SELECT ID, PatientName, PatientContno FROM tblpatient");
while ($p = mysqli_fetch_assoc($all)) {
    echo "- ID {$p['ID']}: {$p['PatientName']} (тел: {$p['PatientContno']})<br>";
}
?>
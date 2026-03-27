<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Установка паролей для пациентов</h2>";

// Подключаем config.php
require_once __DIR__ . '/hms/include/config.php';

// Проверяем подключение
if (!isset($con) || !$con) {
    die("❌ Ошибка подключения к базе данных");
}

echo "✅ Подключение к БД успешно<br><br>";

// Получаем всех пациентов
$query = "SELECT ID, PatientName, PatientEmail, PatientDOB FROM tblpatient";
$result = mysqli_query($con, $query);

if (!$result) {
    die("❌ Ошибка запроса: " . mysqli_error($con));
}

$updated = 0;
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Имя</th><th>Email</th><th>Дата рождения</th><th>Пароль</th><th>Статус</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['ID'];
    $temp_password = date('Ymd', strtotime($row['PatientDOB']));
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    
    // Обновляем пароль
    $update = "UPDATE tblpatient SET passwords = '$hashed_password' WHERE ID = $id";
    if (mysqli_query($con, $update)) {
        $updated++;
        echo "<tr style='background-color:#e8f5e9'>";
        echo "<td>$id</td>";
        echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PatientEmail']) . "</td>";
        echo "<td>" . $row['PatientDOB'] . "</td>";
        echo "<td>$temp_password</td>";
        echo "<td style='color:green'>✅ Установлен</td>";
        echo "</tr>";
    } else {
        echo "<tr style='background-color:#ffebee'>";
        echo "<td>$id</td>";
        echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PatientEmail']) . "</td>";
        echo "<td>" . $row['PatientDOB'] . "</td>";
        echo "<td>$temp_password</td>";
        echo "<td style='color:red'>❌ Ошибка: " . mysqli_error($con) . "</td>";
        echo "</tr>";
    }
}
echo "</table>";

echo "<h3>✅ Готово! Установлено паролей: $updated</h3>";
echo "<h3>🔑 Теперь попробуйте войти:</h3>";
echo "<ul>";
echo "<li><strong>Страница входа:</strong> <a href='index.php'>index.php</a></li>";
echo "<li><strong>Логин:</strong> ваш email или номер телефона</li>";
echo "<li><strong>Пароль:</strong> дата рождения в формате ГГГГММДД</li>";
echo "</ul>";
?>
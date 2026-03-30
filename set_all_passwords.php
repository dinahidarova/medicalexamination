<?php
require_once 'hms/include/config.php';

echo "<h2>Установка паролей для всех пациентов</h2>";

$query = "SELECT ID, PatientName, PatientDOB FROM tblpatient";
$result = mysqli_query($con, $query);

$updated = 0;
$errors = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['ID'];
    $name = $row['PatientName'];
    $dob = $row['PatientDOB'];
    $password = date('Ymd', strtotime($dob));
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    // Обновляем пароль
    $update = "UPDATE tblpatient SET passwords = '$hashed' WHERE ID = $id";
    if (mysqli_query($con, $update)) {
        $updated++;
        echo "✅ ID $id: $name - пароль установлен: <strong>$password</strong><br>";
    } else {
        $errors++;
        echo "❌ ID $id: $name - ошибка: " . mysqli_error($con) . "<br>";
    }
}

echo "<hr>";
echo "<h3>Результат: обновлено $updated пациентов, ошибок: $errors</h3>";
echo "<p>Теперь вы можете войти с:</p>";
echo "<ul>";
echo "<li><strong>Логин:</strong> номер телефона или email</li>";
echo "<li><strong>Пароль:</strong> дата рождения в формате ГГГГММДД</li>";
echo "</ul>";
echo "<a href='index.php'>Перейти к форме входа</a>";
?>
<?php
// check_phones.php
require_once 'hms/include/config.php';

echo "<h2>Проверка номеров телефонов в БД</h2>";

$query = "SELECT ID, PatientName, PatientContno FROM tblpatient";
$result = mysqli_query($con, $query);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Имя</th><th>Телефон (в БД)</th><th>Тип</th><th>Очищенный номер</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $phone_raw = $row['PatientContno'];
    $phone_clean = preg_replace('/[^0-9]/', '', (string)$phone_raw);
    
    echo "<tr>";
    echo "<td>{$row['ID']}</td>";
    echo "<td>{$row['PatientName']}</td>";
    echo "<td><code>" . htmlspecialchars($phone_raw) . "</code></td>";
    echo "<td>" . gettype($phone_raw) . "</td>";
    echo "<td><code>$phone_clean</code></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Тестовый вход:</h3>";
echo "<form method='POST' action='hms/patient/auth.php'>";
echo "<input type='text' name='login' placeholder='Номер телефона' style='padding:5px;'>";
echo "<input type='password' name='password' placeholder='Пароль' style='padding:5px;'>";
echo "<button type='submit'>Войти</button>";
echo "</form>";
?>
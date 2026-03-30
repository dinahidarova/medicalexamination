<?php
require_once 'hms/include/config.php';

echo "<h2>Проверка паролей пациентов</h2>";

$query = "SELECT ID, PatientName, PatientDOB, passwords FROM tblpatient";
$result = mysqli_query($con, $query);

echo "<table border='1' cellpadding='8'>";
echo "<tr style='background:#f0f0f0'>
        <th>ID</th>
        <th>Имя</th>
        <th>Дата рождения</th>
        <th>Ожидаемый пароль</th>
        <th>Пароль в БД</th>
        <th>Статус</th>
      </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $expected_password = date('Ymd', strtotime($row['PatientDOB']));
    $has_password = !empty($row['passwords']);
    
    $status = '';
    $status_color = '';
    
    if (!$has_password) {
        $status = '❌ ПАРОЛЬ НЕ УСТАНОВЛЕН';
        $status_color = 'red';
    } elseif (password_verify($expected_password, $row['passwords'])) {
        $status = '✅ ПАРОЛЬ ВЕРНЫЙ';
        $status_color = 'green';
    } else {
        $status = '⚠️ ПАРОЛЬ НЕ СОВПАДАЕТ';
        $status_color = 'orange';
    }
    
    echo "<tr>";
    echo "<td>{$row['ID']}</td>";
    echo "<td>{$row['PatientName']}</td>";
    echo "<td>{$row['PatientDOB']}</td>";
    echo "<td><strong>$expected_password</strong></td>";
    echo "<td>" . ($has_password ? substr($row['passwords'], 0, 20) . '...' : 'NULL') . "</td>";
    echo "<td style='color:$status_color; font-weight:bold;'>$status</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>🔧 Решение: установить пароли для всех пациентов</h3>";
echo "<a href='set_all_passwords.php' style='background:#4CAF50; color:white; padding:10px; text-decoration:none;'>Установить пароли для всех пациентов</a>";
?>
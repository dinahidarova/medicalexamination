<?php
require_once 'hms/include/config.php';

// Получаем всех пациентов
$query = "SELECT ID, PatientDOB, passwords FROM tblpatient";
$result = mysqli_query($conn, $query);

$updated = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['ID'];
    $current_password = $row['passwords'];
    
    // Если пароль пустой или не захеширован
    if (empty($current_password)) {
        // Устанавливаем временный пароль (дата рождения)
        $temp_password = date('Ymd', strtotime($row['PatientDOB']));
        $new_hash = password_hash($temp_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE tblpatient SET passwords = '$new_hash' WHERE ID = $id";
        mysqli_query($conn, $update_query);
        $updated++;
        echo "Обновлен пациент ID $id: пароль = дата рождения<br>";
    } elseif (strlen($current_password) < 60 && strlen($current_password) != 32) {
        // Простой текст, хешируем
        $new_hash = password_hash($current_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE tblpatient SET passwords = '$new_hash' WHERE ID = $id";
        mysqli_query($conn, $update_query);
        $updated++;
        echo "Захеширован пароль для пациента ID $id<br>";
    } elseif (strlen($current_password) == 32 && ctype_xdigit($current_password)) {
        // MD5 хеш, обновляем на password_hash
        $new_hash = password_hash($current_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE tblpatient SET passwords = '$new_hash' WHERE ID = $id";
        mysqli_query($conn, $update_query);
        $updated++;
        echo "Обновлен MD5 пароль для пациента ID $id<br>";
    }
}

echo "<br><strong>Готово! Обновлено пациентов: $updated</strong>";
?>
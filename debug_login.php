<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Диагностика входа</h2>";

// Подключаем конфигурацию
require_once __DIR__ . '/hms/include/config.php';

if (!isset($con) || !$con) {
    die("❌ Нет подключения к БД");
}

echo "✅ Подключение к БД работает<br><br>";

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $password = $_POST['password'];
    
    echo "<h3>📝 Данные из формы:</h3>";
    echo "Логин: <strong>" . htmlspecialchars($login) . "</strong><br>";
    echo "Пароль: <strong>" . htmlspecialchars($password) . "</strong><br><br>";
    
    // Ищем пациента
    $query = "SELECT * FROM tblpatient 
              WHERE PatientEmail = '$login' OR PatientContno = '$login'";
    
    echo "<h3>📊 SQL запрос:</h3>";
    echo "<code>" . htmlspecialchars($query) . "</code><br><br>";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die("❌ Ошибка запроса: " . mysqli_error($con));
    }
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        echo "<h3>✅ Пациент найден:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Поле</th><th>Значение</th></tr>";
        echo "<tr><td>ID</td><td>" . $row['ID'] . "</td></tr>";
        echo "<tr><td>Имя</td><td>" . htmlspecialchars($row['PatientName']) . "</td></tr>";
        echo "<tr><td>Email</td><td>" . htmlspecialchars($row['PatientEmail']) . "</td></tr>";
        echo "<tr><td>Телефон</td><td>" . $row['PatientContno'] . "</td></tr>";
        echo "<tr><td>Дата рождения</td><td>" . $row['PatientDOB'] . "</td></tr>";
        echo "<tr><td>Пароль в БД</td><td>" . ($row['passwords'] ? substr($row['passwords'], 0, 20) . "..." : "ПУСТО") . "</td></tr>";
        echo "</table><br>";
        
        // Проверяем пароль
        $temp_password = date('Ymd', strtotime($row['PatientDOB']));
        echo "<h3>🔑 Проверка пароля:</h3>";
        echo "Ожидаемый пароль (дата рождения): <strong>$temp_password</strong><br>";
        echo "Введенный пароль: <strong>$password</strong><br>";
        
        if ($password == $temp_password) {
            echo "<p style='color:green; font-weight:bold;'>✅ ПАРОЛЬ ВЕРНЫЙ (совпадает с датой рождения)!</p>";
            
            // Устанавливаем сессию
            $_SESSION['id'] = $row['ID'];
            $_SESSION['name'] = $row['PatientName'];
            $_SESSION['role'] = 'patient';
            $_SESSION['email'] = $row['PatientEmail'];
            
            echo "<p style='color:green;'>✅ Сессия установлена! Перенаправление через 2 секунды...</p>";
            echo "<meta http-equiv='refresh' content='2;url=hms/patient/index.php'>";
            
        } elseif (!empty($row['passwords']) && password_verify($password, $row['passwords'])) {
            echo "<p style='color:green; font-weight:bold;'>✅ ПАРОЛЬ ВЕРНЫЙ (хешированный)!</p>";
            
            $_SESSION['id'] = $row['ID'];
            $_SESSION['name'] = $row['PatientName'];
            $_SESSION['role'] = 'patient';
            $_SESSION['email'] = $row['PatientEmail'];
            
            echo "<p style='color:green;'>✅ Сессия установлена! Перенаправление через 2 секунды...</p>";
            echo "<meta http-equiv='refresh' content='2;url=hms/patient/index.php'>";
            
        } else {
            echo "<p style='color:red; font-weight:bold;'>❌ ПАРОЛЬ НЕВЕРНЫЙ!</p>";
            
            // Дополнительная диагностика
            if (!empty($row['passwords'])) {
                echo "<h4>Детали пароля:</h4>";
                echo "Длина пароля в БД: " . strlen($row['passwords']) . "<br>";
                echo "Формат: ";
                if (strlen($row['passwords']) == 60 && substr($row['passwords'], 0, 4) == '$2y$') {
                    echo "bcrypt (правильный формат)<br>";
                    // Пробуем разные варианты
                    echo "Проверка с разными вариантами пароля:<br>";
                    echo "- Дата рождения ($temp_password): " . (password_verify($temp_password, $row['passwords']) ? "✅ подходит" : "❌ не подходит") . "<br>";
                    echo "- Введенный пароль ($password): " . (password_verify($password, $row['passwords']) ? "✅ подходит" : "❌ не подходит") . "<br>";
                } elseif (strlen($row['passwords']) == 32) {
                    echo "MD5 (старый формат, нужно обновить)<br>";
                } else {
                    echo "другой формат<br>";
                }
            } else {
                echo "<p>Пароль в БД пустой. Нужно установить пароль через set_passwords.php</p>";
            }
        }
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Пациент с логином '$login' НЕ НАЙДЕН!</p>";
        
        // Показываем всех пациентов для проверки
        echo "<h3>📋 Все пациенты в базе:</h3>";
        $all_patients = mysqli_query($con, "SELECT ID, PatientName, PatientEmail, PatientContno FROM tblpatient");
        if (mysqli_num_rows($all_patients) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Имя</th><th>Email</th><th>Телефон</th></tr>";
            while ($p = mysqli_fetch_assoc($all_patients)) {
                echo "<tr>";
                echo "<td>" . $p['ID'] . "</td>";
                echo "<td>" . htmlspecialchars($p['PatientName']) . "</td>";
                echo "<td>" . htmlspecialchars($p['PatientEmail']) . "</td>";
                echo "<td>" . $p['PatientContno'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Нет данных в таблице tblpatient";
        }
    }
} else {
    // Показываем форму для теста
    echo "<h3>📝 Тестовая форма входа</h3>";
    
    // Показываем первого пациента для примера
    $sample = mysqli_query($con, "SELECT PatientName, PatientEmail, PatientContno, PatientDOB FROM tblpatient LIMIT 1");
    if ($sample_row = mysqli_fetch_assoc($sample)) {
        $sample_password = date('Ymd', strtotime($sample_row['PatientDOB']));
        echo "<div style='background:#f0f0f0; padding:10px; margin:10px 0;'>";
        echo "<strong>📌 Пример для входа:</strong><br>";
        echo "Логин: <code>" . htmlspecialchars($sample_row['PatientEmail']) . "</code> или <code>" . $sample_row['PatientContno'] . "</code><br>";
        echo "Пароль: <code>$sample_password</code> (дата рождения)<br>";
        echo "</div>";
    }
    ?>
    
    <form method="POST" action="" style="border:1px solid #ccc; padding:20px; max-width:400px;">
        <h3>Введите данные для проверки:</h3>
        <div style="margin-bottom:10px;">
            <label>Логин (Email или телефон):</label><br>
            <input type="text" name="login" required style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom:10px;">
            <label>Пароль:</label><br>
            <input type="text" name="password" required style="width:100%; padding:8px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#4CAF50; color:white; border:none;">Проверить вход</button>
    </form>
    <?php
}
?>
<?php
// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Проверка данных пациента</h2>";

// Проверяем существование файла config.php
$config_file = __DIR__ . '/hms/include/config.php';
echo "<p>Путь к config.php: " . $config_file . "</p>";

if (!file_exists($config_file)) {
    die("❌ Файл config.php не найден по пути: " . $config_file);
}

// Подключаем config.php
require_once $config_file;

// Проверяем, что переменная $con существует
if (!isset($con)) {
    die("❌ Переменная \$con не определена после подключения config.php");
}

if (!$con) {
    die("❌ Подключение к БД не установлено: " . mysqli_connect_error());
}

echo "✅ Подключение к БД успешно<br><br>";

// Проверяем наличие таблицы tblpatient
$check_table = "SHOW TABLES LIKE 'tblpatient'";
$table_result = mysqli_query($con, $check_table);

if (!$table_result) {
    die("❌ Ошибка запроса: " . mysqli_error($con));
}

if (mysqli_num_rows($table_result) == 0) {
    die("❌ Таблица 'tblpatient' не найдена в базе данных 'hms'");
}

echo "✅ Таблица 'tblpatient' существует<br><br>";

// Получаем первого пациента
$query = "SELECT ID, PatientName, PatientEmail, PatientContno, PatientDOB, passwords FROM tblpatient LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result) {
    die("❌ Ошибка запроса: " . mysqli_error($con));
}

if ($row = mysqli_fetch_assoc($result)) {
    echo "<h3>📋 Данные первого пациента:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Поле</th><th>Значение</th></tr>";
    echo "<tr><td>ID</td><td>" . $row['ID'] . "</td></tr>";
    echo "<tr><td>Имя</td><td>" . htmlspecialchars($row['PatientName']) . "</td></tr>";
    echo "<tr><td>Email</td><td>" . htmlspecialchars($row['PatientEmail']) . "</td></tr>";
    echo "<tr><td>Телефон</td><td>" . $row['PatientContno'] . "</td></tr>";
    echo "<tr><td>Дата рождения</td><td>" . $row['PatientDOB'] . "</td></tr>";
    echo "<tr><td>Пароль в БД</td><td>" . (empty($row['passwords']) ? "<span style='color:red'>НЕ УСТАНОВЛЕН</span>" : "Установлен") . "</td></tr>";
    echo "</table>";
    
    // Проверяем временный пароль
    $temp_password = date('Ymd', strtotime($row['PatientDOB']));
    echo "<h3>🔑 Проверка пароля:</h3>";
    echo "Временный пароль (дата рождения): <strong>$temp_password</strong><br>";
    
    if (empty($row['passwords'])) {
        echo "<p style='color:red'>❌ Пароль не установлен! Запустите скрипт установки паролей.</p>";
    } else {
        // Проверяем формат пароля
        if (strlen($row['passwords']) == 60 && substr($row['passwords'], 0, 4) == '$2y$') {
            echo "✅ Пароль захеширован (bcrypt)<br>";
            if (password_verify($temp_password, $row['passwords'])) {
                echo "✅ Временный пароль <strong>'$temp_password'</strong> подходит!<br>";
            } else {
                echo "❌ Временный пароль НЕ подходит<br>";
            }
        } elseif (strlen($row['passwords']) == 32) {
            echo "⚠️ Пароль в формате MD5 (нужно обновить)<br>";
        } else {
            echo "⚠️ Пароль в формате: " . $row['passwords'] . "<br>";
        }
    }
    
    echo "<h3>🔧 Рекомендации для входа:</h3>";
    echo "<ul>";
    echo "<li><strong>Логин:</strong> " . $row['PatientEmail'] . " (или телефон: " . $row['PatientContno'] . ")</li>";
    echo "<li><strong>Пароль:</strong> $temp_password</li>";
    echo "</ul>";
    
} else {
    echo "❌ В таблице tblpatient нет данных";
}

// Показываем структуру таблицы
echo "<h3>📊 Структура таблицы tblpatient:</h3>";
$columns = mysqli_query($con, "SHOW COLUMNS FROM tblpatient");
if ($columns) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Default</th></tr>";
    while ($col = mysqli_fetch_assoc($columns)) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Не удалось получить структуру таблицы";
}
?>
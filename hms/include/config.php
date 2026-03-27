<?php
// Устанавливает подключение к MySQL-серверу
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS' ,'');
define('DB_NAME', 'hms');

// Создаем подключение
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Проверяем подключение
if (mysqli_connect_errno()) {
    die("Ошибка при подключении к базе данных: " . mysqli_connect_error());
}

// Устанавливаем кодировку
mysqli_set_charset($con, "utf8");

// Для отладки - убедимся, что подключение работает
// Раскомментируйте следующую строку для проверки
echo "Подключение к БД успешно";
?>
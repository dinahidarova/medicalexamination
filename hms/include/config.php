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

// Ключ для шифрования (храните в отдельном файле вне проекта!)
define('ENCRYPTION_KEY', 'ваш-секретный-ключ-32-символа-!!!');
define('ENCRYPTION_CIPHER', 'AES-256-CBC');

function encrypt_data($data) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_CIPHER));
    $encrypted = openssl_encrypt($data, ENCRYPTION_CIPHER, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_data($data) {
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_CIPHER);
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, ENCRYPTION_CIPHER, ENCRYPTION_KEY, 0, $iv);
}

?>
<?php
// test_mail.php
require_once 'hms/include/mail.php';

// Отправляем тест на тот же ящик (с которого отправляем)
$your_email = "huidarova@yandex.ru";  // ЗАМЕНИТЕ

$result = send_email($your_email, 'Тест отправки почты', '<p>Письмо успешно отправлено через Mail.ru SMTP!</p>');

if($result) {
    echo "✅ Письмо успешно отправлено на $your_email<br>";
    echo "Проверьте почту (возможно, в папке Спам)";
} else {
    echo "❌ Ошибка при отправке письма<br>";
    echo "Проверьте настройки SMTP в OpenServer";
}
?>
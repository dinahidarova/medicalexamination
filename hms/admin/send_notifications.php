<?php
require 'C:/ospanel/vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Настройка подключения к БД
$conn = mysqli_connect("localhost", "root", "", "hms");
if (!$conn) {
    die("Ошибка подключения к БД: " . mysqli_connect_error());
}

// Функция отправки письма
function sendEmail($to, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.yandex.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'medical-examination-system@yandex.ru'; // ⚠️ замени
        $mail->Password = 'sfwksjcdcekqprrx';            // ⚠️ замени на пароль приложения
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('medical-examination-system@yandex.ru', 'Медцентр');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo "✔️ Уведомление отправлено: $name ($to)<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ Ошибка отправки $name ($to): " . $mail->ErrorInfo . "<br>";
        return false;
    }
}

// Получение подходящих пациентов
$query = "SELECT p.ID, p.PatientName, p.PatientEmail, p.PatientDOB,
       (YEAR(CURDATE()) - YEAR(p.PatientDOB)) as age,
       a.last_checkup
FROM tblpatient p
LEFT JOIN (
    SELECT userId, MAX(appointmentDate) as last_checkup
    FROM appointment
    WHERE isCompleted = 1
    GROUP BY userId
) a ON p.ID = a.userId
WHERE (
    (YEAR(CURDATE()) - YEAR(p.PatientDOB)) BETWEEN 18 AND 39
    AND (a.last_checkup IS NULL OR DATEDIFF(CURDATE(), a.last_checkup) > 1095)
) OR (
    (YEAR(CURDATE()) - YEAR(p.PatientDOB)) >= 40
    AND (a.last_checkup IS NULL OR DATEDIFF(CURDATE(), a.last_checkup) > 365)
)";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Ошибка запроса: " . mysqli_error($conn));
}

echo "<h3>Результаты отправки уведомлений:</h3>";

while ($row = mysqli_fetch_assoc($result)) {
    $name = $row['PatientName'];
    $email = $row['PatientEmail'];
    $age = $row['age'];
    $period = $age >= 40 ? '1 год' : '3 года';

    // Пример шаблона письма
    $subject = "Пора на диспансеризацию!";
    $body = "
        <p>Уважаемый(ая) <strong>$name</strong>,</p>
        <p>Напоминаем, что вам рекомендуется пройти диспансеризацию раз в $period.</p>
        <p>Свяжитесь с нами для записи: <strong>8-800-123-4567</strong><br>
        <em>Медицинский Центр 'Здоровье'</em></p>
    ";

    sendEmail($email, $name, $subject, $body);
}
?>

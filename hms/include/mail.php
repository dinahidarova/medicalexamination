<?php
// hms/include/mail.php
// 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../phpmailer/src/Exception.php';


function send_email($to, $subject, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Настройки сервера
        $mail->isSMTP();
        $mail->Host = 'smtp.yandex.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'medical-examination-system@mail.ru';   // email
        $mail->Password = 'Qbx9OzFDvV0QYp4Del9S'; // ПАРОЛЬ_ПРИЛОЖЕНИЯ
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // или 'tls'
$mail->Port = 587;
        
        // Отправитель и получатель
        $mail->setFrom('medical-examination-system@mail.ru', 'Система диспансеризации');
        $mail->addAddress($to);
        
       $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Ошибка отправки: " . $mail->ErrorInfo);
        return false;
    }
}
?>
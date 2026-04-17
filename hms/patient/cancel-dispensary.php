<?php
session_start();
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/mail.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];
$dispensary_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($dispensary_id > 0) {
    // Получаем данные о записи и пациенте
    $check = mysqli_query($con, "SELECT d.*, p.PatientEmail, p.PatientName, p.PatientContno 
                                  FROM dispensarization d
                                  JOIN tblpatient p ON p.ID = d.patientId
                                  WHERE d.id = '$dispensary_id' AND d.patientId = '$patient_id' AND d.status = 'in_progress'");
    
    if(mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $disp_date = $row['dispDate'];
        $patient_email = $row['PatientEmail'];
        $patient_name = $row['PatientName'];
        
        mysqli_query($con, "DELETE FROM dispensarization WHERE id = '$dispensary_id'");
        
        // Отправляем email-уведомление об отмене
        $subject = "Запись на диспансеризацию отменена";
        $message = "
            <p>Уважаемый(ая) <strong>$patient_name</strong>!</p>
            <p>Ваша запись на диспансеризацию, назначенная на <strong>" . date('d.m.Y', strtotime($disp_date)) . "</strong>, была отменена.</p>
            <p>Если вы не отменяли запись, пожалуйста, свяжитесь с регистратурой по телефону: +7 (843) 123-45-67.</p>
            <hr>
            <p>Вы можете записаться на новую дату в личном кабинете.</p>
        ";
        send_email($patient_email, $subject, $message);
        
        $_SESSION['msg'] = "Запись на диспансеризацию успешно отменена. Уведомление отправлено на почту.";
    } else {
        $_SESSION['msg'] = "Запись не найдена или уже завершена";
    }
}

header("Location: dashboard.php");
exit();
?>
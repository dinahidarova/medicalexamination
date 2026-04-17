<?php
// cron_reminder.php - запускать ежедневно (например, через планировщик задач)
require_once 'hms/include/config.php';
require_once 'hms/include/mail.php';

// Находим диспансеризации на завтра
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$query = "SELECT d.*, p.PatientEmail, p.PatientName, p.PatientContno 
          FROM dispensarization d
          JOIN tblpatient p ON p.ID = d.patientId
          WHERE d.dispDate = '$tomorrow' AND d.status = 'in_progress'";
$result = mysqli_query($con, $query);

$count = 0;
while($row = mysqli_fetch_assoc($result)) {
    $subject = "Напоминание о диспансеризации";
    $message = "
        <p>Уважаемый(ая) <strong>{$row['PatientName']}</strong>!</p>
        <p>Напоминаем, что <strong>завтра (" . date('d.m.Y', strtotime($row['dispDate'])) . ")</strong> у вас запланирована диспансеризация.</p>
        <p><strong>Время:</strong> 09:00 - 16:00</p>
        <p><strong>Что взять с собой:</strong> паспорт, полис ОМС</p>
        <p>Пожалуйста, не опаздывайте. При себе иметь маску и сменную обувь.</p>
        <hr>
        <p>Для отмены записи войдите в личный кабинет.</p>
    ";
    send_email($row['PatientEmail'], $subject, $message);
    $count++;
}

echo "Уведомления отправлены для $count пациентов";
?>
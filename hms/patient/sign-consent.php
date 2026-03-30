<?php
session_start();
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/logging.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

$patient_id = $_SESSION['id'];
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Получаем текущий статус согласия
$check = mysqli_query($con, "SELECT consent_given FROM tblpatient WHERE ID = '$patient_id'");
$current = mysqli_fetch_assoc($check);
$is_signed = ($current['consent_given'] == 1);

// Обработка подписания
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'sign') {
        $update = "UPDATE tblpatient SET consent_given = 1, consent_date = NOW(), consent_ip = '$ip_address' WHERE ID = '$patient_id'";
        if(mysqli_query($con, $update)) {
            log_action($patient_id, 'patient', "Подписано согласие на обработку ПДн", $con);
            header("Location: dashboard.php?msg=signed");
            exit();
        }
    } elseif($_POST['action'] == 'withdraw') {
        $update = "UPDATE tblpatient SET consent_given = 0, consent_date = NULL, consent_ip = NULL WHERE ID = '$patient_id'";
        if(mysqli_query($con, $update)) {
            log_action($patient_id, 'patient', "Отозвано согласие на обработку ПДн", $con);
            header("Location: dashboard.php?msg=withdrawn");
            exit();
        }
    }
}

// Если согласие уже подписано - перенаправляем на страницу отзыва
if($is_signed && !isset($_GET['action'])) {
    header("Location: withdraw-consent.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Согласие на обработку персональных данных</title>
<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">📝 Согласие на обработку персональных данных</h3>
                    </div>
                    <div class="panel-body">
                        <div class="consent-text" style="height: 350px; overflow-y: scroll; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; background: #f9f9f9;">
                            <h4>Согласие на обработку персональных данных</h4>
                            <p>Я, <strong><?php echo $_SESSION['name']; ?></strong>,</p>
                            <p>в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных» даю согласие на обработку моих персональных данных в информационной системе «Диспансеризация».</p>
                            
                            <h5>Перечень персональных данных:</h5>
                            <ul>
                                <li>Фамилия, имя, отчество;</li>
                                <li>Дата рождения;</li>
                                <li>Адрес места жительства;</li>
                                <li>Контактный телефон, адрес электронной почты;</li>
                                <li>Номер полиса ОМС;</li>
                                <li>Данные о состоянии здоровья (диагнозы, результаты обследований).</li>
                            </ul>
                            
                            <h5>Цели обработки:</h5>
                            <ul>
                                <li>Проведение диспансеризации населения;</li>
                                <li>Ведение электронной медицинской карты;</li>
                                <li>Организация записи на прием к врачу;</li>
                                <li>Информирование о результатах обследований.</li>
                            </ul>
                            
                            <h5>Права субъекта персональных данных:</h5>
                            <ul>
                                <li>Право на получение информации об обработке ПДн;</li>
                                <li>Право на отзыв согласия в любое время;</li>
                                <li>Право на удаление своих данных.</li>
                            </ul>
                            
                            <p>Настоящее согласие действует бессрочно до момента его отзыва.</p>
                            
                            <p>Дата: "___" ________ 20___ г.</p>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="sign">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="confirm_consent" required>
                                    Я ознакомлен(а) с условиями и даю согласие на обработку моих персональных данных
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success">✅ Подтвердить согласие</button>
                            <a href="dashboard.php" class="btn btn-default">Отмена</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('include//footer.php');?>
</body>
</html>
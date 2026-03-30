<?php
session_start();
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/logging.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем информацию о подписанном согласии
$check = mysqli_query($con, "SELECT consent_given, consent_date, consent_ip FROM tblpatient WHERE ID = '$patient_id'");
$current = mysqli_fetch_assoc($check);

if($current['consent_given'] != 1) {
    header("Location: sign-consent.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_withdraw'])) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $update = "UPDATE tblpatient SET consent_given = 0, consent_date = NULL, consent_ip = NULL WHERE ID = '$patient_id'";
    if(mysqli_query($con, $update)) {
        log_action($patient_id, 'patient', "Отозвано согласие на обработку ПДн с IP: $ip_address", $con);
        
        // Завершаем сессию и выходим из системы
        session_destroy();
        header("Location: ../../index.php?msg=consent_withdrawn");
        exit();
    } else {
        $error = "Ошибка при отзыве согласия";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отзыв согласия на обработку персональных данных</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">⚠️ Отзыв согласия на обработку персональных данных</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <div class="alert alert-warning">
                            <strong>Внимание!</strong>
                            <ul>
                                <li>Отзыв согласия приведет к немедленному выходу из системы</li>
                                <li>Вы не сможете записываться на прием к врачу</li>
                                <li>Вы не сможете просматривать результаты обследований</li>
                            </ul>
                            <p>Для восстановления доступа потребуется повторное подписание согласия.</p>
                        </div>
                        
                        <div class="well well-sm">
                            <strong>Информация о подписанном согласии:</strong><br>
                            Дата подписания: <?php echo date('d.m.Y H:i:s', strtotime($current['consent_date'])); ?><br>
                            IP-адрес: <?php echo $current['consent_ip']; ?>
                        </div>
                        
                        <form method="POST">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="confirm_withdraw" required>
                                    Я подтверждаю отзыв согласия на обработку моих персональных данных
                                </label>
                            </div>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите отозвать согласие?');">
                                ❌ Отозвать согласие и выйти
                            </button>
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
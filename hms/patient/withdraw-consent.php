<?php
session_start();
require_once __DIR__ . '/../include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['id'];
    
    // Отзываем согласие
    $update = "UPDATE tblpatient SET consent_given = 0, consent_date = NULL WHERE ID = '$patient_id'";
    mysqli_query($con, $update);
    
    // Логируем
    require_once __DIR__ . '/../include/logging.php';
    log_action($patient_id, 'patient', 'Отзыв согласия на обработку ПДн', $con);
    
    // Завершаем сессию
    session_destroy();
    header("Location: ../../index.php?withdrawn=1");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Отзыв согласия</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <div class="alert alert-warning">
            <h3>Внимание!</h3>
            <p>Отзыв согласия на обработку персональных данных приведет к удалению вашей учетной записи и всех связанных данных.</p>
            <p>Вы не сможете пользоваться системой диспансеризации.</p>
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Отозвать согласие</button>
            <a href="dashboard.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
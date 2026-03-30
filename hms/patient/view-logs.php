<?php
session_start();
require_once __DIR__ . '/../include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем логи пользователя
$logs_query = "SELECT action, ip_address, user_agent, created_at 
               FROM logs 
               WHERE user_id = '$patient_id' AND user_role = 'patient' 
               ORDER BY created_at DESC 
               LIMIT 50";
$logs_result = mysqli_query($con, $logs_query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Журнал действий - Пациент</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
</head>
<body>
    <div id="app">
        <?php include('../include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('../include/header.php'); ?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">📋 Журнал моих действий</h1>
                                <p>История операций с вашими персональными данными</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Журнал действий</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Дата и время</th>
                                                <th>Действие</th>
                                                <th>IP-адрес</th>
                                                <th>Устройство</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($logs_result) > 0): ?>
                                                <?php while($log = mysqli_fetch_assoc($logs_result)): ?>
                                                <tr>
                                                    <td><?php echo date('d.m.Y H:i:s', strtotime($log['created_at'])); ?></td>
                                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                                    <td><code><?php echo $log['ip_address']; ?></code></td>
                                                    <td><?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)) . '...'; ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Журнал действий пуст</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-muted small">
                                    <i class="fa fa-info-circle"></i> 
                                    Журнал содержит записи о всех действиях, связанных с доступом к вашим персональным данным.
                                    Данные хранятся в течение 1 года.
                                </p>
                                <a href="dashboard.php" class="btn btn-default">← Вернуться в личный кабинет</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('../include/footer.php'); ?>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
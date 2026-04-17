<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: 199 index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем историю диспансеризаций (завершенные и просроченные)
$history_query = "SELECT * FROM dispensarization 
                  WHERE patientId = '$patient_id' 
                  AND (status = 'completed' OR dispDate < CURDATE())
                  ORDER BY dispDate DESC";
$history_result = mysqli_query($con, $history_query);

// Получаем статистику
$total = mysqli_num_rows($history_result);
$completed = 0;
$has_deviations = false;

while($row = mysqli_fetch_assoc($history_result)) {
    if($row['status'] == 'completed') {
        $completed++;
    }
}
mysqli_data_seek($history_result, 0);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Медицинская история - Пациент</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
</head>
<body>
    <div id="app">
        <?php include('include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('include/header.php'); ?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">📋 Медицинская история</h1>
                                <p>История прохождения диспансеризации</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Медицинская история</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Блок статистики -->
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-sm-6">
                                        <div class="panel panel-default text-center">
                                            <div class="panel-body">
                                                <i class="fa fa-calendar fa-3x text-primary"></i>
                                                <h3><?php echo $total; ?></h3>
                                                <p>Всего диспансеризаций</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="panel panel-success text-center">
                                            <div class="panel-body">
                                                <i class="fa fa-check-circle fa-3x text-success"></i>
                                                <h3><?php echo $completed; ?></h3>
                                                <p>Завершено</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- История диспансеризаций -->
                                <?php if(mysqli_num_rows($history_result) == 0): ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        У вас пока нет завершенных диспансеризаций.
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Дата</th>
                                                    <th>Статус</th>
                                                    <th>Результат</th>
                                                    <th>Действие</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                                                <tr>
                                                    <td><?php echo date('d.m.Y', strtotime($row['dispDate'])); ?>
                                                    <td>
                                                        <?php if($row['status'] == 'completed'): ?>
                                                            <span class="label label-success">✅ Завершена</span>
                                                        <?php else: ?>
                                                            <span class="label label-danger">❌ Не завершена</span>
                                                        <?php endif; ?>
                                                    
                                                    <td>
                                                        <?php if($row['status'] == 'completed'): ?>
                                                            <a href="examination-results.php" class="btn btn-success btn-sm">
                                                                <i class="fa fa-file-text-o"></i> Посмотреть заключение
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    
                                                    <td>
                                                        <?php if($row['status'] == 'completed'): ?>
                                                            <a href="examination-results.php" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-chart-line"></i> Результаты
                                                            </a>
                                                        <?php endif; ?>
                                                    
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="text-center" style="margin-top: 20px;">
                                    <a href="dashboard.php" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Вернуться в личный кабинет
                                    </a>
                                    <a href="book-appointment.php" class="btn btn-primary">
                                        <i class="fa fa-calendar-plus-o"></i> Записаться на диспансеризацию
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
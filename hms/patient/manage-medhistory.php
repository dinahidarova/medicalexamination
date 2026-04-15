<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем всю историю обследований (сортировка от новых к старым)
$history_query = "SELECT 
    mh.*,
    d.doctorName,
    ds.doctorSpecialization
FROM tblmedicalhistory mh
LEFT JOIN doctors d ON d.id = mh.DoctorID
LEFT JOIN doctorspecilization ds ON ds.doctorSpecializationId = d.doctorSpecializationId
WHERE mh.PatientID = '$patient_id'
ORDER BY mh.CreationDate DESC";

$history_result = mysqli_query($con, $history_query);

// Получаем записи на прием (визиты)
$appointments_query = "SELECT 
    a.*,
    d.doctorName,
    ds.doctorSpecialization
FROM appointment a
LEFT JOIN doctors d ON d.id = a.doctorId
LEFT JOIN doctorspecilization ds ON ds.doctorSpecializationId = a.doctorSpecializationId
WHERE a.userId = '$patient_id'
ORDER BY a.appointmentDate DESC";

$appointments_result = mysqli_query($con, $appointments_query);

// Подсчет статистики
$total_visits = mysqli_num_rows($appointments_result);
$total_exams = mysqli_num_rows($history_result);
$completed_exams = 0;
$has_deviations = false;

while($row = mysqli_fetch_assoc($history_result)) {
    if(!empty($row['FinalConclusion']) && strpos(mb_strtolower($row['FinalConclusion']), 'норм') === false) {
        $has_deviations = true;
    }
}
mysqli_data_seek($history_result, 0); // Сброс указателя
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Медицинская история - Пациент</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
</head>
<body>
    <div id="app">
        <?php include('include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('include/header.php'); ?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <!-- start: PAGE TITLE -->
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">📋 Медицинская история</h1>
                                <p>Все обследования и визиты к врачам</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Медицинская история</span></li>
                            </ol>
                        </div>
                    </section>
                    <!-- end: PAGE TITLE -->
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Блок статистики -->
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-sm-4">
                                        <div class="panel panel-default text-center">
                                            <div class="panel-body">
                                                <i class="fa fa-calendar fa-3x text-primary"></i>
                                                <h3><?php echo $total_visits; ?></h3>
                                                <p>Всего визитов к врачам</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="panel panel-default text-center">
                                            <div class="panel-body">
                                                <i class="fa fa-stethoscope fa-3x text-primary"></i>
                                                <h3><?php echo $total_exams; ?></h3>
                                                <p>Всего обследований</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="panel panel-<?php echo $has_deviations ? 'warning' : 'success'; ?> text-center">
                                            <div class="panel-body">
                                                <i class="fa fa-heartbeat fa-3x <?php echo $has_deviations ? 'text-warning' : 'text-success'; ?>"></i>
                                                <h3><?php echo $has_deviations ? '⚠️ Требуется внимание' : '✅ В норме'; ?></h3>
                                                <p>Общее состояние здоровья</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Вкладки для переключения между разделами -->
                                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                                    <li role="presentation" class="active">
                                        <a href="#examinations" aria-controls="examinations" role="tab" data-toggle="tab">
                                            <i class="fa fa-stethoscope"></i> Результаты обследований
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#appointments" aria-controls="appointments" role="tab" data-toggle="tab">
                                            <i class="fa fa-calendar"></i> История визитов
                                        </a>
                                    </li>
                                </ul>
                                
                                <!-- Содержимое вкладок -->
                                <div class="tab-content">
                                    <!-- Вкладка: Результаты обследований -->
                                    <div role="tabpanel" class="tab-pane active" id="examinations">
                                        <?php if(mysqli_num_rows($history_result) == 0): ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                У вас пока нет результатов обследований.
                                            </div>
                                        <?php else: ?>
                                            <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                                                <div class="panel panel-<?php 
                                                    if(!empty($row['FinalConclusion']) && strpos(mb_strtolower($row['FinalConclusion']), 'норм') === false) echo 'warning';
                                                    elseif(!empty($row['FinalConclusion'])) echo 'success';
                                                    else echo 'default';
                                                ?>">
                                                    <div class="panel-heading">
                                                        <i class="fa fa-calendar"></i>
                                                        <strong> Дата: <?php echo date('d.m.Y H:i', strtotime($row['CreationDate'])); ?></strong>
                                                        <?php if(!empty($row['doctorName'])): ?>
                                                            <span class="pull-right">
                                                                <i class="fa fa-user-md"></i> Врач: <?php echo htmlspecialchars($row['doctorName']); ?>
                                                                <?php if(!empty($row['doctorSpecialization'])): ?>
                                                                    (<?php echo htmlspecialchars($row['doctorSpecialization']); ?>)
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <?php if(!empty($row['BloodPressure'])): ?>
                                                                <div class="col-sm-3">
                                                                    <strong><i class="fa fa-heartbeat"></i> Давление:</strong>
                                                                    <?php echo htmlspecialchars($row['BloodPressure']); ?> мм рт.ст.
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if(!empty($row['BloodSugar'])): ?>
                                                                <div class="col-sm-3">
                                                                    <strong><i class="fa fa-tint"></i> Сахар:</strong>
                                                                    <?php echo htmlspecialchars($row['BloodSugar']); ?> ммоль/л
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if(!empty($row['Weight'])): ?>
                                                                <div class="col-sm-3">
                                                                    <strong><i class="fa fa-balance-scale"></i> Вес:</strong>
                                                                    <?php echo htmlspecialchars($row['Weight']); ?> кг
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if(!empty($row['Temperature'])): ?>
                                                                <div class="col-sm-3">
                                                                    <strong><i class="fa fa-thermometer-half"></i> Температура:</strong>
                                                                    <?php echo htmlspecialchars($row['Temperature']); ?> °C
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if(!empty($row['MedicalPres'])): ?>
                                                            <div class="alert alert-info" style="margin-top: 15px;">
                                                                <strong><i class="fa fa-stethoscope"></i> Заключение:</strong><br>
                                                                <?php echo nl2br(htmlspecialchars($row['MedicalPres'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(!empty($row['FinalConclusion'])): ?>
                                                            <div class="alert alert-<?php 
                                                                if(strpos(mb_strtolower($row['FinalConclusion']), 'норм') !== false) echo 'success';
                                                                else echo 'warning';
                                                            ?>" style="margin-top: 10px;">
                                                                <strong><i class="fa fa-flag-checkered"></i> Итоговое заключение:</strong><br>
                                                                <?php echo nl2br(htmlspecialchars($row['FinalConclusion'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Вкладка: История визитов -->
                                    <div role="tabpanel" class="tab-pane" id="appointments">
                                        <?php if(mysqli_num_rows($appointments_result) == 0): ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                У вас пока нет записей на прием.
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Дата</th>
                                                            <th>Время</th>
                                                            <th>Врач</th>
                                                            <th>Специализация</th>
                                                            <th>Статус</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while($app = mysqli_fetch_assoc($appointments_result)): ?>
                                                            <tr>
                                                                <td><?php echo date('d.m.Y', strtotime($app['appointmentDate'])); ?></td>
                                                                <td><?php echo $app['appointmentTime'] ? date('H:i', strtotime($app['appointmentTime'])) : '—'; ?></td>
                                                                <td><?php echo htmlspecialchars($app['doctorName'] ?? 'Не назначен'); ?></td>
                                                                <td><?php echo htmlspecialchars($app['doctorSpecialization'] ?? '—'); ?></td>
                                                                <td>
                                                                    <?php if($app['isCompleted'] == 1): ?>
                                                                        <span class="label label-success"><i class="fa fa-check"></i> Состоялся</span>
                                                                    <?php elseif(strtotime($app['appointmentDate']) < time()): ?>
                                                                        <span class="label label-danger"><i class="fa fa-times"></i> Пропущен</span>
                                                                    <?php else: ?>
                                                                        <span class="label label-info"><i class="fa fa-clock-o"></i> Запланирован</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="text-center" style="margin-top: 20px;">
                                    <a href="dashboard.php" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Вернуться в личный кабинет
                                    </a>
                                    <a href="examination-results.php" class="btn btn-primary">
                                        <i class="fa fa-chart-line"></i> Результаты диспансеризации
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
        <?php include('include/setting.php'); ?>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        jQuery(document).ready(function() {
            Main.init();
        });
    </script>
</body>
</html>
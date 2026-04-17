<?php
session_start();

// Проверка авторизации пациента
if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

// Подключаем конфигурацию - используем правильный путь и переменную $con
require_once __DIR__ . '/../include/config.php';

// Проверяем, что подключение есть
if (!isset($con) || !$con) {
    die("Ошибка подключения к базе данных");
}
// Получаем данные пациента
$patient_id = $_SESSION['id'];
$query = "SELECT * FROM tblpatient WHERE ID = '$patient_id'";
$result = mysqli_query($con, $query);
$patient = mysqli_fetch_assoc($result);

// Получаем количество записей на прием
$appointments_query = "SELECT COUNT(*) as count FROM appointment WHERE userId = '$patient_id' AND appointmentDate >= CURDATE()";
$appointments_result = mysqli_query($con, $appointments_query);
$appointments = mysqli_fetch_assoc($appointments_result);
$active_appointments = $appointments['count'];

// Получаем историю посещений (завершенные)
$history_query = "SELECT COUNT(*) as count FROM appointment WHERE userId = '$patient_id' AND isCompleted = 1";
$history_result = mysqli_query($con, $history_query);
$history = mysqli_fetch_assoc($history_result);
$completed_visits = $history['count'];

// Получаем последнюю диспансеризацию
$dispensary_query = "SELECT * FROM dispensarization WHERE patientId = '$patient_id' ORDER BY dispDate DESC LIMIT 1";
$dispensary_result = mysqli_query($con, $dispensary_query);
$dispensary = mysqli_fetch_assoc($dispensary_result);

// Получаем результаты обследований
$medical_query = "SELECT COUNT(*) as count FROM tblmedicalhistory WHERE PatientID = '$patient_id'";
$medical_result = mysqli_query($con, $medical_query);
$medical = mysqli_fetch_assoc($medical_result);
$exams_count = $medical['count'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Пациент | Личный кабинет</title>
    
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" media="screen">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
    <?php if(isset($_GET['first_login'])): ?>
    <script>
        alert('Добро пожаловать! Ваш временный пароль - дата рождения. Рекомендуем сменить пароль в настройках профиля.');
    </script>
    <?php endif; ?>
</head>
<body>
    <div id="app">        
        <?php include('include//sidebar.php');?>
        <div class="app-content">
            <?php include('include//header.php');?>
            
            <!-- end: TOP NAVBAR -->
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <!-- start: PAGE TITLE -->
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">Личный кабинет пациента</h1>
                                <p>Добро пожаловать, <?php echo htmlspecialchars($patient['PatientName']); ?>!</p>
                            </div>
                            <ol class="breadcrumb">
                                <li>
                                    <span>Пациент</span>
                                </li>
                                <li class="active">
                                    <span>Главная</span>
                                </li>
                            </ol>
                        </div>
                    </section>
                    <!-- end: PAGE TITLE -->
                    
                    <!-- start: ИНФОРМАЦИОННЫЕ КАРТОЧКИ -->
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <!-- Карточка профиля -->
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x"> 
                                            <i class="fa fa-square fa-stack-2x text-primary"></i> 
                                            <i class="fa fa-user-md fa-stack-1x fa-inverse"></i> 
                                        </span>
                                        <h2 class="StepTitle">Мой профиль</h2>
                                        <p><strong>ФИО:</strong> <?php echo htmlspecialchars($patient['PatientName']); ?></p>
                                        <p><strong>Дата рождения:</strong> <?php echo date('d.m.Y', strtotime($patient['PatientDOB'])); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['PatientEmail']); ?></p>
                                        <p><strong>Телефон:</strong> <?php echo $patient['PatientContno']; ?></p>
                                        <p class="links cl-effect-1">
                                            <a href="edit-profile.php">
                                                Редактировать профиль
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            

                            
                            <!-- Карточка записей на прием -->
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x"> 
                                            <i class="fa fa-square fa-stack-2x text-primary"></i> 
                                            <i class="fa fa-calendar fa-stack-1x fa-inverse"></i> 
                                        </span>
                                        <h2 class="StepTitle">Мои визиты</h2>
                                        <p><strong>Активных записей:</strong> <?php echo $active_appointments; ?></p>
                                        <p><strong>Завершенных визитов:</strong> <?php echo $completed_visits; ?></p>
                                        <p class="cl-effect-1">
                                            <a href="appointment-history.php">
                                                История посещений
                                            </a>
                                        </p>
                                        <p class="cl-effect-1">
                                            <a href="book-appointment.php">
                                                Записаться на прием
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Карточка диспансеризации -->
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x"> 
                                            <i class="fa fa-square fa-stack-2x text-primary"></i> 
                                            <i class="fa fa-heartbeat fa-stack-1x fa-inverse"></i> 
                                        </span>
                                        <h2 class="StepTitle">Диспансеризация</h2>
                                        <?php if($dispensary): ?>
                                            <p><strong>Дата:</strong> <?php echo date('d.m.Y', strtotime($dispensary['dispDate'])); ?></p>
                                            <p><strong>Статус:</strong> 
                                                <?php if($dispensary['status'] == 'completed'): ?>
                                                    <span style="color:green;">✅ Завершена</span>
                                                <?php else: ?>
                                                    <span style="color:orange;">🔄 В процессе</span>
                                                <?php endif; ?>
                                            </p>
                                            <p><strong>Обследований:</strong> <?php echo $exams_count; ?></p>
                                        <?php else: ?>
                                            <p>Нет активной диспансеризации</p>
                                            <p><a href="start-dispensary.php">Начать диспансеризацию</a></p>
                                        <?php endif; ?>
                                        <p class="cl-effect-1">
                                            <a href="examination-results.php">
                                                Результаты обследований
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end: ИНФОРМАЦИОННЫЕ КАРТОЧКИ -->
                    
  <!-- start: ЗАПЛАНИРОВАННЫЕ ДИСПАНСЕРИЗАЦИИ -->
<div class="container-fluid container-fullw bg-white" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-primary">🏥 Мои диспансеризации</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Дата диспансеризации</th>
                            <th>Статус</th>
                            <th>Прогресс</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Запрашиваем предстоящие и текущие диспансеризации
                        $dispensary_query = "SELECT * FROM dispensarization 
                                             WHERE patientId = '$patient_id' 
                                             ORDER BY dispDate DESC";
                        $dispensary_result = mysqli_query($con, $dispensary_query);
                        
                        if(mysqli_num_rows($dispensary_result) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center">Нет записей на диспансеризацию</td>
                            </tr>
                        <?php else: ?>
                            <?php while($disp = mysqli_fetch_assoc($dispensary_result)): 
                                // Определяем статус
                                $status_label = '';
                                $status_class = '';
                                $can_cancel = false;
                                
                                if($disp['status'] == 'completed') {
                                    $status_label = '✅ Завершена';
                                    $status_class = 'success';
                                } elseif(strtotime($disp['dispDate']) < time()) {
                                    $status_label = '❌ Просрочена';
                                    $status_class = 'danger';
                                } else {
                                    $status_label = '⏳ Запланирована';
                                    $status_class = 'info';
                                    $can_cancel = true;
                                }
                                
                                // Подсчитываем прогресс (сколько обследований выполнено)
                                $progress_query = "SELECT COUNT(*) as total FROM dispensary_exams WHERE dispensary_id = '{$disp['id']}'";
                                $progress_result = mysqli_query($con, $progress_query);
                                $total_exams = mysqli_fetch_assoc($progress_result)['total'] ?? 0;
                                
                                $completed_query = "SELECT COUNT(*) as done FROM dispensary_exams WHERE dispensary_id = '{$disp['id']}' AND status = 'completed'";
                                $completed_result = mysqli_query($con, $completed_query);
                                $completed_exams = mysqli_fetch_assoc($completed_result)['done'] ?? 0;
                                
                                $percent = $total_exams > 0 ? round($completed_exams / $total_exams * 100) : 0;
                            ?>
                            <tr>
                                <td><?php echo date('d.m.Y', strtotime($disp['dispDate'])); ?>
                                <td><span class="label label-<?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                <td>
                                    <div class="progress" style="margin-bottom: 0; width: 150px;">
                                        <div class="progress-bar progress-bar-success" role="progressbar" style="width: <?php echo $percent; ?>%;">
                                            <?php echo $percent; ?>%
                                        </div>
                                    </div>
                                
                                <td>
                                    <?php if($can_cancel): ?>
                                        <a href="cancel-dispensary.php?id=<?php echo $disp['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Отменить запись на диспансеризацию <?php echo date('d.m.Y', strtotime($disp['dispDate'])); ?>?')">
                                            <i class="fa fa-times"></i> Отменить
                                        </a>
                                    <?php else: ?>
                                        <a href="examination-results.php" class="btn btn-success btn-sm">
                                            <i class="fa fa-file-text-o"></i> Результаты
                                        </a>
                                    <?php endif; ?>
                                
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end: ЗАПЛАНИРОВАННЫЕ ДИСПАНСЕРИЗАЦИИ -->
                </div>
            </div>
        </div>
        <!-- start: FOOTER -->
        <?php include('include//footer.php');?>
        <!-- end: FOOTER -->
        
        <!-- start: SETTINGS -->
        <?php include('include//setting.php');?>
        <!-- end: SETTINGS -->
    </div>
    <!-- start: MAIN JAVASCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <!-- end: MAIN JAVASCRIPTS -->
    <!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
    <script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
    <script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <script src="vendor/autosize/autosize.min.js"></script>
    <script src="vendor/selectFx/classie.js"></script>
    <script src="vendor/selectFx/selectFx.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
    <!-- start: CLIP-TWO JAVASCRIPTS -->
    <script src="assets/js/main.js"></script>
    <!-- start: JavaScript Event Handlers for this page -->
    <script src="assets/js/form-elements.js"></script>
    <script>
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
    <!-- end: JavaScript Event Handlers for this page -->
    <!-- end: CLIP-TWO JAVASCRIPTS -->
</body>
</html>
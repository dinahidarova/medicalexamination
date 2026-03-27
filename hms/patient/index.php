<?php
session_start();
require_once __DIR__ . '/../include/config.php';

// Проверка авторизации пациента
if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../index.php");
    exit();
}

// Получаем данные пациента
$patient_id = $_SESSION['id'];
$query = "SELECT * FROM tblpatient WHERE ID = '$patient_id'";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

// Получаем количество записей на прием
$appointments_query = "SELECT COUNT(*) as count FROM appointment WHERE userId = '$patient_id' AND appointmentDate >= CURDATE()";
$appointments_result = mysqli_query($conn, $appointments_query);
$appointments = mysqli_fetch_assoc($appointments_result);

// Получаем результаты диспансеризации
$dispensary_query = "SELECT * FROM dispensarization WHERE patientId = '$patient_id' ORDER BY dispDate DESC LIMIT 1";
$dispensary_result = mysqli_query($conn, $dispensary_query);
$dispensary = mysqli_fetch_assoc($dispensary_result);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Пациент | Личный кабинет</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" />
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/plugins.css">
    <?php if(isset($_GET['first_login'])): ?>
    <script>
        alert('Добро пожаловать! Ваш временный пароль - дата рождения. Рекомендуем сменить пароль в настройках профиля.');
    </script>
    <?php endif; ?>
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
                                <h1 class="mainTitle">Личный кабинет пациента</h1>
                                <p>Добро пожаловать, <?php echo htmlspecialchars($patient['PatientName']); ?>!</p>
                            </div>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary"></i>
                                            <i class="fa fa-user-md fa-stack-1x fa-inverse"></i>
                                        </span>
                                        <h2 class="StepTitle">Мои данные</h2>
                                        <p><strong>ФИО:</strong> <?php echo htmlspecialchars($patient['PatientName']); ?></p>
                                        <p><strong>Дата рождения:</strong> <?php echo $patient['PatientDOB']; ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['PatientEmail']); ?></p>
                                        <p><strong>Телефон:</strong> <?php echo $patient['PatientContno']; ?></p>
                                        <p class="links cl-effect-1">
                                            <a href="edit-profile.php">Редактировать профиль</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary"></i>
                                            <i class="fa fa-calendar fa-stack-1x fa-inverse"></i>
                                        </span>
                                        <h2 class="StepTitle">Мои визиты</h2>
                                        <p>Активных записей: <strong><?php echo $appointments['count']; ?></strong></p>
                                        <p>История посещений и запись к врачу</p>
                                        <p class="cl-effect-1">
                                            <a href="appointments.php">Посмотреть визиты</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="panel panel-white no-radius text-center">
                                    <div class="panel-body">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-square fa-stack-2x text-primary"></i>
                                            <i class="fa fa-file-text fa-stack-1x fa-inverse"></i>
                                        </span>
                                        <h2 class="StepTitle">Диспансеризация</h2>
                                        <?php if($dispensary): ?>
                                            <p>Последняя: <?php echo $dispensary['dispDate']; ?></p>
                                            <p>Статус: <?php echo $dispensary['status'] == 'completed' ? '✅ Завершена' : '🔄 В процессе'; ?></p>
                                        <?php else: ?>
                                            <p>Нет записей о диспансеризации</p>
                                        <?php endif; ?>
                                        <p class="cl-effect-1">
                                            <a href="examination-results.php">Посмотреть результаты</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('../include/footer.php'); ?>
        </div>
    </div>
    
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем данные пациента
$patient = mysqli_fetch_assoc(mysqli_query($con, "SELECT PatientName, PatientDOB, PatientGender FROM tblpatient WHERE ID = '$patient_id'"));

// Функция определения возрастной группы
function getAgeGroup($birthDate) {
    $age = date('Y') - date('Y', strtotime($birthDate));
    if (date('md') < date('md', strtotime($birthDate))) {
        $age--;
    }
    
    if ($age >= 18 && $age <= 39) return '18-39';
    if ($age >= 40 && $age <= 64) return '40-64';
    if ($age >= 65) return '65+';
    return null;
}

// Функция получения обязательных обследований по возрасту
function getRequiredExams($ageGroup, $gender, $con) {
    $exams = [];
    
    $base_exams = [
        ['name' => 'Общий анализ крови', 'frequency' => '1 раз в год', 'icon' => 'fa-tint', 'description' => 'Для оценки общего состояния здоровья'],
        ['name' => 'Общий анализ мочи', 'frequency' => '1 раз в год', 'icon' => 'fa-flask', 'description' => 'Для выявления заболеваний почек и мочевыводящих путей'],
        ['name' => 'ЭКГ', 'frequency' => '1 раз в год', 'icon' => 'fa-heartbeat', 'description' => 'Для оценки работы сердца'],
        ['name' => 'Флюорография', 'frequency' => '1 раз в 2 года', 'icon' => 'fa-x-ray', 'description' => 'Для выявления заболеваний легких'],
    ];
    
    $age_exams = [];
    if ($ageGroup == '40-64') {
        $age_exams = [
            ['name' => 'Измерение внутриглазного давления', 'frequency' => '1 раз в год', 'icon' => 'fa-eye', 'description' => 'Для раннего выявления глаукомы'],
            ['name' => 'Маммография (женщины)', 'frequency' => '1 раз в 2 года', 'icon' => 'fa-female', 'description' => 'Для выявления заболеваний молочных желез'],
            ['name' => 'Анализ крови на ПСА (мужчины)', 'frequency' => '1 раз в год', 'icon' => 'fa-male', 'description' => 'Для раннего выявления заболеваний предстательной железы'],
        ];
    } elseif ($ageGroup == '65+') {
        $age_exams = [
            ['name' => 'Измерение внутриглазного давления', 'frequency' => '1 раз в год', 'icon' => 'fa-eye', 'description' => 'Для раннего выявления глаукомы'],
            ['name' => 'Маммография (женщины)', 'frequency' => '1 раз в год', 'icon' => 'fa-female', 'description' => 'Для выявления заболеваний молочных желез'],
            ['name' => 'Анализ крови на ПСА (мужчины)', 'frequency' => '1 раз в год', 'icon' => 'fa-male', 'description' => 'Для раннего выявления заболеваний предстательной железы'],
            ['name' => 'Денситометрия', 'frequency' => '1 раз в 2 года', 'icon' => 'fa-bone', 'description' => 'Для оценки плотности костной ткани'],
        ];
    }
    
    return array_merge($base_exams, $age_exams);
}

$ageGroup = getAgeGroup($patient['PatientDOB']);
$exams = getRequiredExams($ageGroup, $patient['PatientGender'], $con);

$completed = 0;
foreach ($exams as $exam) {
    $check = mysqli_query($con, "SELECT * FROM tblmedicalhistory WHERE PatientID = '$patient_id' AND MedicalPres LIKE '%{$exam['name']}%'");
    if(mysqli_num_rows($check) > 0) {
        $completed++;
    }
}
$total = count($exams);
$percent = $total > 0 ? round($completed / $total * 100) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>План диспансеризации - Пациент</title>
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
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">📋 План диспансеризации</h1>
                                <p>Обязательные обследования для <?php echo htmlspecialchars($patient['PatientName']); ?></p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>План диспансеризации</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Информационная карточка -->
<div class="panel panel-default" style="background: white;">
    <div class="panel-heading" style="background: #f5f5f5;">
        <h3 class="panel-title"><i class="fa fa-info-circle"></i> Информация о пациенте</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4">
                <strong style="color: #333;"><i class="fa fa-user"></i> Возрастная группа:</strong><br>
                <span style="color: #333; font-weight: normal;"><?php echo $ageGroup; ?> лет</span>
            </div>
            <div class="col-sm-4">
                <strong style="color: #333;"><i class="fa fa-venus-mars"></i> Пол:</strong><br>
                <span style="color: #333;"><?php echo ($patient['PatientGender'] == 'female') ? 'Женский' : 'Мужской'; ?></span>
            </div>
            <div class="col-sm-4">
                <strong style="color: #333;"><i class="fa fa-chart-line"></i> Прогресс прохождения:</strong>
                <div class="progress" style="margin-top: 5px;">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: <?php echo $percent; ?>%;">
                        <?php echo $percent; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                                
                                <!-- Список обследований -->
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-list"></i> Перечень обязательных обследований</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="center">№</th>
                                                        <th>Обследование</th>
                                                        <th>Периодичность</th>
                                                        <th>Описание</th>
                                                        <th>Статус</th>
                                                        <th>Действие</th>
                                                    </thead>
                                                <tbody>
                                                    <?php foreach($exams as $key => $exam): 
                                                        $is_completed = false;
                                                        $check = mysqli_query($con, "SELECT * FROM tblmedicalhistory WHERE PatientID = '$patient_id' AND MedicalPres LIKE '%{$exam['name']}%'");
                                                        if(mysqli_num_rows($check) > 0) {
                                                            $is_completed = true;
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $key + 1; ?> </td>
                                                        <td>
                                                            <i class="fa <?php echo $exam['icon']; ?> text-primary"></i>
                                                            <?php echo $exam['name']; ?>
                                                        </td>
                                                        <td><?php echo $exam['frequency']; ?></td>
                                                        <td><small style="color: #555;"><?php echo $exam['description']; ?></small></td>
                                                        <td>
                                                            <?php if($is_completed): ?>
                                                                <span class="label label-success"><i class="fa fa-check"></i> Пройдено</span>
                                                            <?php else: ?>
                                                                <span class="label label-warning"><i class="fa fa-clock-o"></i> Ожидает</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if(!$is_completed): ?>
                                                                <a href="book-appointment.php?exam=<?php echo urlencode($exam['name']); ?>" class="btn btn-primary btn-sm">
                                                                    <i class="fa fa-calendar"></i> Записаться
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-success btn-sm disabled" disabled>
                                                                    <i class="fa fa-check"></i> Выполнено
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="alert alert-info" style="margin-top: 20px;">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Рекомендация:</strong> Пройдите все обязательные обследования в рамках диспансеризации.
                                            Результаты будут отображаться в вашем личном кабинете.
                                        </div>
                                        
                                        <div class="text-center" style="margin-top: 20px;">
                                            <a href="dashboard.php" class="btn btn-default">
                                                <i class="fa fa-arrow-left"></i> Вернуться в личный кабинет
                                            </a>
                                            <a href="examination-results.php" class="btn btn-primary">
                                                <i class="fa fa-chart-line"></i> Результаты обследований
                                            </a>
                                        </div>
                                    </div>
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
</body>
</html>
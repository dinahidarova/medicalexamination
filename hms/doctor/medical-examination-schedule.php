<?php
session_start();
error_reporting(E_ALL);
include('include/config.php');

if(!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('location:logout.php');
    exit();
}

// Получаем текущий год и месяц
$currentYear = date('Y');
$currentMonth = date('m');
$monthNames = [
    1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 
    4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
    7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
    10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
];

// Обработка переключения месяцев
if(isset($_GET['month']) && isset($_GET['year'])) {
    $currentMonth = (int)$_GET['month'];
    $currentYear = (int)$_GET['year'];
    
    // Корректировка если вышли за границы
    if($currentMonth > 12) {
        $currentMonth = 1;
        $currentYear++;
    } elseif($currentMonth < 1) {
        $currentMonth = 12;
        $currentYear--;
    }
}

// Получаем все приёмы для текущего врача
$sql = "SELECT 
    a.id, a.appointmentDate, a.appointmentTime,
    p.PatientName, p.PatientDOB,
    TIMESTAMPDIFF(YEAR, p.PatientDOB, CURDATE()) as age,
    ds.doctorSpecialization
FROM appointment a
JOIN tblpatient p ON p.ID = a.userId
JOIN doctorspecilization ds ON ds.doctorSpecializationId = a.doctorSpecializationId
WHERE a.doctorId = '".$_SESSION['id']."'
ORDER BY a.appointmentDate, a.appointmentTime";

$result = mysqli_query($con, $sql);
$appointments = [];

while($row = mysqli_fetch_assoc($result)) {
    $date = date('Y-m-d', strtotime($row['appointmentDate']));
    $appointments[$date][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Календарь приёмов</title>
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
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .calendar {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .calendar th, .calendar td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            height: 100px;
            vertical-align: top;
        }
        .calendar th {
            background-color: #f2f2f2;
        }
        .day-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .appointment {
            font-size: 12px;
            margin: 2px 0;
            padding: 2px;
            border-radius: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .age-18-39 {
            background-color: #d4edda;
            border-left: 3px solid #28a745;
        }
        .age-40-plus {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }
        .month-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .today {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div id="app">        
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">Календарь приёмов</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li>
                                    <span>Врач</span>
                                </li>
                                <li class="active">
                                    <span>Календарь приёмов</span>
                                </li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="month-header">
                                    <a href="?month=<?= $currentMonth-1 ?>&year=<?= $currentYear ?>" class="btn btn-primary">
                                        <i class="fa fa-chevron-left"></i> Предыдущий месяц
                                    </a>
                                    <h2><?= $monthNames[$currentMonth] ?> <?= $currentYear ?></h2>
                                    <a href="?month=<?= $currentMonth+1 ?>&year=<?= $currentYear ?>" class="btn btn-primary">
                                        Следующий месяц <i class="fa fa-chevron-right"></i>
                                    </a>
                                </div>
                                
                                <table class="calendar">
                                    <thead>
                                        <tr>
                                            <th>Пн</th>
                                            <th>Вт</th>
                                            <th>Ср</th>
                                            <th>Чт</th>
                                            <th>Пт</th>
                                            <th>Сб</th>
                                            <th>Вс</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Создаем календарь на текущий месяц
                                        $firstDay = date('N', strtotime("$currentYear-$currentMonth-01"));
                                        $daysInMonth = date('t', strtotime("$currentYear-$currentMonth-01"));
                                        $today = date('Y-m-d');
                                        
                                        echo '<tr>';
                                        // Пустые ячейки перед первым днем месяца
                                        for($i = 1; $i < $firstDay; $i++) {
                                            echo '<td></td>';
                                        }
                                        
                                        // Ячейки с днями месяца
                                        for($day = 1; $day <= $daysInMonth; $day++) {
                                            $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                            $isToday = ($date == $today) ? 'today' : '';
                                            
                                            echo '<td class="'.$isToday.'">';
                                            echo '<div class="day-number">'.$day.'</div>';
                                            
                                            // Выводим приёмы на этот день
                                            if(isset($appointments[$date])) {
                                                foreach($appointments[$date] as $app) {
                                                    $ageClass = ($app['age'] >= 40) ? 'age-40-plus' : 'age-18-39';
                                                    echo '<div class="appointment '.$ageClass.'" title="'.$app['PatientName'].', '.$app['age'].' лет">';
                                                    echo $app['PatientName'].' ('.$app['age'].')';
                                                    echo '</div>';
                                                }
                                            }
                                            
                                            echo '</td>';
                                            
                                            // Переход на новую строку в конце недели
                                            if(($day + $firstDay - 1) % 7 == 0 && $day != $daysInMonth) {
                                                echo '</tr><tr>';
                                            }
                                        }
                                        
                                        // Пустые ячейки после последнего дня месяца
                                        $remainingCells = 7 - (($daysInMonth + $firstDay - 1) % 7);
                                        if($remainingCells < 7) {
                                            for($i = 0; $i < $remainingCells; $i++) {
                                                echo '<td></td>';
                                            }
                                        }
                                        
                                        echo '</tr>';
                                        ?>
                                    </tbody>
                                </table>
                                
                                <div class="legend mt-4">
                                    <h4>Условные обозначения:</h4>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="appointment age-18-39 mr-2" style="width: 20px; height: 20px;"></div>
                                        <span>Пациенты 18-39 лет (раз в 3 года)</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="appointment age-40-plus mr-2" style="width: 20px; height: 20px;"></div>
                                        <span>Пациенты 40+ лет (ежегодно)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php');?>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script>
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
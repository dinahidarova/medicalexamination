<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: 199 index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем итоговое заключение терапевта
$final_result = mysqli_query($con, "SELECT * FROM tblmedicalhistory 
    WHERE PatientID = '$patient_id' 
    AND FinalConclusion IS NOT NULL 
    AND FinalConclusion != ''
    ORDER BY CreationDate DESC 
    LIMIT 1");

$has_final = mysqli_num_rows($final_result) > 0;
$final = $has_final ? mysqli_fetch_assoc($final_result) : null;

// ========== НОВЫЙ КОД: ОПРЕДЕЛЕНИЕ ГРУППЫ ЗДОРОВЬЯ ==========
function getHealthGroup($final_conclusion, $has_deviations) {
    $conclusion_lower = mb_strtolower($final_conclusion);
    
    if (!$has_deviations) {
        return ['group' => 'I', 'label' => 'I группа - здоров', 'color' => 'success', 'description' => 'Хронические неинфекционные заболевания отсутствуют, факторы риска не выявлены.'];
    } elseif ($has_deviations && (strpos($conclusion_lower, 'фактор риска') !== false || strpos($conclusion_lower, 'незначительн') !== false)) {
        return ['group' => 'II', 'label' => 'II группа - факторы риска', 'color' => 'warning', 'description' => 'Выявлены факторы риска развития хронических неинфекционных заболеваний. Требуется профилактическое консультирование.'];
    } else {
        return ['group' => 'III', 'label' => 'III группа - требуется наблюдение', 'color' => 'danger', 'description' => 'Выявлены заболевания, требующие диспансерного наблюдения и лечения.'];
    }
}

$has_deviations = false;
if($has_final && !empty($final['FinalConclusion'])) {
    $conclusion_text = mb_strtolower($final['FinalConclusion']);
    if(strpos($conclusion_text, 'отклонени') !== false || strpos($conclusion_text, 'повышен') !== false) {
        $has_deviations = true;
    }
}

$health_info = null;
if($has_final) {
    $health_info = getHealthGroup($final['FinalConclusion'], $has_deviations);
}
// ========== КОНЕЦ НОВОГО КОДА ==========
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Результаты обследований - Пациент</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
                                <h1 class="mainTitle">📊 Результаты диспансеризации</h1>
                                <p>Итоговое заключение врача-терапевта</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Результаты обследований</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(!$has_final): ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>ℹ️ Результаты пока не готовы</strong><br>
                                        Ваши результаты обследований будут доступны после завершения диспансеризации и формирования итогового заключения врачом-терапевтом.
                                    </div>
                                <?php else: ?>
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <i class="fa fa-stethoscope"></i> 
                                                Итоговое заключение врача-терапевта
                                            </h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="well" style="background-color: #f9f9f9;">
                                                <?php echo nl2br(htmlspecialchars($final['FinalConclusion'])); ?>
                                            </div>
                                            
                                            <?php if(!empty($final['MedicalPres'])): ?>
                                            <div class="alert alert-info" style="margin-top: 15px;">
                                                <i class="fa fa-stethoscope"></i>
                                                <strong>📝 Рекомендации:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($final['MedicalPres'])); ?>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($therapist_name)): ?>
                                            <div class="text-right text-muted" style="margin-top: 15px;">
                                                <i class="fa fa-user-md"></i> Врач: <?php echo htmlspecialchars($therapist_name); ?>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="text-muted" style="margin-top: 15px; font-size: 12px;">
                                                <i class="fa fa-calendar"></i> Дата заключения: <?php echo date('d.m.Y', strtotime($final['CreationDate'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($health_info): ?>
<div class="alert alert-<?php echo $health_info['color']; ?>" style="margin-top: 15px;">
    <strong>Группа здоровья: <?php echo $health_info['label']; ?></strong><br>
    <?php echo $health_info['description']; ?>
</div>
<?php endif; ?>
                                
                                <div class="text-center" style="margin-top: 20px;">
                                    <a href="dashboard.php" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Вернуться в личный кабинет
                                    </a>
                                    <a href="dispensary_plan.php" class="btn btn-primary">
                                        <i class="fa fa-list"></i> План диспансеризации
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
    <div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-chart-line"></i> Динамика ключевых показателей</h4>
    </div>
    <div class="panel-body">
        <canvas id="healthChart" style="width: 100%; height: 300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- График динамики показателей -->
<div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-chart-line"></i> Динамика ключевых показателей</h4>
    </div>
    <div class="panel-body">
        <canvas id="healthChart" style="width: 100%; height: 300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php
// Получаем данные для графика ТОЛЬКО где есть числовые показатели
$chart_query = "SELECT 
    DATE_FORMAT(CreationDate, '%d.%m.%Y') as date,
    BloodPressure, 
    BloodSugar, 
    Weight, 
    Temperature
FROM tblmedicalhistory 
WHERE PatientID = '$patient_id' 
    AND (
        (BloodPressure IS NOT NULL AND BloodPressure != '') OR
        (BloodSugar IS NOT NULL AND BloodSugar != '') OR
        (Weight IS NOT NULL AND Weight != '') OR
        (Temperature IS NOT NULL AND Temperature != '')
    )
ORDER BY CreationDate ASC";

$chart_result = mysqli_query($con, $chart_query);

$dates = [];
$systolic = [];      // верхнее давление
$diastolic = [];     // нижнее давление
$sugar = [];
$weight = [];
$temperature = [];

while($row = mysqli_fetch_assoc($chart_result)) {
    $dates[] = $row['date'];
    
    // Парсим давление (формат "120/80" или "120")
    if(!empty($row['BloodPressure'])) {
        if(strpos($row['BloodPressure'], '/') !== false) {
            $bp = explode('/', $row['BloodPressure']);
            $systolic[] = (int)$bp[0];
            $diastolic[] = (int)$bp[1];
        } else {
            $systolic[] = (int)$row['BloodPressure'];
            $diastolic[] = null;
        }
    } else {
        $systolic[] = null;
        $diastolic[] = null;
    }
    
    // Сахар
    $sugar[] = !empty($row['BloodSugar']) ? (float)$row['BloodSugar'] : null;
    
    // Вес
    $weight[] = !empty($row['Weight']) ? (float)$row['Weight'] : null;
    
    // Температура
    $temperature[] = !empty($row['Temperature']) ? (float)$row['Temperature'] : null;
}
?>

const ctx = document.getElementById('healthChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [
            {
                label: 'Давление (верхнее), мм рт.ст.',
                data: <?php echo json_encode($systolic); ?>,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Уровень сахара, ммоль/л',
                data: <?php echo json_encode($sugar); ?>,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Вес, кг',
                data: <?php echo json_encode($weight); ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                title: {
                    display: true,
                    text: 'Значения показателей'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Дата обследования'
                }
            }
        }
    }
});
</script>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];

// Получаем итоговое заключение
$query = "SELECT * FROM tblmedicalhistory 
    WHERE PatientID = '$patient_id' 
    AND FinalConclusion IS NOT NULL 
    AND FinalConclusion != ''
    ORDER BY CreationDate DESC 
    LIMIT 1";

$result = mysqli_query($con, $query);
$has_final = mysqli_num_rows($result) > 0;
$final = $has_final ? mysqli_fetch_assoc($result) : null;

// Получаем имя врача
$therapist_name = '';
if($has_final && !empty($final['DoctorID'])) {
    $doc = mysqli_fetch_assoc(mysqli_query($con, "SELECT doctorName FROM doctors WHERE id = '{$final['DoctorID']}'"));
    $therapist_name = $doc['doctorName'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Результаты обследований</title>
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
                            </div>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(!$has_final): ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        Результаты пока не готовы. Они появятся после завершения диспансеризации.
                                    </div>
                                <?php else: ?>
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h4>🩺 Итоговое заключение врача-терапевта</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div style="background:#f9f9f9; padding:15px; border-radius:5px;">
                                                <?php echo nl2br(htmlspecialchars($final['FinalConclusion'])); ?>
                                            </div>
                                            
                                            <?php if(!empty($final['MedicalPres'])): ?>
                                                <div class="alert alert-info" style="margin-top:15px;">
                                                    <strong>📝 Рекомендации:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($final['MedicalPres'])); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="text-right text-muted" style="margin-top:15px;">
                                                <i class="fa fa-user-md"></i> Врач: <?php echo htmlspecialchars($therapist_name); ?><br>
                                                <i class="fa fa-calendar"></i> Дата: <?php echo date('d.m.Y', strtotime($final['CreationDate'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="dashboard.php" class="btn btn-default">← Вернуться</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
    </div>
</body>
</html>
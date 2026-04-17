<?php
session_start();
require_once 'include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: 199 index.php");
    exit();
}

$doctor_id = $_SESSION['id'];
$dispensary_id = isset($_GET['dispensary_id']) ? intval($_GET['dispensary_id']) : 0;

// Получаем информацию о диспансеризации и пациенте
$disp_query = "SELECT d.*, p.PatientName, p.PatientDOB 
               FROM dispensarization d
               JOIN tblpatient p ON p.ID = d.patientId
               WHERE d.id = '$dispensary_id'";
$disp_result = mysqli_query($con, $disp_query);
$disp = mysqli_fetch_assoc($disp_result);

// Получаем список обследований
$exams_query = "SELECT * FROM dispensary_exams WHERE dispensary_id = '$dispensary_id'";
$exams_result = mysqli_query($con, $exams_query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Обследования пациента</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div id="app">
        <?php include('include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('include/header.php'); ?>
            <div class="main-content">
                <div class="wrap-content container">
                    <h1 class="mainTitle">Обследования пациента: <?php echo $disp['PatientName']; ?></h1>
                    <p>Дата диспансеризации: <?php echo date('d.m.Y', strtotime($disp['dispDate'])); ?></p>
                    
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Обследование</th><th>Статус</th><th>Действие</th></tr>
                        </thead>
                        <tbody>
                            <?php while($exam = mysqli_fetch_assoc($exams_result)): ?>
                            <tr>
                                <td><?php echo $exam['exam_name']; ?>途
                                <td>
                                    <?php if($exam['status'] == 'completed'): ?>
                                        <span class="label label-success">Выполнено</span>
                                    <?php else: ?>
                                        <span class="label label-warning">Ожидает</span>
                                    <?php endif; ?>
                                途
                                <td>
                                    <?php if($exam['status'] != 'completed'): ?>
                                        <a href="update_exam_status.php?exam_id=<?php echo $exam['id']; ?>&dispensary_id=<?php echo $dispensary_id; ?>" 
                                           class="btn btn-success btn-sm">
                                            Отметить выполненным
                                        </a>
                                    <?php endif; ?>
                                途
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <a href="dashboard.php" class="btn btn-default">Назад</a>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
    </div>
</body>
</html>
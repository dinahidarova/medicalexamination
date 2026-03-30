<?php
session_start();
error_reporting(0);
include('include/config.php');
if(!isset($_SESSION['id']) || strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
} else {
    // Обработка отмены приема
    if(isset($_GET['cancel'])) {
        mysqli_query($con,"update appointment set doctorStatus='0' where id ='".$_GET['id']."'");
        $_SESSION['msg']="Appointment canceled !!";
    }
    
    // Обработка отметки о завершении приема
    if(isset($_POST['mark_completed'])) {
        $appointmentId = intval($_POST['appointment_id']);
        $doctorId = $_SESSION['id'];
        $summary = trim($_POST['medical_summary']);
        
        // Получаем данные из формы (если пусто — ставим NULL)
        $finalConclusion = isset($_POST['final_conclusion']) && !empty(trim($_POST['final_conclusion'])) 
            ? mysqli_real_escape_string($con, trim($_POST['final_conclusion'])) 
            : NULL;
        $bloodPressure = isset($_POST['blood_pressure']) && !empty(trim($_POST['blood_pressure'])) 
            ? mysqli_real_escape_string($con, trim($_POST['blood_pressure'])) 
            : NULL;
        $bloodSugar = isset($_POST['blood_sugar']) && !empty(trim($_POST['blood_sugar'])) 
            ? mysqli_real_escape_string($con, trim($_POST['blood_sugar'])) 
            : NULL;
        $temperature = isset($_POST['temperature']) && !empty(trim($_POST['temperature'])) 
            ? mysqli_real_escape_string($con, trim($_POST['temperature'])) 
            : NULL;
        $weight = isset($_POST['weight']) && !empty(trim($_POST['weight'])) 
            ? mysqli_real_escape_string($con, trim($_POST['weight'])) 
            : NULL;

        // Получаем ID пациента
        $getPatient = mysqli_query($con, "SELECT userId FROM appointment WHERE id='$appointmentId' AND doctorId='$doctorId'");
        $patientData = mysqli_fetch_assoc($getPatient);

        if (!$patientData) {
            die("❌ Не найден приём или врач не соответствует.");
        }

        $patientId = $patientData['userId'];

        // Формируем SQL запрос с правильной обработкой NULL
        $bloodPressureSql = $bloodPressure !== NULL ? "'$bloodPressure'" : "NULL";
        $bloodSugarSql = $bloodSugar !== NULL ? "'$bloodSugar'" : "NULL";
        $temperatureSql = $temperature !== NULL ? "'$temperature'" : "NULL";
        $weightSql = $weight !== NULL ? "'$weight'" : "NULL";
        $finalConclusionSql = $finalConclusion !== NULL ? "'$finalConclusion'" : "NULL";
        $summarySql = mysqli_real_escape_string($con, $summary);
        
        // Сохраняем медицинскую историю с итоговым заключением
        $insert = mysqli_query($con, "INSERT INTO tblmedicalhistory 
            (PatientID, DoctorID, BloodPressure, BloodSugar, Weight, Temperature, MedicalPres, FinalConclusion, CreationDate)
            VALUES 
            ('$patientId', '$doctorId', $bloodPressureSql, $bloodSugarSql, $weightSql, $temperatureSql, '$summarySql', $finalConclusionSql, NOW())");

        if (!$insert) {
            die("❌ Ошибка вставки в tblmedicalhistory: " . mysqli_error($con));
        }

        // Обновляем статус приёма
        mysqli_query($con, "UPDATE appointment SET isCompleted='1' WHERE id='$appointmentId' AND doctorId='$doctorId'");

        $_SESSION['msg'] = "Приём завершён и заключение сохранено!";
        header("Location: appointment-history.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Приёмы сегодня</title>
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
                                    <h1 class="mainTitle">Приёмы сегодня</h1>
                                </div>
                                <ol class="breadcrumb">
                                    <li><span>Врач</span></li>
                                    <li class="active"><span>Приёмы сегодня</span></li>
                                </ol>
                            </div>
                        </section>
                        
                        <div class="container-fluid container-fullw bg-white">
                            <div class="row">
                                <div class="col-md-12">
                                    <p style="color:red;"><?php echo htmlentities($_SESSION['msg']);?>
                                    <?php echo htmlentities($_SESSION['msg']="");?></p>    
                                    <table class="table table-hover" id="sample-table-1">
                                        <thead>
                                            <tr>
                                                <th class="center">№</th>
                                                <th>ФИО пациента</th>
                                                <th>Возраст</th>
                                                <th>Специализация врача</th>
                                                <th>Дата приёма</th>
                                                <th>Состояние</th>
                                                <th>Действие</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sql = mysqli_query($con, "SELECT 
                                            p.PatientName as fname,
                                            a.*,
                                            ds.doctorSpecialization,
                                            TIMESTAMPDIFF(YEAR, p.PatientDOB, CURDATE()) as patientAge
                                        FROM appointment a
                                        JOIN tblpatient p ON p.ID = a.userId
                                        JOIN doctorspecilization ds ON ds.doctorSpecializationId = a.doctorSpecializationId
                                        WHERE 
                                            a.doctorId = '".$_SESSION['id']."'
                                            AND a.appointmentDate = CURDATE()
                                        ORDER BY a.appointmentTime ASC");

                                        if (!$sql) {
                                            echo "<tr><td colspan='7'>Error: ".mysqli_error($con)."</td></tr>";
                                        } elseif (mysqli_num_rows($sql) == 0) {
                                            echo "技术<td colspan='7' class='text-center'>📭 На сегодня приёмов по диспансеризации нет</td></tr>";
                                        } else {
                                            $cnt = 1;
                                            while($row = mysqli_fetch_array($sql)) {
                                        ?>
                                        <tr>
                                            <td class="center"><?php echo $cnt;?>. </td>
                                            <td><?php echo htmlspecialchars($row['fname']);?></td>
                                            <td><?php echo htmlspecialchars($row['patientAge'] ?? 'N/A');?></td>
                                            <td><?php echo htmlspecialchars($row['doctorSpecialization']);?></td>
                                            <td><?php echo date("d.m.Y", strtotime($row['appointmentDate'])); ?></td>
                                            <td>
                                                <?php if($row['isCompleted'] == 1): ?>
                                                    <span class="label label-success">Приём состоялся</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Приём не состоялся</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['isCompleted'] == 0): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                    
                                                    <?php if (mb_strtolower($row['doctorSpecialization'], 'UTF-8') == 'терапевт'): ?>
                                                        <?php
                                                        // Получаем предыдущие заключения узких специалистов
                                                        $historyQuery = mysqli_query($con, "SELECT 
                                                                d.doctorName, 
                                                                ds.doctorSpecialization, 
                                                                mh.MedicalPres
                                                            FROM tblmedicalhistory mh
                                                            JOIN doctors d ON d.id = mh.DoctorID
                                                            JOIN doctorspecilization ds ON ds.doctorSpecializationId = d.doctorSpecializationId
                                                            WHERE mh.PatientID = '{$row['userId']}'
                                                              AND mh.DoctorID != '{$_SESSION['id']}'
                                                              AND mh.MedicalPres IS NOT NULL
                                                            ORDER BY mh.CreationDate DESC
                                                        ");
                                                        ?>
                                                        
                                                        <?php if (mysqli_num_rows($historyQuery) > 0): ?>
                                                            <div class="form-group" style="margin-bottom: 10px;">
                                                                <label>📋 Заключения узких специалистов:</label>
                                                                <div class="well well-sm" style="max-height: 200px; overflow-y: auto;">
                                                                    <?php while ($h = mysqli_fetch_assoc($historyQuery)): ?>
                                                                        <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                                                            <strong><?= htmlspecialchars($h['doctorName']) ?> (<?= htmlspecialchars($h['doctorSpecialization']) ?>):</strong><br>
                                                                            <?= htmlspecialchars($h['MedicalPres']) ?>
                                                                        </div>
                                                                    <?php endwhile; ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="form-group" style="margin-bottom: 10px;">
                                                            <label for="final_conclusion">🩺 Итоговое заключение терапевта:</label>
                                                            <textarea name="final_conclusion" class="form-control" rows="3" placeholder="Введите итоговое заключение на основе заключений специалистов..."></textarea>
                                                        </div>
                                                        
                                                        <div class="form-group" style="margin-bottom: 10px;">
                                                            <label>📊 Показатели (необязательно):</label>
                                                            <div class="row">
                                                                <div class="col-xs-3">
                                                                    <input type="text" name="blood_pressure" class="form-control input-sm" placeholder="Давление">
                                                                </div>
                                                                <div class="col-xs-3">
                                                                    <input type="text" name="blood_sugar" class="form-control input-sm" placeholder="Сахар">
                                                                </div>
                                                                <div class="col-xs-3">
                                                                    <input type="text" name="temperature" class="form-control input-sm" placeholder="Температура">
                                                                </div>
                                                                <div class="col-xs-3">
                                                                    <input type="text" name="weight" class="form-control input-sm" placeholder="Вес">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="form-group" style="margin-bottom: 10px;">
                                                        <textarea name="medical_summary" class="form-control" rows="2" placeholder="Введите медицинское заключение..." required></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" name="mark_completed" class="btn btn-success btn-xs" onclick="return confirm('Завершить приём?')">Завершить приём</button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php 
                                                $cnt++;
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('include/footer.php');?>
            <?php include('include/setting.php');?>
        </div>
        
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="vendor/modernizr/modernizr.js"></script>
        <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
        <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="vendor/switchery/switchery.min.js"></script>
        <script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
        <script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="vendor/autosize/autosize.min.js"></script>
        <script src="vendor/selectFx/classie.js"></script>
        <script src="vendor/selectFx/selectFx.js"></script>
        <script src="vendor/select2/select2.min.js"></script>
        <script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/form-elements.js"></script>
        <script>
            jQuery(document).ready(function() {
                Main.init();
                FormElements.init();
            });
        </script>
    </body>
</html>
<?php } ?>
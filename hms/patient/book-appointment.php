<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];
$success = false;
$error = '';

// Получаем список врачей по специальностям
$doctors_query = "SELECT d.id, d.doctorName, ds.doctorSpecialization 
                  FROM doctors d
                  JOIN doctorspecilization ds ON ds.doctorSpecializationId = d.doctorSpecializationId
                  ORDER BY ds.doctorSpecialization, d.doctorName";
$doctors_result = mysqli_query($con, $doctors_query);

// Получаем список специальностей для фильтра
$specializations_query = "SELECT doctorSpecializationId, doctorSpecialization FROM doctorspecilization ORDER BY doctorSpecialization";
$specializations_result = mysqli_query($con, $specializations_query);

// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = mysqli_real_escape_string($con, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($con, $_POST['appointment_time']);
    
    // Получаем специальность врача
    $spec_query = mysqli_query($con, "SELECT doctorSpecializationId FROM doctors WHERE id = '$doctor_id'");
    $spec = mysqli_fetch_assoc($spec_query);
    $spec_id = $spec['doctorSpecializationId'];
    
    // Проверяем, нет ли уже записи на это время
    $check_query = "SELECT * FROM appointment 
                    WHERE doctorId = '$doctor_id' 
                    AND appointmentDate = '$appointment_date' 
                    AND appointmentTime = '$appointment_time'";
    $check_result = mysqli_query($con, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $error = 'На выбранное время уже есть запись. Пожалуйста, выберите другое время.';
    } else {
        $insert = "INSERT INTO appointment (doctorSpecializationId, doctorId, userId, appointmentDate, appointmentTime, isCompleted) 
                   VALUES ('$spec_id', '$doctor_id', '$patient_id', '$appointment_date', '$appointment_time', 0)";
        
        if(mysqli_query($con, $insert)) {
            $success = true;
        } else {
            $error = 'Ошибка при записи: ' . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Запись на приём - Пациент</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
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
                                <h1 class="mainTitle">📅 Запись на приём к врачу</h1>
                                <p>Выберите врача и удобное время для посещения</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Запись на приём</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($success): ?>
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>✅ Запись успешно создана!</strong><br>
                                        Вы можете отслеживать статус записи в разделе "Мои визиты".
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($error): ?>
                                    <div class="alert alert-danger">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>❌ Ошибка:</strong> <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="form-horizontal">
                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-calendar-plus-o"></i> Выберите врача и время</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Специализация:</label>
                                                <div class="col-sm-6">
                                                    <select id="specialization_filter" class="form-control">
                                                        <option value="">Все специализации</option>
                                                        <?php while($spec = mysqli_fetch_assoc($specializations_result)): ?>
                                                            <option value="<?php echo $spec['doctorSpecialization']; ?>">
                                                                <?php echo htmlspecialchars($spec['doctorSpecialization']); ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Врач:</label>
                                                <div class="col-sm-6">
                                                    <select name="doctor_id" id="doctor_select" class="form-control" required>
                                                        <option value="">-- Выберите врача --</option>
                                                        <?php 
                                                        mysqli_data_seek($doctors_result, 0);
                                                        while($doc = mysqli_fetch_assoc($doctors_result)): 
                                                        ?>
                                                            <option value="<?php echo $doc['id']; ?>" data-specialization="<?php echo $doc['doctorSpecialization']; ?>">
                                                                <?php echo htmlspecialchars($doc['doctorName']) . ' (' . htmlspecialchars($doc['doctorSpecialization']) . ')'; ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Дата приёма:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="appointment_date" id="datepicker" class="form-control" placeholder="Выберите дату" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Время приёма:</label>
                                                <div class="col-sm-6">
                                                    <select name="appointment_time" class="form-control" required>
                                                        <option value="">-- Выберите время --</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="09:30:00">09:30</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="10:30:00">10:30</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="11:30:00">11:30</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="13:30:00">13:30</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="14:30:00">14:30</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="15:30:00">15:30</option>
                                                        <option value="16:00:00">16:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-6">
                                                    <button type="submit" name="book_appointment" class="btn btn-primary">
                                                        <i class="fa fa-calendar-check-o"></i> Записаться на приём
                                                    </button>
                                                    <a href="dashboard.php" class="btn btn-default">Отмена</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="alert alert-info" style="margin-top: 20px;">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Информация:</strong>
                                    <ul style="margin-top: 10px;">
                                        <li>Приём ведётся по предварительной записи</li>
                                        <li>При себе необходимо иметь паспорт и полис ОМС</li>
                                        <li>В случае невозможности прийти, пожалуйста, отмените запись заранее</li>
                                    </ul>
                                </div>
                                
                                <div class="text-center" style="margin-top: 20px;">
                                    <a href="dashboard.php" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Вернуться в личный кабинет
                                    </a>
                                    <a href="medical-history.php" class="btn btn-primary">
                                        <i class="fa fa-history"></i> Мои визиты
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
    
    <script>
        // Фильтр врачей по специализации
        const specializationFilter = document.getElementById('specialization_filter');
        const doctorSelect = document.getElementById('doctor_select');
        const doctors = Array.from(doctorSelect.options);
        
        specializationFilter.addEventListener('change', function() {
            const selectedSpec = this.value;
            
            doctors.forEach(option => {
                if(option.value === '') return;
                const spec = option.getAttribute('data-specialization');
                if(selectedSpec === '' || spec === selectedSpec) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            doctorSelect.value = '';
        });
        
        // Настройка календаря
        flatpickr("#datepicker", {
            locale: "ru",
            minDate: "today",
            dateFormat: "Y-m-d",
            disable: [
                function(date) {
                    return date.getDay() === 0;
                }
            ]
        });
    </script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
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
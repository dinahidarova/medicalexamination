<?php
session_start();
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/mail.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];
$success = false;
$error = '';

// Получаем возраст пациента
$age_query = "SELECT PatientDOB, PatientName, PatientEmail, TIMESTAMPDIFF(YEAR, PatientDOB, CURDATE()) as age FROM tblpatient WHERE ID = '$patient_id'";
$age_result = mysqli_query($con, $age_query);
$patient_data = mysqli_fetch_assoc($age_result);
$patient_age = $patient_data['age'];
$patient_email = $patient_data['PatientEmail'];
$patient_name = $patient_data['PatientName'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_dispensarization'])) {
    $dispensary_date = mysqli_real_escape_string($con, $_POST['dispensary_date']);
    
    // Проверка периодичности
    $last_query = "SELECT dispDate FROM dispensarization WHERE patientId = '$patient_id' AND status = 'completed' ORDER BY dispDate DESC LIMIT 1";
    $last_result = mysqli_query($con, $last_query);
    
    if(mysqli_num_rows($last_result) > 0) {
        $last_row = mysqli_fetch_assoc($last_result);
        $last_date = strtotime($last_row['dispDate']);
        $current_date = strtotime($dispensary_date);
        $years_diff = date('Y', $current_date) - date('Y', $last_date);
        
        if($patient_age < 40 && $years_diff < 3) {
            $error = 'Вы можете пройти диспансеризацию только раз в 3 года. Следующая запись возможна через ' . (3 - $years_diff) . ' год(а).';
        } elseif($patient_age >= 40 && $years_diff < 1) {
            $error = 'Вы можете пройти диспансеризацию только раз в год. Следующая запись возможна через ' . (12 - date('m', $current_date)) . ' месяцев.';
        }
    }
    
    // Проверяем, нет ли уже записи на эту дату
    $check_query = "SELECT * FROM dispensarization WHERE patientId = '$patient_id' AND dispDate = '$dispensary_date'";
    $check_result = mysqli_query($con, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $error = 'Вы уже записаны на диспансеризацию на эту дату.';
    } elseif(empty($error)) {
        // Создаем запись о диспансеризации
        $insert = "INSERT INTO dispensarization (patientId, dispDate, status) VALUES ('$patient_id', '$dispensary_date', 'in_progress')";
        
        if(mysqli_query($con, $insert)) {
            $dispensary_id = mysqli_insert_id($con);
            
            // Создаем список обследований по возрасту
            if($patient_age < 40) {
                $exams = ['Терапевт', 'Общий анализ крови', 'Общий анализ мочи', 'Флюорография', 'ЭКГ'];
            } else {
                $exams = ['Терапевт', 'Общий анализ крови', 'Общий анализ мочи', 'Флюорография', 'ЭКГ', 'Офтальмолог', 'Кардиолог'];
            }
            
            foreach($exams as $exam) {
                $insert_exam = "INSERT INTO dispensary_exams (dispensary_id, exam_name, status) VALUES ('$dispensary_id', '$exam', 'pending')";
                mysqli_query($con, $insert_exam);
            }
            
            // Отправляем email-уведомление
            $subject = "Запись на диспансеризацию подтверждена";
            $message = "
                <p>Уважаемый(ая) <strong>$patient_name</strong>!</p>
                <p>Вы успешно записаны на диспансеризацию.</p>
                <p><strong>Дата:</strong> " . date('d.m.Y', strtotime($dispensary_date)) . "</p>
                <p><strong>Время:</strong> 09:00 - 16:00</p>
                <p><strong>Место:</strong> Ваша поликлиника по месту прикрепления</p>
                <p><strong>Что взять с собой:</strong> паспорт, полис ОМС</p>
                <hr>
                <p>Для отмены записи войдите в личный кабинет.</p>
            ";
            send_email($patient_email, $subject, $message);
            
            $success = true;
        } else {
            $error = 'Ошибка при записи: ' . mysqli_error($con);
        }
    }
}

$planned_query = "SELECT * FROM dispensarization WHERE patientId = '$patient_id' AND dispDate >= CURDATE() AND status = 'in_progress' ORDER BY dispDate ASC";
$planned_result = mysqli_query($con, $planned_query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Запись на диспансеризацию</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
                                <h1 class="mainTitle">Запись на диспансеризацию</h1>
                                <p>Профилактический медицинский осмотр 1 раз в 3 года (до 39 лет) или ежегодно (40+ лет)</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Запись</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($success): ?>
                                    <div class="alert alert-success">
                                        <strong>Запись успешно создана!</strong><br>
                                        Уведомление отправлено на вашу электронную почту: <?php echo htmlspecialchars($patient_email); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Выберите дату</h4>
                                    </div>
                                    <div class="panel-body">
                                        <form method="POST" class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Дата диспансеризации:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="dispensary_date" id="datepicker" class="form-control" placeholder="Выберите дату" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-6">
                                                    <button type="submit" name="book_dispensarization" class="btn btn-primary">Записаться</button>
                                                    <a href="dashboard.php" class="btn btn-default">Отмена</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <?php if(mysqli_num_rows($planned_result) > 0): ?>
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Запланированные диспансеризации</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <thead><tr><th>Дата</th><th>Статус</th><th>Действие</th></tr></thead>
                                            <tbody>
                                                <?php while($row = mysqli_fetch_assoc($planned_result)): ?>
                                                <tr>
                                                    <td><?php echo date('d.m.Y', strtotime($row['dispDate'])); ?></td>
                                                    <td><span class="label label-warning">Ожидает</span></td>
                                                    <td><a href="cancel-dispensary.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Отменить запись?')">Отменить</a></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="text-center">
                                    <a href="dashboard.php" class="btn btn-default">Назад</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
    </div>
    <script>
        flatpickr("#datepicker", {
            locale: "ru",
            minDate: "today",
            dateFormat: "Y-m-d",
            disable: [function(date) { return date.getDay() === 0; }]
        });
    </script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
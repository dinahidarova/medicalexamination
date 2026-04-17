<?php
session_start();
require_once __DIR__ . '/include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['id'];
$error = '';
$success = '';

// Получаем текущие данные пациента
$query = "SELECT * FROM tblpatient WHERE ID = '$patient_id'";
$result = mysqli_query($con, $query);
$patient = mysqli_fetch_assoc($result);

// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    
    // Проверка email на уникальность (для других пациентов)
    $check_email = mysqli_query($con, "SELECT ID FROM tblpatient WHERE PatientEmail = '$email' AND ID != '$patient_id'");
    if(mysqli_num_rows($check_email) > 0) {
        $error = 'Этот email уже используется другим пациентом';
    } else {
        $update = "UPDATE tblpatient SET 
                   PatientContno = '$phone', 
                   PatientEmail = '$email', 
                   PatientAdd = '$address',
                   UpdationDate = NOW()
                   WHERE ID = '$patient_id'";
        
        if(mysqli_query($con, $update)) {
            $success = 'Профиль успешно обновлен';
            // Обновляем данные в сессии
            $_SESSION['email'] = $email;
            // Обновляем данные для отображения
            $patient['PatientContno'] = $phone;
            $patient['PatientEmail'] = $email;
            $patient['PatientAdd'] = $address;
        } else {
            $error = 'Ошибка при обновлении: ' . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Редактирование профиля</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
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
                                <h1 class="mainTitle">Редактирование профиля</h1>
                                <p>Изменение личных данных</p>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Пациент</span></li>
                                <li class="active"><span>Редактирование профиля</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <?php if($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <?php if($success): ?>
                                    <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php endif; ?>
                                
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Личные данные</h4>
                                    </div>
                                    <div class="panel-body">
                                        <form method="POST" class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">ФИО</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static"><?php echo htmlspecialchars($patient['PatientName']); ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Дата рождения</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static"><?php echo date('d.m.Y', strtotime($patient['PatientDOB'])); ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Пол</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static"><?php echo ($patient['PatientGender'] == 'female') ? 'Женский' : 'Мужской'; ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Телефон</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($patient['PatientContno']); ?>" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email</label>
                                                <div class="col-sm-9">
                                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($patient['PatientEmail']); ?>" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Адрес</label>
                                                <div class="col-sm-9">
                                                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($patient['PatientAdd'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                                        Сохранить изменения
                                                    </button>
                                                    <a href="dashboard.php" class="btn btn-default">Отмена</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
</body>
</html>
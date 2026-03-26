<?php
session_start();
error_reporting(E_ALL); // Включаем вывод ошибок для отладки
include('include/config.php');

if(!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('location:logout.php');
    exit();
}

// Получаем email врача из сессии
$doctorEmail = $_SESSION['dlogin'];

// Запрос для получения данных врача с названием специализации
$sql = "SELECT d.*, ds.doctorSpecialization 
        FROM doctors d
        LEFT JOIN doctorspecilization ds ON d.doctorSpecializationId = ds.doctorSpecializationId
        WHERE d.docEmail = '$doctorEmail'";

$result = mysqli_query($con, $sql);

if(!$result) {
    die('Ошибка запроса: ' . mysqli_error($con));
}

$doctorData = mysqli_fetch_assoc($result);

if(!$doctorData) {
    die('Данные врача не найдены');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Система | Профиль врача</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
	
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
                                <h1 class="mainTitle">Врач | Профиль врача</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Врач</span></li>
                                <li class="active"><span>Профиль врача</span></li>
                            </ol>
                        </div>
                    </section>
                    
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row margin-top-30">
                                    <div class="col-lg-8 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading">
                                                <h5 class="panel-title">Данные врача</h5>
                                            </div>
                                            <div class="panel-body">
                                                <h4>Профиль <?php echo htmlspecialchars($doctorData['doctorName']); ?></h4>
                                                <p><b>Дата регистрации:</b> <?php echo htmlspecialchars($doctorData['creationDate']); ?></p>
                                                <?php if($doctorData['updationDate']): ?>
                                                <p><b>Последнее обновление:</b> <?php echo htmlspecialchars($doctorData['updationDate']); ?></p>
                                                <?php endif; ?>
                                                <hr />
                                                
                                                <div class="form-group">
                                                    <label>Специализация</label>
                                                    <div class="form-control-static">
                                                        <?php echo htmlspecialchars($doctorData['doctorSpecialization'] ?? 'Не указана'); ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>ФИО врача</label>
                                                    <div class="form-control-static">
                                                        <?php echo htmlspecialchars($doctorData['doctorName']); ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Адрес клиники</label>
                                                    <div class="form-control-static">
                                                        <?php echo htmlspecialchars($doctorData['address']); ?>
                                                    </div>
                                                </div>

                                                

                                                <div class="form-group">
                                                    <label>Контактный телефон</label>
                                                    <div class="form-control-static">
                                                        <?php echo htmlspecialchars($doctorData['contactno']); ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Электронная почта</label>
                                                    <div class="form-control-static">
                                                        <?php echo htmlspecialchars($doctorData['docEmail']); ?>
                                                    </div>
                                                </div>
                                                
                                                <a href="dashboard.php" class="btn btn-o btn-primary">Назад</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        
        <?php include('include/footer.php');?>
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
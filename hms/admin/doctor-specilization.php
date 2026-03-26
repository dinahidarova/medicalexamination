<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

if(isset($_POST['submit'])) {
    $doctorspecilization = trim($_POST['doctorspecilization']);
    if(!empty($doctorspecilization)) {
        $sql = mysqli_query($con, "INSERT INTO doctorspecilization(doctorSpecialization) VALUES('".mysqli_real_escape_string($con, $doctorspecilization)."')");
        if($sql) {
            $_SESSION['msg'] = "Специализация врача успешно добавлена!";
        } else {
            $_SESSION['msg'] = "Ошибка: ".mysqli_error($con);
        }
    } else {
        $_SESSION['msg'] = "Пожалуйста, введите название специализации";
    }
}

// Обработка удаления
if(isset($_GET['del'])) {
    $sid = intval($_GET['id']);
    $sql = mysqli_query($con, "DELETE FROM doctorspecilization WHERE doctorSpecializationId = '$sid'");
    if($sql) {
        $_SESSION['msg'] = "Специализация удалена!";
    } else {
        $_SESSION['msg'] = "Ошибка удаления: ".mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Админ | Специализации врачей</title>
	
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
                                <h1 class="mainTitle">Админ | Управление специализациями</h1>
                            </div>
                        </section>

                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(isset($_SESSION['msg'])): ?>
                                    <div class="alert alert-info">
                                        <?php echo htmlspecialchars($_SESSION['msg']); 
                                        unset($_SESSION['msg']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Форма добавления специализации -->
                                <div class="row margin-top-30">
                                    <div class="col-lg-6 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading">
                                                <h5 class="panel-title">Добавить специализацию</h5>
                                            </div>
                                            <div class="panel-body">
                                                <form role="form" method="post">
                                                    <div class="form-group">
                                                        <label>Название специализации</label>
                                                        <input type="text" name="doctorspecilization" class="form-control" 
                                                               placeholder="Введите специализацию" required>
                                                    </div>
                                                    <button type="submit" name="submit" class="btn btn-primary">
                                                        Добавить
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Таблица со списком специализаций -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="over-title margin-bottom-15">Список <span class="text-bold">специализаций</span></h5>
                                        
                                        <table class="table table-hover" id="sample-table-1">
                                            <thead>
                                                <tr>
                                                    <th class="center">#</th>
                                                    <th>Специализация</th>
                                                    <th>Дата создания</th>
                                                    <th>Дата обновления</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = mysqli_query($con, "SELECT * FROM doctorspecilization");
                                                if(mysqli_num_rows($sql) > 0) {
                                                    $cnt = 1;
                                                    while($row = mysqli_fetch_assoc($sql)) {
                                                ?>
                                                <tr>
                                                    <td class="center"><?php echo $cnt; ?>.</td>
                                                    <td><?php echo htmlspecialchars($row['doctorSpecialization']); ?></td>
                                                    <td><?php echo $row['creationDate']; ?></td>
                                                    <td><?php echo $row['updationDate'] ?? '—'; ?></td>
                                                    <td>
                                                        <div class="visible-md visible-lg hidden-sm hidden-xs">
                                                            <a href="edit-doctor-specialization.php?id=<?php echo $row['doctorSpecializationId']; ?>" 
                                                               class="btn btn-transparent btn-xs" title="Редактировать">
                                                               <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <a href="doctor-specilization.php?id=<?php echo $row['doctorSpecializationId']; ?>&del=delete" 
                                                               onclick="return confirm('Вы уверены, что хотите удалить?')" 
                                                               class="btn btn-transparent btn-xs tooltips" title="Удалить">
                                                               <i class="fa fa-times"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                        $cnt++;
                                                    }
                                                } else {
                                                ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Нет добавленных специализаций</td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('include/footer.php');?>
			<!-- end: FOOTER -->
		
			<!-- start: SETTINGS -->
	<?php include('include/setting.php');?>
			
			<!-- end: SETTINGS -->
		</div>
		<!-- start: MAIN JAVASCRIPTS -->
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="vendor/modernizr/modernizr.js"></script>
		<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
		<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="vendor/switchery/switchery.min.js"></script>
		<!-- end: MAIN JAVASCRIPTS -->
		<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
		<script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
		<script src="vendor/autosize/autosize.min.js"></script>
		<script src="vendor/selectFx/classie.js"></script>
		<script src="vendor/selectFx/selectFx.js"></script>
		<script src="vendor/select2/select2.min.js"></script>
		<script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
		<script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
		<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<!-- start: CLIP-TWO JAVASCRIPTS -->
		<script src="assets/js/main.js"></script>
		<!-- start: JavaScript Event Handlers for this page -->
		<script src="assets/js/form-elements.js"></script>
		<script>
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
			});
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
<?php } ?>

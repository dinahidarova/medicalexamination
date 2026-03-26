<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');
require 'C:/ospanel/domains/hospital/hms/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$areaStats = [];

if (isset($_POST['generate']) || isset($_POST['export_excel'])) {
    // Получаем всех пациентов с диагнозами
    $query = mysqli_query($con, "SELECT PatientAdd, PatientMedhis FROM tblpatient WHERE PatientMedhis IS NOT NULL");

while ($row = mysqli_fetch_assoc($query)) {
    $address = trim($row['PatientAdd']);


$knownAreas = [
    'Вахитовский', 'Приволжский', 'Советский', 'Московский',
    'Кировский', 'Авиастроительный', 'Ново-Савиновский'
];

$found = false;
foreach ($knownAreas as $areaName) {
    if (stripos($address, $areaName) !== false) {
        $area = $areaName . ' район';
        $found = true;
        break;
    }
}
if (!$found) {
    $area = 'Неизвестный район';
}


    $diagnoses = explode(';', $row['PatientMedhis']);

    foreach ($diagnoses as $diag) {
        $cleanDiag = trim($diag);
        if (!$cleanDiag) continue;

        if (!isset($areaStats[$area][$cleanDiag])) {
            $areaStats[$area][$cleanDiag] = 0;
        }
        $areaStats[$area][$cleanDiag]++;
    }
}


    // Экспорт в Excel
if (isset($_POST['export_excel']) && !empty($areaStats)) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Статистика по районам');

    // Получаем список всех уникальных заболеваний
    $allDiseases = [];
    foreach ($areaStats as $area => $diagnoses) {
        foreach ($diagnoses as $disease => $count) {
            $allDiseases[$disease] = true;
        }
    }
    $allDiseases = array_keys($allDiseases);

    // Заголовки: A1 — "Район", B1 и далее — заболевания
    foreach ($allDiseases as $index => $disease) {
        $sheet->setCellValueByColumnAndRow($index + 2, 1, $disease);
    }

    // Заполнение таблицы
    $rowNum = 2;
    foreach ($areaStats as $area => $diagnoses) {
        $sheet->setCellValue("A{$rowNum}", $area);
        foreach ($allDiseases as $colIndex => $disease) {
            $value = isset($diagnoses[$disease]) ? $diagnoses[$disease] : 0;
            $sheet->setCellValueByColumnAndRow($colIndex + 2, $rowNum, $value);
        }
        $rowNum++;
    }

    // Отправляем файл
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report_disease_by_area.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}



}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <?php include('include/header.php'); ?>
    <title>Админ | Статистика по диспансеризации</title>
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
    <?php include('include/sidebar.php'); ?>
    <div class="main-content" >
					<div class="wrap-content container" id="container">
						<!-- start: PAGE TITLE -->
						<section id="page-title">
							<div class="row">
								<div class="col-sm-8">
									<h1 class="mainTitle">Админ | Создать отчёт</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>Админ</span>
									</li>
									<li class="active">
										<span>Создать отчёт</span>
									</li>
								</ol>
							</div>
						</section>
    <div class="app-content" align="left">
        
        <div class="main-content">
            <div class="wrap-content container" id="container">

    <h2 class="mb-4">Статистика диспансеризации по районам Казани</h2>
    <form method="post" class="mb-3">
        <button type="submit" name="generate" class="btn btn-primary">Показать статистику</button>
        <?php if (!empty($areaStats)) { ?>
            <button type="submit" name="export_excel" class="btn btn-success">Экспорт в Excel</button>
        <?php } ?>
    </form>


    <?php if (!empty($areaStats)) { ?>
        <?php foreach ($areaStats as $area => $diagnoses): ?>
            <h4><?php echo htmlspecialchars($area); ?></h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Заболевание</th>
                        <th>Количество случаев</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diagnoses as $diag => $count): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($diag); ?></td>
                            <td><?php echo $count; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php } ?>
                </div> <!-- wrap-content -->
        </div> <!-- main-content -->
   <?php include('include/footer.php'); ?> </div> <!-- app-content -->
</div> <!-- app -->

    
</div>


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
</body>
</html>

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
        $appointmentId = $_POST['appointment_id'];
        mysqli_query($con,"update appointment set isCompleted='1' where id ='$appointmentId' and doctorId='".$_SESSION['id']."'");
        $_SESSION['msg']="Appointment marked as completed!";
        header("Location: appointment-history.php");
        exit();
    }
    if (isset($_POST['submit'])) {
    $patientId = $_POST['patient'];
    $dispDate = $_POST['dispDate'];

    $res = mysqli_query($con, "INSERT INTO dispensarization (patientId, dispDate) VALUES ('$patientId', '$dispDate')");
    if (!$res) {
        echo "Ошибка при вставке в dispensarization: " . mysqli_error($con);
        exit;
    }

    // Получаем список всех врачей
    $doctors = mysqli_query($con, "SELECT ID FROM doctors");
    while ($doc = mysqli_fetch_assoc($doctors)) {
        $docId = $doc['ID'];
        mysqli_query($con, "INSERT INTO appointment (userId, doctorId, appointmentDate) VALUES ('$patientId', '$docId', '$dispDate')");
    }

    echo "<script>alert('Диспансеризация успешно создана');</script>";
}
    if (isset($_POST['init_disp'])) {
    $patientId = $_POST['disp_patient'];
    $dispDate = $_POST['disp_date'];
    $registrarId = $_SESSION['id'];

    // Получаем пол пациента
    $genderQuery = mysqli_query($con, "SELECT PatientGender FROM tblpatient WHERE ID = '$patientId'");
    $genderRow = mysqli_fetch_assoc($genderQuery);
    $gender = strtolower(trim($genderRow['PatientGender'])); // male / female

    // Вставка в таблицу диспансеризации
    $res = mysqli_query($con, "INSERT INTO dispensarization (patientId, dispDate) VALUES ('$patientId', '$dispDate')");
    if (!$res) {
        $_SESSION['msg'] = "Ошибка при вставке в диспансеризацию: " . mysqli_error($con);
        exit;
    }

    // Получаем список всех врачей и специализаций
    $doctors = mysqli_query($con, "SELECT d.id, d.doctorSpecializationId, ds.doctorSpecialization
        FROM doctors d
        JOIN doctorspecilization ds ON ds.doctorSpecializationId = d.doctorSpecializationId
    ");

    $success = true;
    while ($doc = mysqli_fetch_assoc($doctors)) {
        $doctorId = $doc['id'];
        $specId = $doc['doctorSpecializationId'];
        $specName = strtolower($doc['doctorSpecialization']);

        // Исключаем по полу
        if (
            ($gender === 'female' && $specId === '9') ||
            ($gender === 'male' && $specId === '2')
        ) {
            continue;
        }

        $result = mysqli_query($con, "
            INSERT INTO appointment(userId, doctorId, doctorSpecializationId, appointmentDate, appointmentTime, isCompleted)
            VALUES('$patientId', '$doctorId', '$specId', '$dispDate', NULL, 0)
        ");

        if (!$result) {
            $success = false;
            $_SESSION['msg'] = "Ошибка при создании приёмов: " . mysqli_error($con);
            break;
        }
    }

    if ($success) {
        $_SESSION['msg'] = "Диспансеризация успешно инициирована!";
    }
}


}

	

?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>История приёмов</title>
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
                                    <h1 class="mainTitle">История приёмов</h1>
                                </div>
                                <ol class="breadcrumb">
                                    <li>
                                        <span></span>
                                    </li>
                                    <li class="active">
                                        <span>История приёмов</span>
                                    </li>
                                </ol>
                            </div>
                        </section>

                        
<hr>
<h4>Инициировать диспансеризацию</h4>
<form method="post">
    <div class="form-group">
        <label for="disp_patient">Выберите пациента</label>
        <select name="disp_patient" class="form-control" required>
            <option value="">-- Выберите пациента --</option>
            <?php
            $patients = mysqli_query($con, "SELECT ID, PatientName FROM tblpatient");
            while ($p = mysqli_fetch_array($patients)) {
                echo "<option value='{$p['ID']}'>" . htmlspecialchars($p['PatientName']) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="disp_date">Дата диспансеризации</label>
        <input type="date" name="disp_date" class="form-control" required>
    </div>
    <button type="submit" name="init_disp" class="btn btn-success">Назначить диспансеризацию</button>
</form>

</div>
<!-- end: CREATE NEW APPOINTMENT FORM -->
<h4>Журнал диспансеризаций</h4>
<?php
session_start();
include('includes/config.php'); // Подключение к БД
error_reporting(0);

// Получаем список диспансеризаций
$result = mysqli_query($con, "SELECT d.id as disp_id, d.dispDate, p.PatientName, d.patientId 
    FROM dispensarization d 
    JOIN tblpatient p ON p.ID = d.patientId
    ORDER BY d.dispDate DESC
");

$dispensarizations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $dispId = $row['disp_id'];
    $patientId = $row['patientId'];

    // Кол-во назначенных врачей
    $total = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM appointment 
    WHERE appointmentDate = '{$row['dispDate']}' AND userId = '$patientId'
"))['total'];

$filled = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as filled FROM appointment 
    WHERE appointmentDate = '{$row['dispDate']}' 
    AND userId = '$patientId' AND isCompleted = 1
"))['filled'];

$status = ($total > 0 && $total == $filled) ? 'Завершена' : 'В процессе';

    $dispensarizations[] = [
        'date' => $row['dispDate'],
        'name' => $row['PatientName'],
        'status' => $status
    ];
}
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Пациент</th>
            <th>Дата</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dispensarizations as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['name']) ?></td>
            <td><?= date("d.m.Y", strtotime($d['date'])) ?></td>
            <td>
                <?php if ($d['status'] === 'Завершена'): ?>
                    <span class="badge bg-success">Завершена</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">В процессе</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
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
        <script>
function fetchSpecialization(selectElement) {
    var option = selectElement.options[selectElement.selectedIndex];
    var specName = option.getAttribute('data-spec-name');
    var specId = option.getAttribute('data-spec-id');

    document.getElementById('specialization_text').value = specName || '';
    document.getElementById('specialization_id').value = specId || '';
}
</script>
    </body>
</html>
<?php  ?>
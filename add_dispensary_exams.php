<?php
// add_dispensary_exams.php - запустить один раз
require_once 'hms/include/config.php';

// Получаем все активные диспансеризации
$disp_query = "SELECT id, patientId FROM dispensarization WHERE status = 'in_progress'";
$disp_result = mysqli_query($con, $disp_query);

while($disp = mysqli_fetch_assoc($disp_result)) {
    $disp_id = $disp['id'];
    $patient_id = $disp['patientId'];
    
    // Получаем возраст пациента
    $age_query = "SELECT TIMESTAMPDIFF(YEAR, PatientDOB, CURDATE()) as age FROM tblpatient WHERE ID = '$patient_id'";
    $age_result = mysqli_query($con, $age_query);
    $age = mysqli_fetch_assoc($age_result)['age'];
    
    // Определяем список обследований по возрасту
    $exams = [];
    if($age < 40) {
        $exams = ['Терапевт', 'Общий анализ крови', 'Общий анализ мочи', 'Флюорография', 'ЭКГ'];
    } else {
        $exams = ['Терапевт', 'Общий анализ крови', 'Общий анализ мочи', 'Флюорография', 'ЭКГ', 'Офтальмолог', 'Кардиолог'];
    }
    
    // Добавляем обследования
    foreach($exams as $exam) {
        $check = mysqli_query($con, "SELECT * FROM dispensary_exams WHERE dispensary_id = '$disp_id' AND exam_name = '$exam'");
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($con, "INSERT INTO dispensary_exams (dispensary_id, exam_name, status) VALUES ('$disp_id', '$exam', 'pending')");
        }
    }
    
    echo "Добавлены обследования для диспансеризации ID $disp_id<br>";
}
echo "Готово!";
?>
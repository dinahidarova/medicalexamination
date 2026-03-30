<?php
require_once 'hms/include/config.php';

$patient_id = 11; // ID пациентки

echo "<h2>Проверка итогового заключения</h2>";

$query = "SELECT * FROM tblmedicalhistory 
    WHERE PatientID = '$patient_id' 
    AND FinalConclusion IS NOT NULL 
    AND FinalConclusion != ''
    ORDER BY CreationDate DESC 
    LIMIT 1";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Ошибка: " . mysqli_error($con));
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "<h3 style='color:green'>✅ Найдено итоговое заключение!</h3>";
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    echo "<hr>";
    echo "<h4>Текст заключения:</h4>";
    echo "<div style='background:#f0f0f0; padding:15px;'>";
    echo nl2br(htmlspecialchars($row['FinalConclusion']));
    echo "</div>";
} else {
    echo "<h3 style='color:red'>❌ Нет итогового заключения для пациента ID $patient_id</h3>";
}

// Проверим все записи
echo "<h3>Все записи для пациента:</h3>";
$all = mysqli_query($con, "SELECT id, FinalConclusion, LEFT(FinalConclusion, 50) as preview FROM tblmedicalhistory WHERE PatientID = '$patient_id' ORDER BY CreationDate DESC");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>FinalConclusion</th><th>Preview</th></tr>";
while ($row = mysqli_fetch_assoc($all)) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>" . ($row['FinalConclusion'] ? '✅ ЕСТЬ' : '❌ ПУСТО') . "</td>";
    echo "<td>" . htmlspecialchars($row['preview']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
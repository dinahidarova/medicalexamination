<?php
require_once 'hms/include/config.php';
$patient_id = 11;
$result = mysqli_query($con, "SELECT FinalConclusion FROM tblmedicalhistory WHERE PatientID = $patient_id AND FinalConclusion IS NOT NULL ORDER BY CreationDate DESC LIMIT 1");
$row = mysqli_fetch_assoc($result);
echo "<h2>Заключение:</h2>";
echo "<div style='background:#eee; padding:20px;'>" . nl2br(htmlspecialchars($row['FinalConclusion'])) . "</div>";
?>
<?php
session_start();
require_once 'include/config.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['id'];
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$dispensary_id = isset($_GET['dispensary_id']) ? intval($_GET['dispensary_id']) : 0;

if($exam_id > 0 && $dispensary_id > 0) {
    // Проверяем, что обследование относится к диспансеризации
    $check = mysqli_query($con, "SELECT * FROM dispensary_exams WHERE id = '$exam_id' AND dispensary_id = '$dispensary_id'");
    
    if(mysqli_num_rows($check) > 0) {
        mysqli_query($con, "UPDATE dispensary_exams SET status = 'completed', completed_at = NOW() WHERE id = '$exam_id'");
        
        // Проверяем, все ли обследования выполнены
        $remaining = mysqli_query($con, "SELECT COUNT(*) as left FROM dispensary_exams WHERE dispensary_id = '$dispensary_id' AND status = 'pending'");
        $left = mysqli_fetch_assoc($remaining)['left'];
        
        if($left == 0) {
            mysqli_query($con, "UPDATE dispensarization SET status = 'completed' WHERE id = '$dispensary_id'");
        }
    }
}

header("Location: patient_exams.php?dispensary_id=$dispensary_id");
exit();
?>
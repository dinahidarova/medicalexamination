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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($new_password != $confirm_password) {
        $error = 'Новый пароль и подтверждение не совпадают';
    } elseif(strlen($new_password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $query = "SELECT passwords FROM tblpatient WHERE ID = '$patient_id'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        
        if(password_verify($old_password, $row['passwords'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE tblpatient SET passwords = '$new_hash' WHERE ID = '$patient_id'";
            if(mysqli_query($con, $update)) {
                $success = 'Пароль успешно изменен!';
            } else {
                $error = 'Ошибка при смене пароля';
            }
        } else {
            $error = 'Неверный текущий пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Смена пароля</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div id="app">
        <?php include('include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('include/header.php'); ?>
            <div class="main-content">
                <div class="wrap-content container">
                    <h1 class="mainTitle">Смена пароля</h1>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label>Текущий пароль</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Новый пароль</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Подтверждение пароля</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сменить пароль</button>
                        <a href="dashboard.php" class="btn btn-default">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
        <?php include('include/footer.php'); ?>
    </div>
</body>
</html>
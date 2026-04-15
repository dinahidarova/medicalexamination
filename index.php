<?php
session_start();
require_once 'hms/include/config.php';

// Если пациент уже авторизован
if(isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'patient') {
    header("Location: hms/patient/index.php");
    exit();
}

// Если авторизован другой пользователь (врач, администратор и т.д.)
if(isset($_SESSION['id']) && isset($_SESSION['role'])) {
    switch($_SESSION['role']) {
        case 'admin':
            header("Location: hms/admin/dashboard.php");
            exit();
            break;
        case 'doctor':
            header("Location: hms/doctor/dashboard.php");
            exit();
            break;
        case 'reception':
            header("Location: hms/reception/dashboard.php");
            exit();
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диспансеризация - Вход для пациентов</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #4ba29b 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #4b91a2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.3s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .alert-error {
            background: #fee;
            border-left: 4px solid #f44336;
            color: #c33;
        }
        
        .alert-success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }
        
        .alert-icon {
            font-size: 18px;
        }
        
        .login-footer {
            margin-top: 25px;
            text-align: center;
        }
        
        .login-footer p {
            margin: 10px 0;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .info-note {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Система диспансеризации</h1>
                <p>Вход для пациентов</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span class="alert-text">
                        <?php 
                        switch($_GET['error']) {
                            case 'invalid':
                                echo 'Неверный email, телефон, полис или пароль';
                                break;
                            case 'blocked':
                                echo 'Ваш аккаунт заблокирован. Обратитесь в регистратуру';
                                break;
                            default:
                                echo 'Ошибка авторизации. Попробуйте снова';
                        }
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <span class="alert-text">Регистрация успешна! Войдите в систему</span>
                </div>
            <?php endif; ?>
            
            <form action="hms/patient/auth.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="login">
                        📧 Email / 📱 Телефон / 🏥 Полис
                    </label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           placeholder="Введите email, телефон или номер полиса" 
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        🔒 Пароль
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Введите пароль" 
                           required>
                </div>
                
                <button type="submit" class="btn-login">Войти в личный кабинет</button>
            </form>
            
            <div class="login-footer">
                <p>Нет учетной записи? <a href="hms/patient/register.php">Зарегистрироваться</a></p>
            </div>
            
      
        </div> 
    </div>
</body>
</html>
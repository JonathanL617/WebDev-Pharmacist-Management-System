<?php
session_start();
require_once '../config/config.php';
require_once '../config/conn.php';
require_once '../controller/AuthController.php';

$auth = new AuthController($conn);
$error = '';
$message = '';
$showCard = 'login'; // Default to login card

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    try {
        if ($auth->resetPassword($_POST['reset_email'], $_POST['new_password'], $_POST['confirm_password'])) {
            $message = "Password reset successful. Please login with your new password.";
            $showCard = 'success';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $showCard = 'reset';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pharmacy Management System</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/login_page.css">
    <style>
        /* Additional styles to prevent layout shifts */
        #login-error {
            margin-top: 10px;
            margin-bottom: 10px;
            min-height: 0;
            transition: all 0.3s ease;
        }
        
        .login-card form,
        .reset-password-card form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 0;
        }
        
        .checkbox-container label {
            display: flex;
            align-items: center;
            gap: 1px;
            margin: 0;
            cursor: pointer;
        }
        
        .links {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="layout">
        <div class="login-side">
            <!-- Login section -->
            <div class="login-card" id="loginCard" style="display: <?php echo ($showCard === 'login') ? 'block' : 'none'; ?>;">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="logo">
                <h3>Pharmacist Management System</h3>
                <h5>Login</h5>
                
                <?php if ($message && $showCard === 'login'): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                
                <!-- Error message placeholder (hidden by default) -->
                <div id="login-error" style="display:none;"></div>
                
                <form id="login-form" method="POST" action="<?php echo BASE_URL; ?>/app/controller/AuthController.php">
                    <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_COOKIE['user_login']) ? htmlspecialchars($_COOKIE['user_login']) : ''; ?>">
                    
                    <input type="password" name="password" placeholder="Password" required>
                    
                    <div class="checkbox-container">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" <?php echo isset($_COOKIE['user_login']) ? 'checked' : ''; ?>>
                            <span>Remember Me</span>
                        </label>
                    </div>
                    
                    <button type="submit" name="login" class="main">Log In</button>
                </form>
                
                <div class="links">
                    <a href="#" onclick="showResetPassword(); return false;">Forgot password?</a>
                </div>
            </div>

            <!-- Reset password section -->
            <div class="reset-password-card" id="resetPasswordCard" style="display: <?php echo ($showCard === 'reset') ? 'block' : 'none'; ?>;">
                <button class="back-button" onclick="showLogin()">← Back</button>
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="Logo">
                <h2>Reset Password</h2>
                
                <?php if ($error && $showCard === 'reset'): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <input type="email" name="reset_email" placeholder="Enter your email" required>
                    
                    <input type="password" name="new_password" placeholder="New Password (min 8 characters)" required minlength="8">
                    
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required minlength="8">
                    
                    <button type="submit" name="reset_password" class="main">Reset Password</button>
                </form>
            </div>

            <!-- Success section -->
            <div class="success-card" id="successCard" style="display: <?php echo ($showCard === 'success') ? 'block' : 'none'; ?>;">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="Logo">
                <h2>Password Reset Successful!</h2>
                
                <?php if ($message && $showCard === 'success'): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                
                <button class="main" onclick="showLogin()">Return to Login Page</button>
            </div>
        </div>
        <div></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/login.js"></script>
</body>
</html>
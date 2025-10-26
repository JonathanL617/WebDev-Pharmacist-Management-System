<?php
session_start();
require_once '../config/config.php';
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
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/login_page.css">
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
                <form id="login-form" method="POST" onsubmit="handleLogin(event)">
                    <input type="email" name="email" placeholder="Email" required value="<?php if(isset($_COOKIE['user_login'])) echo htmlspecialchars($_COOKIE['user_login']); ?>">
                    <input type="password" name="password" placeholder="Password" required value="<?php if(isset($_COOKIE['password'])) echo htmlspecialchars($_COOKIE['password']); ?>">
                    <div class="checkbox-container">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" <?php if(isset($_COOKIE['user_login'])) echo 'checked'; ?>>
                            Remember Me
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
                <button class="back-button" onclick="showLogin()">Back</button>
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="Logo">
                <h2>Reset Password</h2>
                <?php if ($message && $showCard === 'reset'): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <input type="email" name="reset_email" placeholder="Enter your email" required>
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    <button type="submit" name="reset_password" class="main">Reset Password</button>
                </form>
            </div>

            <!-- Success section -->
            <div class="success-card" id="successCard" style="display: <?php echo ($showCard === 'success') ? 'block' : 'none'; ?>;">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="Logo">
                <h2>Reset Password Successful!</h2>
                <?php if ($message): ?>
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
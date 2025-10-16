<?php
    session_start();

    require_once '../config/conn.php';
    require_once '../controller/AuthController.php';

    $auth = new AuthController($conn);
    $error = '';
    $message = '';
    $showCard = 'login'; //default to show login card

    if(isset($_POST['login'])){
        try {
            if($auth->login($_POST['email'], $_POST['password'])){
                header('Location: dashboard.php');
                exit();
            }
        }
        catch(Exception $e){
            $error = $e->getMessage();
        }
    }

    if(isset($_POST['reset_password'])){
        try {
            $auth->resetPassword($_POST['reset_email'], $_POST['new_password'], $_POST['confrim_password']);
            $message = "Password reset successful. Please login with new password";
            $showCard = 'success';
        }
        catch(Exception $e){
            $error = $e->getMessage();
            $showCard = 'reset';
        }
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/login_page.css">
    </head>
    <body>
        <div class="layout">
            <div class="login-side">
                <!-- login section -->
                <div class="login-card" id="loginCard" style="display: <?php echo ($showCard === 'login') ? 'block' : 'none'; ?>;">
                    <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="logo">
                    <h3>Pharmacist Management System</h3>
                    <h5>Login</h5>
                    <?php if ($message && $showCard === 'login'): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($message && $showCard === 'login'): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <form action="<?php echo BASE_URL; ?>/login_page.php" method="POST">
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" name="login" class="main">Log In</button>
                    </form>

                    <div class="links">
                        <a href="#" onclick="showResetPassword(); return false;">Forgot password?</a>
                    </div>
                </div>

                <!-- reset password section -->
                <div class="reset-password-card" id="resetPasswordCard" style="display: <?php echo ($showCard === 'reset') ? 'block' : 'none'; ?>">
                    <button class="back-button" onclick="showLogin()">< Back</button>
                    <br>
                    <br>
                    <img src="../../assets/img/logo.jpg" alt="Logo">
                    
                    <h2>Reset Password</h2>
                    <?php if($error && $showCard === 'reset'): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="<?php echo BASE_URL; ?>/login_page.php" method="POST">
                        <input type="email" name="reset_email" placeholder="Enter your email" required>
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                        <button type="submit" name="reset_password" class="main">Reset Password</button>
                    </form>
                </div>

                <!-- success section -->
                <div class="success-card" id="successCard" style="display: <?php echo ($showCard === 'success') ? 'block' : 'none'; ?>;">
                    <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="Logo">
                    <h2>Reset Password Successful!</h2>
                    <?php if($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <button class="main" onclick="showLogin()">Return to Login Page</button>
                </div>
            </div>
            <div></div>
        </div>

        <!-- script -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="<?php echo BASE_URL; ?>/assets/js/login.js"></script>
    </body>
</html>
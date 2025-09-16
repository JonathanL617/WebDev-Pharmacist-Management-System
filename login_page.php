<?php
    include '../include/conn.php';
    include '../include/login_function.php';
?>

<!DOCTYPE html>

<html>
    <head>
        <style>

        </style>
    </head>
    <body>
        <div class="layout">
            <!-- left side -->
            <div class="login-side">
                <!-- login card -->
                <div class="login-card">
                    <h3>Pharmacis Management System</h3>
                    <h5>Login</h5>
                    <form>
                        <label>Username</label>
                        <input>Username/ID</input>
                        
                        <label>Password</label>
                        <input>Password</input>

                        <button>Login</button>
                    </form>

                    <!-- forget password -->
                    <div>
                        <a href="#">Forget Password?</a>
                    </div>
                </div>
            </div>
            <!-- right side -->
            <div class="background">

            </div>
        </div>
    </body>
</html>
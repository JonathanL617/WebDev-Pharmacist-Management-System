<?php
    include '../include/conn.php';
    include '../include/login_function.php';
?>

<!DOCTYPE html>

<html>
    <head>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
        <link rel="stylesheet" href="css/login_page.css">
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
                </div>
            </div>
            <!-- right side -->
            <div class="background">

            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>
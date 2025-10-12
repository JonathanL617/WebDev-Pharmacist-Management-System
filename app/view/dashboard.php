<?php 
    session_start();
    require_once '../config/config.php';

    /*
    if(!isset($_SESSION['user_id'])){
        header('Location: login_page.php');
        exit();
    }
    */
    $userRole = 'pharmacist';

    $defaultPages = [
        'superadmin' => 'manage_accounts',
        'admin' => 'manage_users',
        'pharmacist' => 'stock_management',
        'doctor' => 'patient_records'
    ];

    $validPages = [
        'superadmin' => ['manage_accounts', 'admin_requests'],
        'admin' => ['manage_users', 'register_patients', 'account_requests'],
        'pharmacist' => ['stock_management', 'prescription_requests', 'prescription_queue', 'dispense_history'],
        'doctor' => ['patient_records', 'prescriptions', 'resupply_requests', 'history']
    ];

    $page = isset($_GET['page']) ? $_GET['page'] : $defaultPages[$userRole];

    // Validate page and redirect if invalid
    if (!in_array($page, $validPages[$userRole])) {
        $page = $defaultPages[$userRole];
        header("Location: dashboard.php?page=" . $page);
        exit();
    }

    $contentFile = __DIR__ . "/pages/{$userRole}/{$page}.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

        <!-- css -->
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
    </head>
    <body>
        <!-- navigation bar -->
        <nav class="nav-container">
            <div class="tab-container">
                <!-- switch content of the tabs based on user role -->
                <?php switch($userRole){
                    case 'superadmin': ?>
                        <a class="tab" data-page="manage_accounts" onclick="openTab('manage_accounts', this)">Manage Accounts</a>
                        <a class="tab" data-page="admin_requests" onclick="openTab('admin_requests', this)">Requests</a>
                    <?php break;

                    case 'admin': ?>
                        <a class="tab" data-page="manage_users" onclick="openTab('manage_users', this)">Manage Users</a>
                        <a class="tab" data-page="register_patients" onclick="openTab('register_patients', this)">Register Patients</a>
                        <a class="tab" data-page="account_requests" onclick="openTab('account_requests', this)">Account Requests</a>
                    <?php break;

                    case 'pharmacist': ?>
                        <a class="tab" data-page="stock_management" onclick="openTab('stock_management', this)">Medicine Stock</a>
                        <a class="tab" data-page="prescription_requests" onclick="openTab('prescription_requests', this)">Prescription Requests</a>
                        <a class="tab" data-page="prescription_queue" onclick="openTab('prescription_queue', this)">Prescription Queue</a>
                        <a class="tab" data-page="dispense_history" onclick="openTab('dispense_history', this)">Dispense History</a>
                    <?php break;

                    case 'doctor': ?>
                        <a class="tab" data-page="patient_records" onclick="openTab('patient_records', this)">Patient Records</a>
                        <a class="tab" data-page="prescriptions" onclick="openTab('prescriptions', this)">Prescriptions</a>
                        <a class="tab" data-page="resupply_requests" onclick="openTab('resupply_requests', this)">Resupply Requests</a>
                        <a class="tab" data-page="history" onclick="openTab('history', this)">History</a>
                    <?php break;
                } ?>
                
            </div>
            <div class="profile-container">
                <i class="bi bi-person-circle me-3 fs-2"></i>
                <span>Username</span>
            </div>
        </nav>

        <!-- content containers -->
        <div class="content-container">
            <?php
                if(file_exists($contentFile)){
                    include $contentFile;
                }
                else {
                    echo "<div class='alert alert-warning'>Page not found: {$contentFile}</div>";
                }
            ?>
        </div>
        <?php
            echo "<pre>";
            echo "Current Role: " . $userRole . "\n";
            echo "Page: " . $page . "\n";
            echo "Looking for file: " . $contentFile . "\n";
            echo "File exists: " . (file_exists($contentFile) ? 'Yes' : 'No') . "\n";
            echo "</pre>";
        ?>

        <!-- scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../assets/js/main.js"></script>
        <script src="<?php echo BASE_URL; ?>/assets/js/adminManagement.js"></script>
    </body>
</html>
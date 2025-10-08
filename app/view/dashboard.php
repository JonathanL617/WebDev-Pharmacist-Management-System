<?php 
    $_SESSION['username'];
    $_SESSION['role'];

    $userRole = 'superadmin';
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

        <!-- css -->
        <link rel="stylesheet" href="assets/css/dashboard.css">
    </head>
    <body>
        <!-- navigation bar -->
        <nav class="nav-container">
            <div class="tab-container">
                <!-- switch content of the tabs based on user role -->
                <?php switch($userRole){
                    case 'superadmin': ?>
                        <a class="tab" onclick="openTab('manage-accounts', this)">Manage Accounts</a>
                        <a class="tab" onclick="openTab('admin-requests', this)">Requests</a>
                    <?php break;

                    case 'admin': ?>
                        <a class="tab" onclick="openTab('manage-users', this)">Manage Users</a>
                        <a class="tab" onclick="openTab('register-patients', this)">Register Patients</a>
                        <a class="tab" onclick="openTab('account-requests', this)">Account Requests</a>
                    <?php break;

                    case 'pharmacist': ?>
                        <a class="tab" onclick="openTab('stock-management', this)">Medicine Stock</a>
                        <a class="tab" onclick="openTab('view-prescription-requests', this)">Prescription Requests</a>
                        <a class="tab" onclick="openTab('manage-request', this)">Prescription Queue</a>
                        <a class="tab" onclick="openTab('view-history', this)">Dispense History</a>
                    <?php break;

                    case 'doctor': ?>
                        <a class="tab" onclick="openTab('view-patient-records', this)">Patient Records</a>
                        <a class="tab" onclick="openTab('view-prescriptions', this)">Prescriptions</a>
                        <a class="tab" onclick="openTab('manage-resupply-requests', this)">Resupply Requests</a>
                        <a class="tab" onclick="openTab('view-history', this)">History</a>
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
            
        </div>

        <!-- scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/admin_management.js"></script>
    </body>
</html>
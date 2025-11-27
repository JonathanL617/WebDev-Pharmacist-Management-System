
<?php 
    session_start();
    require_once '../config/config.php';
    
    
    if(!isset($_SESSION['user_id'])){
        header('Location:' . BASE_URL . '/app/view/login_page.php');
        exit();
    }
    
   $userRole = $_SESSION['user_role']; // Default to admin if not set for testing
   $staff_id = $_SESSION['staff_id'] ?? null;

    //$userRole = $_SESSION['user_role'] ?? 'pharmacist'; // Default to pharmacist if not set for testing
    //$staff_id = $_SESSION['staff_id'] ?? 'P001'; // Use session or fallback

    //$userRole = $_SESSION['user_role'] ?? 'doctor'; // Default to doctor if not set for testing
    //$staff_id = $_SESSION['staff_id'] ?? 'D001'; // Use session or fallback
    

    $defaultPages = [
        'superadmin' => 'manage_accounts',
        'admin' => 'manage_users',
        'pharmacist' => 'stock_management',
        'doctor' => 'patient_records'
    ];

    $validPages = [
        'superadmin' => ['manage_accounts', 'admin_requests'],
        'admin' => ['manage_users', 'register_patients', 'account_requests'],
        'pharmacist' => ['stock_management',  'prescription_queue', 'API'],
        'doctor' => ['patient_records', 'prescriptions', 'resupply_requests', 'API']
    ];

    $page = isset($_GET['page']) ? $_GET['page'] : ($defaultPages[$userRole] ?? 'stock_management');

    // Validate page and redirect if invalid
    if (!isset($validPages[$userRole]) || !in_array($page, $validPages[$userRole])) {
        $page = $defaultPages[$userRole] ?? 'stock_management';
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
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/manage_user.css">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/manage_accounts.css">
    </head>

        <!-- IMPORTANT-->
        <body <?php if (isset($staff_id)) echo 'data-staff-id="' . htmlspecialchars($staff_id) . '"'; ?>>
        <!-- IMPORTANT: This data attribute is used by JavaScript to get staff ID -->

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
                    
                        <a class="tab" data-page="prescription_queue" onclick="openTab('prescription_queue', this)">Prescription Queue</a>
                        <a class="tab" data-page="API" onclick="openTab('API', this)">Drug Search</a>
                    <?php break;

                    case 'doctor': ?>
                        <a class="tab" data-page="patient_records" onclick="openTab('patient_records', this)">Patient Records</a>
                        <a class="tab" data-page="prescriptions" onclick="openTab('prescriptions', this)">Prescriptions</a>
                        <a class="tab" data-page="API" onclick="openTab('API', this)">Drug Search</a>
                        
                    <?php break;
                } ?>
                
            </div>
            <div class="profile-container dropdown">
                <a class="dropdown-toggle d-flex align-items-center text-decoration-none text-dark" 
                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-3 fs-2"></i>
                    <span><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'User'); ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </li>
                </ul>
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
        /*
            echo "<pre>";
            echo "Current Role: " . $userRole . "\n";
            echo "Page: " . $page . "\n";
            echo "Looking for file: " . $contentFile . "\n";
            echo "File exists: " . (file_exists($contentFile) ? 'Yes' : 'No') . "\n";
            echo "</pre>";
        */
        ?>

        <!-- Logout Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to log out?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="<?php echo BASE_URL; ?>/app/controller/AuthController.php">
                            <input type="hidden" name="logout" value="1">
                            <button type="submit" class="btn btn-danger">Yes, Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <!-- Global Variables -->
    <script>
        const loggedInUserId = "<?php 
            // Use role-specific ID for registered_by fields
            echo match($_SESSION['user_role'] ?? '') {
                'superadmin' => $_SESSION['super_admin_id'] ?? '',
                'admin' => $_SESSION['admin_id'] ?? '',
                'doctor', 'pharmacist' => $_SESSION['staff_id'] ?? '',
                default => $_SESSION['user_id'] ?? ''
            };
        ?>";
        const loggedInUserRole = "<?php echo $_SESSION['user_role'] ?? ''; ?>";
    </script>

    <!-- Page Specific Scripts -->
    <?php
        $pageScripts = [
            'manage_accounts' => ['manageAccount.js'],
            'manage_users' => ['admin_manage_user.js'],
            'register_patients' => ['admin_register_patients.js'],
            'admin_requests' => ['superadmin_requests.js'],
            'account_requests' => ['admin_account_requests.js'],
            'stock_management' => ['pharmacist_stock_management.js'],
            'prescription_queue' => ['prescription_queue.js'],
            'patient_records' => ['doctor_patient_records.js'],
            'prescriptions' => ['doctor_prescriptions.js'],
            'API' => ['API.js'],
            // Add other pages as needed
        ];

        if (isset($pageScripts[$page])) {
            foreach ($pageScripts[$page] as $script) {
                echo '<script src="' . BASE_URL . '/assets/js/' . $script . '"></script>';
            }
        }
    ?>
    </body>
</html>
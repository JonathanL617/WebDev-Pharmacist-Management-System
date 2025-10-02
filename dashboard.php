<!DOCTYPE html>
<html>
    <head>
        <!-- css -->
        <link rel="stylesheet" href="css/dashboard.css">
    </head>
    <body>
        <!-- navigation bar -->
        <nav class="nav-conatiner">
            <div class="tab-container">
                <!-- switch content of the tabs based on user role -->
                <?php switch($userRole){
                    case 'superadmin': ?>
                        <button class="tab">Manage Accounts</button>
                        <button class="tab">Requests</button>
                    <?php break;

                    case 'admin': ?>
                        <button class="tab">Manage Users</button>
                        <button class="tab">Regiter Patients</button>
                        <button class="tab">Account Requests</button>
                    <?php break;

                    case 'pharmacist': ?>
                        <button class="tab">Medicine Stock</button>
                        <button class="tab">Precription Requests</button>
                        <button class="tab">Precsription Queue</button>
                        <button class="tab">Dispense History</button>
                    <?php break;

                    case 'doctor': ?>
                        <button class="tab">Patient Records</button>
                        <button class="tab">Prescriptions</button>
                        <button class="tab">Resupply Requests</button>
                        <button class="tab">History</button>
                    <?php break;
                } ?>
                
            </div>
            <div class="profile">

            </div>
        </nav>
        <!-- four role-based view -->
        <!-- main content -->
        <!-- content loader -->
        
        <!-- superadmin -->
        <!-- 
            create & manage acc
            delete acc
            approve/reject request
        -->
        
        <!-- admin -->
        <!--
            create & manage acc
            manage request
            manange users
        -->
        
        <!-- pharmacist -->
        <!--
            stock management
            create prescription request
            view request
            view queue
            view history
        -->
        
        <!-- doctor -->
        <!--
            view patients
            view medicine stock
            notification
            create prescription request
            view recent prescription
            manage resupply request for patient (approve/reject)
        -->
    </body>
    <!-- js -->
</html>
<!-- admin -->
<div class="tab-content" id="account-requests">
</div>
<!--
    create & manage acc
    manage request
    manange users
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Account Request Form</title>
    <!-- Bootstrap 5.0.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <div class="container my-5">
        <div class="tab-content" id="staff-request-form">
            <h1 class="mb-4">Staff Account Request Form</h1>
            <form id="staffRequestForm" action="/submit-staff-request" method="POST">
                <div class="mb-3">
                    <label for="staff_id" class="form-label">Staff ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="staff_id" name="staff_id" readonly placeholder="Auto-generated (e.g., D001 or P001)" required>
                </div>
                <div class="mb-3">
                    <label for="staff_name" class="form-label">Staff Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="staff_name" name="staff_name" maxlength="100" placeholder="Enter Full Name" required>
                </div>
                <div class="mb-3">
                    <label for="staff_dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="staff_dob" name="staff_dob" required>
                </div>
                <div class="mb-3">
                    <label for="staff_specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="staff_specialization" name="staff_specialization" maxlength="100" placeholder="Enter Specialization" required>
                </div>
                <div class="mb-3">
                    <label for="staff_role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="staff_role" name="staff_role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="doctor">Doctor</option>
                        <option value="pharmacist">Pharmacist</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="staff_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="staff_phone" name="staff_phone" maxlength="15" placeholder="Enter Phone Number" pattern="[0-9]{10,15}" required>
                </div>
                <div class="mb-3">
                    <label for="staff_email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="staff_email" name="staff_email" maxlength="100" placeholder="Enter Email Address" required>
                </div>
                <div class="mb-3">
                    <label for="registered_by" class="form-label">Registered By (Admin ID) <span class="text-danger">*</span></label>
                    <select class="form-select" id="registered_by" name="registered_by" required>
                        <option value="" disabled selected>Loading Admin IDs...</option>
                    </select>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                    <a href="/dashboard.php?page=account_requests" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Bootstrap 5.0.2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/adminform.js"></script>
</body>
</html>
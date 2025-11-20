<div class="tab-content" id="manage-users">
</div>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/manage_users.css">
</head>
<body>
    <div class="container my-5">
        <div class="tab-content" id="manage-users">
            <h1 class="mb-4">Admin - Manage Users</h1>

            <!-- Tabs for Staff and Patients -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#manage-staff">Manage Staff</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#manage-patients">Manage Patients</a>
                </li>
            </ul>

            <!-- Staff Tab -->
            <div class="tab-pane fade show active" id="manage-staff">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Staff List</h5>
                        <div>
                            <input type="text" class="form-control" id="staffSearch" placeholder="Search staff by name or ID">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Specialization</th>
                                        <th>Role</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Registered By</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="staffTable">
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Loading staff...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patients Tab -->
            <div class="tab-pane fade" id="manage-patients">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Patient List</h5>
                        <div>
                            <input type="text" class="form-control" id="patientSearch" placeholder="Search patients by name or ID">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Patient ID</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Phone</th>
                                        <th>Gender</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Registered By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="patientTable">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Loading patients...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Edit Modal -->
            <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editStaffForm">
                                <div class="mb-3">
                                    <label for="edit_staff_id" class="form-label">Staff ID</label>
                                    <input type="text" class="form-control" id="edit_staff_id" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="edit_staff_name" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="edit_staff_dob" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_specialization" class="form-label">Specialization</label>
                                    <input type="text" class="form-control" id="edit_staff_specialization" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_role" class="form-label">Role</label>
                                    <select class="form-select" id="edit_staff_role" required>
                                        <option value="" disabled selected>Select Role</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="pharmacist">Pharmacist</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="edit_staff_phone" maxlength="15" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_staff_email" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_registered_by" class="form-label">Registered By</label>
                                    <input type="text" class="form-control" id="edit_staff_registered_by" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_staff_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_staff_status" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="blocked">Blocked</option>
                                    </select>
                                </div>
                                <div id="staffFormErrors" class="text-danger mb-3" style="display: none;"></div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Edit Modal -->
            <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPatientModalLabel">Edit Patient</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editPatientForm">
                                <div class="mb-3">
                                    <label for="edit_patient_id" class="form-label">Patient ID</label>
                                    <input type="text" class="form-control" id="edit_patient_id" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="edit_patient_name" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="edit_patient_dob" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="edit_patient_phone" maxlength="15" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_gender" class="form-label">Gender</label>
                                    <select class="form-select" id="edit_patient_gender" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_patient_email" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="edit_patient_address" rows="3" maxlength="255" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_patient_registered_by" class="form-label">Registered By</label>
                                    <input type="text" class="form-control" id="edit_patient_registered_by" readonly>
                                </div>
                                <div id="patientFormErrors" class="text-danger mb-3" style="display: none;"></div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/assets/js/manage_users.js"></script>
</body>
</html>
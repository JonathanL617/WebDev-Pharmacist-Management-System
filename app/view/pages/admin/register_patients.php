<div class="tab-content" id="register-patients">
</div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/tables.css">
    <link rel="stylesheet" href="/assets/css/form.css">
</head>
<body>
    <div class="container my-5">
        <div class="tab-content" id="patient-management">
            <h1 class="mb-4">Patient Management</h1>

            <!-- Existing Patients Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Existing Patients</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerPatientModal">Register New Patient</button>
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
                                </tr>
                            </thead>
                            <tbody id="patientsTable">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Loading patients...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for Registering New Patient -->
            <div class="modal fade" id="registerPatientModal" tabindex="-1" aria-labelledby="registerPatientModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="registerPatientModalLabel">Register New Patient</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="patientRegisterForm">
                                <div class="mb-3">
                                    <label for="patient_id" class="form-label">Patient ID</label>
                                    <input type="text" class="form-control" id="patient_id" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="patient_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="patient_date_of_birth" required>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="patient_phone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_gender" class="form-label">Gender</label>
                                    <select class="form-select" id="patient_gender" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="patient_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="patient_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="patient_address" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="registered_by" class="form-label">Registered By</label>
                                    <input type="text" class="form-control" id="registered_by" value="SA001" readonly>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Register</button>
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
    <script src="/assets/js/admin_register_patients.js"></script>
</body>
</html>
 
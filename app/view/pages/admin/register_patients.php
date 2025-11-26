<!-- admin register patients -->
<div class="tab-content" id="register-patients">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <p class="stat-label">Total Patients</p>
            <div class="stat-value" id="totalPatients">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
            <p class="stat-label">Registered Today</p>
            <div class="stat-value" id="registeredToday">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-week"></i></div>
            <p class="stat-label">This Week</p>
            <div class="stat-value" id="registeredThisWeek">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <p class="stat-label">Registered By You</p>
            <div class="stat-value" id="registeredByCurrent">0</div>
        </div>
    </div>
    <h2>Patient Management</h2>

    <!-- Existing Patients Table -->
    <div class="row mb-4">
        <div class="col-lg-9 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search patients">
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 text-lg-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerPatientModal">
                <i class="bi bi-plus-circle"></i> Register New Patient
            </button>
        </div>
    </div>

    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
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
                            <input type="text" class="form-control" id="registered_by" value="<?php echo $_SESSION['admin_id'] ?? ''; ?>" readonly>
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
<!-- admin manage users -->
<div class="tab-content" id="manage-users">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <p class="stat-label">Total Staff</p>
            <div class="stat-value" id="totalStaff">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <p class="stat-label">Active Staff</p>
            <div class="stat-value" id="activeStaff">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <p class="stat-label">Total Patients</p>
            <div class="stat-value" id="totalPatients">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
            <p class="stat-label">Registered By You</p>
            <div class="stat-value" id="registeredByCurrent">0</div>
        </div>
    </div>
    <h2>User Management</h2>

    <!-- Tabs for Staff and Patients -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#manage-staff">Manage Staff</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#manage-patients">Manage Patients</a>
        </li>
    </ul>

    <!-- Staff Tab -->
    <div class="tab-pane fade show active" id="manage-staff">
        <div class="row mb-4">
            <div class="col-lg-9 col-md-6 mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="staffSearch" class="form-control border-start-0 ps-0" placeholder="Search staff">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 text-lg-end">
            </div>
        </div>

        <div class="responsive-table">
            <table class="table table-hover align-middle">
                <thead class="table-light">
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

    <!-- Patients Tab -->
    <div class="tab-pane fade" id="manage-patients">
        <div class="row mb-4">
            <div class="col-lg-9 col-md-6 mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="patientSearch" class="form-control border-start-0 ps-0" placeholder="Search patients">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 text-lg-end">
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
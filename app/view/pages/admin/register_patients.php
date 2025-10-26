<div class="tab-content" id="register-patients">
    <h1>Register Patient</h1>
    <h1>View Existing Patient</h1>

    <!-- create patient record modal -->
    <div class="modal fade" id="createPatientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Create Patient Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createPatientForm">
                        <div class="mb-3">
                            <label class="form-label" for="patient-name">Patient Full Name</label>
                            <input type="text" class="form-control" id="" name="patient-name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="identification-no">I.C</label>
                            <input type="text" class="form-control" id="" name="identification-no" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" class="form-control" id="" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createPatientRecord()">Create</button>
                </div>
            </div>
        </div>
    </div>
</div>
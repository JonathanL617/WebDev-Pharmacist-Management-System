<!-- admin account requests -->
<div class="tab-content" id="account-requests">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <p class="stat-label">Total Requests</p>
            <div class="stat-value" id="totalRequests">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
            <p class="stat-label">Pending</p>
            <div class="stat-value" id="pendingRequests">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <p class="stat-label">Approved</p>
            <div class="stat-value" id="approvedRequests">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <p class="stat-label">Rejected</p>
            <div class="stat-value" id="rejectedRequests">0</div>
        </div>
    </div>
    <h2>Staff Account Request Form</h2>

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
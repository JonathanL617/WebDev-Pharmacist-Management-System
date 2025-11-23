<!-- pharmacist prescription requests -->
<div class="tab-content" id="view-prescription-requests">
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
    <h2>Prescription Requests</h2>

    <!-- Requests Table -->
    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Medicine</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="requestsTable">
                <tr>
                    <td colspan="7" class="text-center text-muted">Loading requests...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
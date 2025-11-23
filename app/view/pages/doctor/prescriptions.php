<!-- doctor prescriptions -->
<div class="tab-content" id="view-prescriptions">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-prescription2"></i></div>
            <p class="stat-label">Total Prescriptions</p>
            <div class="stat-value" id="totalPrescriptions">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
            <p class="stat-label">Pending</p>
            <div class="stat-value" id="pendingPrescriptions">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <p class="stat-label">Approved</p>
            <div class="stat-value" id="approvedPrescriptions">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
            <p class="stat-label">Today</p>
            <div class="stat-value" id="todayPrescriptions">0</div>
        </div>
    </div>
    <h2>Prescriptions</h2>

    <!-- Search and Create -->
    <div class="row mb-4">
        <div class="col-lg-9 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search prescriptions">
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 text-lg-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrescriptionModal">
                <i class="bi bi-plus-circle"></i> Create Prescription
            </button>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Prescription ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="prescriptionsTable">
                <tr>
                    <td colspan="7" class="text-center text-muted">Loading prescriptions...</td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>

    <!-- Create Prescription Modal -->
    <div class="modal fade" id="createPrescriptionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="createPrescriptionForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Prescription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select class="form-select" id="prescriptionPatient" required>
                                <option value="">Loading patients...</option>
                            </select>
                        </div>
                        
                        <label class="form-label">Medicines</label>
                        <div id="medicineList">
                            <!-- Dynamic rows will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMedicineRow()">
                            <i class="bi bi-plus"></i> Add Medicine
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Prescription</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
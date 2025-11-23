<!-- doctor patient records -->
<div class="tab-content" id="patient-records">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <p class="stat-label">Total Patients</p>
            <div class="stat-value" id="totalPatients">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-file-medical-fill"></i></div>
            <p class="stat-label">Recent Consultations</p>
            <div class="stat-value" id="recentConsultations">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-prescription2"></i></div>
            <p class="stat-label">Pending Prescriptions</p>
            <div class="stat-value" id="pendingPrescriptions">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
            <p class="stat-label">Today's Appointments</p>
            <div class="stat-value" id="todayAppointments">0</div>
        </div>
    </div>
    <h2>Patient Orders</h2>

    <!-- Search -->
    <div class="row mb-4">
        <div class="col-lg-8 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" id="searchPatient" placeholder="Search by Patient ID...">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <button class="btn btn-outline-secondary w-100" id="clearSearch">Clear</button>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="responsive-table" id="patientsCard">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>DOB</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="patientsTableBody">
                <tr>
                    <td colspan="6" class="text-center py-4">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Order History Section (hidden by default) -->
    <section id="orderHistorySection" class="mt-4" style="display:none;">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 id="bannerPatientName">-</h4>
                    <p id="bannerPatientId" class="mb-0">Patient ID: -</p>
                </div>
                <button class="btn btn-light" id="backToPatients">Back</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Patient Info</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>DOB:</strong> <span id="detailDob">-</span></p>
                        <p><strong>Phone:</strong> <span id="detailPhone">-</span></p>
                        <p><strong>Gender:</strong> <span id="detailGender">-</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <span id="detailEmail">-</span></p>
                        <p><strong>Address:</strong> <span id="detailAddress">-</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Order History</div>
            <div class="card-body" id="ordersContainer"></div>
        </div>
    </section>
</div>
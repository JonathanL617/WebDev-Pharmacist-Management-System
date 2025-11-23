<!-- superadmin -->
<div class="tab-content" id="manage_accounts">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <p class="stat-label">Total Accounts</p>
            <div class="stat-value" id="totalAdmins">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <p class="stat-label">Active Accounts</p>
            <div class="stat-value" id="activeAdmins">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <p class="stat-label">Inactive Accounts</p>
            <div class="stat-value" id="inActiveAdmins">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <p class="stat-label">Registered By You</p>
            <div class="stat-value" id="registeredByCurrent">0</div>
        </div>
    </div>
    <h2>Admin Accounts</h2>
    <div class="row mb-4">
        
        <!-- search bar -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search">
            </div>
        </div>

        <!-- filters -->
        <div class="col-lg-2 col-md-3 mb-3">
            <select id="filter-role" class="form-select">
                <option value="">All Roles</option>
                <option value="doctor">Doctor</option>
                <option value="pharmacist">Pharmacist</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-3 mb-3">
            <select id="filter-status" class="form-select">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="blocked">Blocked</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-3 mb-3">
            <select id="filter-date" class="form-select">
                <option value="">All</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-6 mb-3 text-lg-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                <i class="bi bi-plus-circle"></i> Add Admin
            </button>
        </div>
    </div>

    <!-- admin account list -->
    <div class="responsive-table">
        <table class="table table-hover align-middle" id="adminsTable">
            <thead class="table-light">
                <tr>
                    <th width="5%"><input type="checkbox" id="selectAll"></th>
                    <th>Acc. ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Date of Birth</th>
                    <th>Registered By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="adminAccountsTable">
                <!-- data will be loaded dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- create admin account modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Create Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createAdminForm">
                    <div class="mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="dob">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="regitered_by">Registered By</label>
                        <input type="text" class="form-control" 
                         value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? '')?>" readonly>
                    </div>
                    <input type="hidden" name="registered_by" value="<?php echo $_SESSION['super_admin_id'] ?? 0; ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createAdminAccount()">Create</button>
            </div>
        </div>
    </div>
</div>

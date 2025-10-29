<div class="tab-content" id="manage-users">
    <h3>Manage Users</h3>
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
            <button id="exportBtn" class="btn btn-outline-secondary me-2">
                <i class="bi bi-upload"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-circle"></i> Add User
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="usersTable">
            <thead class="table-light">
                <tr>
                    <th width="5%"><input type="checkbox" id="selectAll"></th>
                    <th width="15%">Full Name</th>
                    <th width="20%">Email</th>
                    <th width="12%">Username</th>
                    <th width="10%">Status</th>
                    <th width="10%">Role</th>
                    <th width="13%">Joined Date</th>
                    <th width="10%">Last Active</th>
                    <th width="5%">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <!-- Dynamic content -->
            </tbody>
        </table>
    </div>

    <!-- pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <span class="me-2 text-muted">Rows per page</span>
            <select id="rowsPerPage" class="form-select form-select-sm w-auto me-3">
                <option value="10">10</option>
                <option value="25">25</option>
            </select>
            <span class="text-muted">of <strong id="totalRows"><?php //echo $totalRow ?></strong> rows</span>
        </div>
        
        <nav>
            <!-- need to make dynamic paging -->
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled">
                    <a class="page-link" href="#"><i class="bi bi-chevron-double-left"></i></a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li class="page-item"><a class="page-link" href="#">10</a></li>
                <li class="page-item">
                    <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#"><i class="bi bi-chevron-double-right"></i></a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- create user modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Add New User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="addUserForm">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone No.</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="Doctor">Doctor</option>
                        <option value="Pharmacist">Pharmacist</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Acc. Status</label>
                    <input type="text" class="form-control" name="acc-status" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Registered By</label>
                    <input type="text" class="form-control" name="registered-by" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="addUser()">Add User</button>
        </div>
    </div>
</div>
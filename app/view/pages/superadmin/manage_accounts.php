<!-- superadmin -->
<div class="tab-content" id="manage-accounts">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Accounts</h2>
        <button class="btn btn-primary create-button" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            Create Admin Account
            <i class="bi bi-plus-circle-fill fs-4"></i>
        </button>
    </div>

    <!-- admin account list -->
    <div class="responsive-table">
        <table>
            <thead>
                <tr>
                    <th>Acc. ID</th>
                    <th>Username</th>
                    <th>Email</th>
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
                        <input type="text" class="form-control" id="" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createAdminAccount()">Create</button>
            </div>
        </div>
    </div>
</div>
<!-- 
    create & manage acc
    delete acc
    approve/reject request
-->
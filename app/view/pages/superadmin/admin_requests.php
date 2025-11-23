<div class="tab-content" id="admin-requests">
    <!-- Superadmin Requests View -->
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Pending Staff Account Requests</h2>
            <button class="btn btn-primary" onclick="loadRequests()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Staff ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Requested By</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <!-- Populated by JS -->
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Loading requests...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
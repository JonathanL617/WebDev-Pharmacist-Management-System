<!-- pharmacist stock management -->
<div class="tab-content" id="stock-management">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-capsule"></i></div>
            <p class="stat-label">Total Medicines</p>
            <div class="stat-value" id="totalMedicines">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <p class="stat-label">Low Stock Items</p>
            <div class="stat-value" id="lowStockItems">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <p class="stat-label">Out of Stock</p>
            <div class="stat-value" id="outOfStock">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <p class="stat-label">Total Value</p>
            <div class="stat-value" id="totalValue">$0</div>
        </div>
    </div>
    <h2>Medicine Information</h2>

    <!-- Search and Add Medicine -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search by ID or Name">
            </div>
        </div>
        <div class="col-lg-9 col-md-6 mb-3 text-lg-end">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="bi bi-plus-circle"></i> Add Medicine
            </button>
        </div>
    </div>

    <!-- Medicine Table -->
    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="medicinesTable">
                <tr>
                    <td colspan="6" class="text-center text-muted">Loading medicines...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Medicine Modal -->
    <div class="modal fade" id="medicineModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="medicineForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="medicineModalLabel">Add Medicine</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" id="medicine_name" name="medicine_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" class="form-control" id="medicine_price" name="medicine_price" required>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="medicine_quantity" name="medicine_quantity" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <input type="text" class="form-control" id="medicine_description" name="medicine_description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveMedicine()">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

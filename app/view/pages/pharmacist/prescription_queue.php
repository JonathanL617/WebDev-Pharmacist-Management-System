<!-- pharmacist prescription queue -->
<div class="tab-content" id="prescription-queue">
    <?php
    
    $loggedInUser = $_SESSION['user_id'] ?? 'P001'; // fallback
    ?>
    <script>
        const loggedInUserId = "<?= $loggedInUser ?>";
    </script>

    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-file-earmark-medical-fill"></i></div>
            <p class="stat-label">Total Orders</p>
            <div class="stat-value" id="totalOrders">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
            <p class="stat-label">Pending</p>
            <div class="stat-value" id="pendingOrders">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <p class="stat-label">Approved</p>
            <div class="stat-value" id="approvedOrders">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-all"></i></div>
            <p class="stat-label">Done</p>
            <div class="stat-value" id="doneOrders">0</div>
        </div>
    </div>
    <h2>Incoming Pharmacy Orders</h2>

    <!-- Filter Buttons -->
    <div class="mb-3">
        <button class="btn btn-secondary btn-sm" onclick="loadOrders()">All</button>
        <button class="btn btn-success btn-sm" onclick="loadOrders('Approved')">Approved</button>
        <button class="btn btn-danger btn-sm" onclick="loadOrders('Rejected')">Rejected</button>
        <button class="btn btn-warning btn-sm text-dark" onclick="loadOrders('Pending')">Pending</button>
        <button class="btn btn-info btn-sm text-dark" onclick="loadOrders('Done')">Done</button>
    </div>

    <!-- Orders Table -->
    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="ordersTable">
                <tr>
                    <td colspan="6" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Order Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody">Loading...</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal — FIXED ID! -->
    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="confirmTitle">Confirm Action</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="confirmOrderId">
                    Comment: <textarea class="form-control mt-2" id="confirmComment"></textarea>
                    <div id="confirmMsg" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="confirmBtn">Confirm</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API_URL = '<?= BASE_URL ?>/app/controller/PharmacistController2.php';

let currentAction = "";

// FIXED: This function name must match the one used in onclick!
function openConfirm(id, action) {
    currentAction = action;
    document.getElementById("confirmOrderId").value = id;
    document.getElementById("confirmTitle").textContent = 
        action === 'approve' ? 'Approve Order' :
        action === 'reject' ? 'Reject Order' : 'Mark as Done';
    document.getElementById("confirmComment").value = "";
    document.getElementById("confirmMsg").innerHTML = "";
    new bootstrap.Modal("#confirmModal").show();
}

document.getElementById("confirmBtn").onclick = function() {
    const id = document.getElementById("confirmOrderId").value;
    const comment = document.getElementById("confirmComment").value;

    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: currentAction,
            order_id: id,
            comment: comment
        })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            document.getElementById("confirmMsg").innerHTML = 
                `<div class="alert alert-danger">${res.msg}</div>`;
            return;
        }
        alert(res.msg || "Success!");
        bootstrap.Modal.getInstance("#confirmModal").hide();
        loadOrders(); // refresh table
    });
};

function viewOrder(id) {
    const body = document.getElementById("viewModalBody");
    body.innerHTML = "Loading...";
    new bootstrap.Modal("#viewModal").show();

    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'get_details', order_id: id })
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success || !d.order) {
            body.innerHTML = `<div class="alert alert-danger">${d.msg || 'Not found'}</div>`;
            return;
        }

        let html = `
            <p><strong>Order ID:</strong> ${d.order.order_id}</p>
            <p><strong>Date:</strong> ${d.order.order_date}</p>
            <p><strong>Patient:</strong> ${d.order.patient_name || '—'}</p>
            <p><strong>Doctor:</strong> ${d.order.staff_name || '—'}</p>
            <p><strong>Status:</strong> <span class="badge bg-primary">${d.order.status_id || 'Pending'}</span></p>
            <hr>
            <table class="table table-sm table-bordered">
                <tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Stock</th><th>Subtotal</th></tr>`;

        let total = 0;
        (d.details || []).forEach(item => {
            const sub = item.ordered_qty * item.medicine_price;
            total += sub;
            html += `<tr>
                <td>${item.medicine_name}</td>
                <td>${item.ordered_qty}</td>
                <td>RM ${parseFloat(item.medicine_price).toFixed(2)}</td>
                <td>${item.stock_qty}</td>
                <td>RM ${sub.toFixed(2)}</td>
            </tr>`;
        });

        html += `</table>
                 <div class="text-end fw-bold">Total: RM ${total.toFixed(2)}</div>`;
        body.innerHTML = html;
    });
}

function loadOrders(filter = '') {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'list_orders', filter: filter })
    })
    .then(r => r.json())
    .then(res => {
        const tbody = document.getElementById('ordersTable');
        tbody.innerHTML = '';

        if (!res.success || !res.orders || res.orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
            return;
        }

        res.orders.forEach(o => {
            const status = o.status_id || 'Pending';
            const badge = status === 'Approved' ? 'bg-success' :
                         status === 'Rejected' ? 'bg-danger' :
                         status === 'Done' ? 'bg-info text-dark' : 'bg-warning text-dark';

            tbody.innerHTML += `
                <tr>
                    <td>${o.order_id}</td>
                    <td>${o.order_date}</td>
                    <td>${o.patient_name || '—'}</td>
                    <td>${o.staff_name || '—'}</td>
                    <td><span class="badge ${badge}">${status}</span></td>
                    <td>
                        <button class="btn btn-primary btn-sm me-1" onclick="viewOrder('${o.order_id}')">View</button>
                        <button class="btn btn-success btn-sm me-1" onclick="openConfirm('${o.order_id}','approve')">Approve</button>
                        <button class="btn btn-danger btn-sm me-1" onclick="openConfirm('${o.order_id}','reject')">Reject</button>
                        <button class="btn btn-info btn-sm" onclick="openConfirm('${o.order_id}','done')">Done</button>
                    </td>
                </tr>`;
        });
    });
}

// Load on start
loadOrders();
</script>
</body>
</html>

// Load orders and update stats
loggedInUser = document.body.dataset.staffId;
console.log('Staff ID from data attribute:', loggedInUser); // Add this line
function loadOrders(filter = '') {
    fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'list_orders', filter })
    })
        .then(r => r.json())
        .then(d => {
            console.log('Orders response:', d); // Debug log
            let tbody = document.getElementById('ordersTable');
            if (!tbody) {
                console.error('ordersTable element not found');
                return;
            }
            
            tbody.innerHTML = '';
            
            if (!d.success || !d.orders || d.orders.length === 0) {
                tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No orders found</td></tr>";
                updateStats([]);
                return;
            }

            updateStats(d.orders);

            d.orders.forEach(o => {
                // Fix: Use the correct status field name
                const status = o.status_id || o.status || 'Pending';
                const statusClass = getStatusBadgeClass(status);
                
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${o.order_id}</td>
                    <td>${o.order_date}</td>
                    <td>${o.patient_name || '—'}</td>
                    <td>${o.staff_name || '—'}</td>
                    <td><span class="badge ${statusClass}">${status}</span></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="viewOrder('${o.order_id}')">View</button>
                        ${status.toLowerCase() === 'pending' ? `
                            <button class="btn btn-success btn-sm" onclick="openConfirm('${o.order_id}','approve')">Approve</button>
                            <button class="btn btn-danger btn-sm" onclick="openConfirm('${o.order_id}','reject')">Reject</button>
                        ` : ''}
                        ${status.toLowerCase() === 'approved' ? `
                            <button class="btn btn-info btn-sm" onclick="openConfirm('${o.order_id}','done')">Done</button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            let tbody = document.getElementById('ordersTable');
            if (tbody) {
                tbody.innerHTML = "<tr><td colspan='6' class='text-center text-danger'>Error loading orders</td></tr>";
            }
        });
}

// Add this helper function for status badges
function getStatusBadgeClass(status) {
    const statusLower = status.toLowerCase();
    switch(statusLower) {
        case 'approved': return 'bg-success';
        case 'rejected': return 'bg-danger';
        case 'done': return 'bg-info text-dark';
        case 'completed': return 'bg-info text-dark';
        case 'pending': return 'bg-warning text-dark';
        default: return 'bg-secondary';
    }
}

// Also fix the updateStats function to handle the status field correctly
function updateStats(orders) {
    const total = orders.length;
    const pending = orders.filter(o => {
        const status = o.status_id || o.status || '';
        return status.toLowerCase() === 'pending';
    }).length;
    const approved = orders.filter(o => {
        const status = o.status_id || o.status || '';
        return status.toLowerCase() === 'approved';
    }).length;
    const rejected = orders.filter(o => {
        const status = o.status_id || o.status || '';
        return status.toLowerCase() === 'rejected';
    }).length;

    document.getElementById('totalOrders').textContent = total;
    document.getElementById('pendingOrders').textContent = pending;
    document.getElementById('approvedOrders').textContent = approved;
    document.getElementById('rejectedOrders').textContent = rejected;
}



let confirmMode = "";

function openConfirm(id, mode) {
    confirmMode = mode;
    document.getElementById("confirmOrderId").value = id;
    document.getElementById("confirmTitle").innerHTML = (mode == "approve") ? "Approve Order" : (mode == "reject") ? "Reject Order" : "Mark as Done";
    document.getElementById("confirmMsg").innerHTML = "";
    document.getElementById("confirmComment").value = "";
    new bootstrap.Modal(document.getElementById("confirmModal")).show();
}

const confirmBtn = document.getElementById("confirmBtn");
if (confirmBtn) {
    confirmBtn.onclick = function () {
        let id = document.getElementById("confirmOrderId").value;
        let approver = loggedInUser; // Ensure this variable is defined or available
        let comment = document.getElementById("confirmComment").value;
        

        fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: confirmMode, order_id: id, approver_id: approver, comment: comment })
        })
            .then(r => r.json())
            .then(d => {
                if (!d.success) {
                    document.getElementById("confirmMsg").innerHTML = `<div class='alert alert-danger'>${d.msg}</div>`;
                    return;
                }
                alert("Order " + confirmMode + " successfully!");
                loadOrders();
                bootstrap.Modal.getInstance(document.getElementById("confirmModal")).hide();
            });
    }
}

function viewOrder(id) {
    document.getElementById("viewModalBody").innerHTML = "Loading...";
    new bootstrap.Modal(document.getElementById("viewModal")).show();

    fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "get_details", order_id: id })
    })
        .then(r => r.json())
        .then(d => {
            if (!d.success) { document.getElementById("viewModalBody").innerHTML = d.msg; return; }
            let o = d.order, det = d.details;
            let html = `<strong>Order:</strong> ${o.order_id}<br>
                  <strong>Date:</strong> ${o.order_date}<br>
                  <strong>Patient:</strong> ${o.patient_name}<br>
                  <strong>Doctor:</strong> ${o.staff_name}<br>
                  <strong>Status:</strong> ${o.status_id}<hr>`;

            // Add approval history section
            if (d.approval_details && d.approval_details.length > 0) {
                html += `<h6>Approval History</h6>`;
                d.approval_details.forEach(approval => {
                    html += `<div class="border-bottom pb-2 mb-2">
                        <strong>${approval.approval_status}</strong> 
                        <small class="text-muted">by ${approval.approver_id} on ${approval.approval_date}</small><br>
                        ${approval.approval_comment ? `<em>"${approval.approval_comment}"</em>` : '<em>No comment</em>'}
                    </div>`;
                });
                html += `<hr>`;
            }

            html += `<h6>Medicines</h6>
                  <table class="table table-bordered"><tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Stock</th><th>Subtotal</th></tr>`;
            
            let total = 0;
            det.forEach(x => {
                let sub = x.ordered_qty * x.medicine_price;
                total += sub;
                html += `<tr><td>${x.medicine_name}</td><td>${x.ordered_qty}</td><td>${x.medicine_price}</td><td>${x.stock_qty}</td><td>${sub.toFixed(2)}</td></tr>`;
            });
            
            html += `</table><div class="text-end"><strong>Total: RM ${total.toFixed(2)}</strong></div>`;
            document.getElementById("viewModalBody").innerHTML = html;
        });
}

document.addEventListener('DOMContentLoaded', () => loadOrders());

// Search functionality
const searchInput = document.getElementById('searchOrders');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.trim();
        if (searchTerm) {
            // Send search request
            fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'list_orders', search: searchTerm })
            })
            .then(r => r.json())
            .then(d => {
                console.log('Search results:', d);
                let tbody = document.getElementById('ordersTable');
                if (!tbody) return;
                
                tbody.innerHTML = '';
                
                if (!d.success || !d.orders || d.orders.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No orders found for '" + searchTerm + "'</td></tr>";
                    updateStats([]);
                    return;
                }

                updateStats(d.orders);

                d.orders.forEach(o => {
                    const status = o.status_id || o.status || 'Pending';
                    const statusClass = getStatusBadgeClass(status);
                    
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${o.order_id}</td>
                        <td>${o.order_date}</td>
                        <td>${o.patient_name || '—'}</td>
                        <td>${o.staff_name || '—'}</td>
                        <td><span class="badge ${statusClass}">${status}</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="viewOrder('${o.order_id}')">View</button>
                            ${status.toLowerCase() === 'pending' ? `
                                <button class="btn btn-success btn-sm" onclick="openConfirm('${o.order_id}','approve')">Approve</button>
                                <button class="btn btn-danger btn-sm" onclick="openConfirm('${o.order_id}','reject')">Reject</button>
                            ` : ''}
                            ${status.toLowerCase() === 'approved' ? `
                                <button class="btn btn-info btn-sm" onclick="openConfirm('${o.order_id}','done')">Done</button>
                            ` : ''}
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
            });
        } else {
            // If search is empty, reload all orders
            loadOrders();
        }
    });
}

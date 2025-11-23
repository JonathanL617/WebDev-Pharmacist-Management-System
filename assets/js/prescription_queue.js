
// 1. CORRECT API URL


// 2. PASS LOGGED IN USER ID FROM PHP (ADD THIS IN YOUR PHP FILE BEFORE JS!)
const loggedInUserId = "<?= $_SESSION['staff_id'] ?? 'P001' ?>";

let confirmMode = "";

function openConfirm(id, mode) {
    confirmMode = mode;
    document.getElementById("confirmOrderId").value = id;
    document.getElementById("confirmTitle").innerHTML = 
        mode === "approve" ? "Approve Order" : 
        mode === "reject" ? "Reject Order" : "Mark as Done";
    document.getElementById("confirmMsg").innerHTML = "";
    new bootstrap.Modal(document.getElementById("confirmModal")).show();
}

document.getElementById("confirmBtn").onclick = function() {
    const id = document.getElementById("confirmOrderId").value;
    const comment = document.getElementById("confirmComment").value;

    fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: confirmMode,
            order_id: id,
            approver_id: loggedInUserId,  // NOW IT WORKS!
            comment: comment
        })
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            document.getElementById("confirmMsg").innerHTML = 
                `<div class='alert alert-danger'>${d.msg}</div>`;
            return;
        }
        alert("Order " + confirmMode + "d successfully!");
        bootstrap.Modal.getInstance(document.getElementById("confirmModal")).hide();
        loadOrders(); // refresh table
    });
};

function viewOrder(id) {
    const body = document.getElementById("viewModalBody");
    body.innerHTML = "Loading...";
    const modal = new bootstrap.Modal(document.getElementById("viewModal"));
    modal.show();

    fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "get_details", order_id: id })
    })
    .then(r => r.json())
    .then(d => {
        console.log("View Response:", d); // CHECK THIS IN F12 CONSOLE!

        if (!d.success || !d.order) {
            body.innerHTML = `<div class="alert alert-danger">${d.msg || 'Order not found'}</div>`;
            return;
        }

        let html = `
            <strong>Order ID:</strong> ${d.order.order_id}<br>
            <strong>Date:</strong> ${d.order.order_date}<br>
            <strong>Patient:</strong> ${d.order.patient_name || '—'}<br>
            <strong>Doctor:</strong> ${d.order.staff_name || '—'}<br>
            <strong>Status:</strong> <span class="badge bg-primary">${d.order.status_id || 'Pending'}</span><hr>
            <table class="table table-bordered">
                <tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Stock</th><th>Subtotal</th></tr>`;

        let total = 0;
        (d.details || []).forEach(x => {
            const sub = x.ordered_qty * x.medicine_price;
            total += sub;
            html += `<tr>
                <td>${x.medicine_name}</td>
                <td>${x.ordered_qty}</td>
                <td>RM ${parseFloat(x.medicine_price).toFixed(2)}</td>
                <td>${x.stock_qty}</td>
                <td>RM ${sub.toFixed(2)}</td>
            </tr>`;
        });

        html += `</table>
                 <div class="text-end"><strong>Total: RM ${total.toFixed(2)}</strong></div>`;
        body.innerHTML = html;
    })
    .catch(err => {
        body.innerHTML = `<div class="alert alert-danger">Network error</div>`;
        console.error(err);
    });
}

function loadOrders(filter = '') {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'list_orders', filter: filter })
    })
    .then(r => r.json())
    .then(d => {
        const tbody = document.getElementById('ordersTable');
        tbody.innerHTML = '';

        if (!d.success || !d.orders || d.orders.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No orders found</td></tr>";
            return;
        }

        d.orders.forEach(o => {
            const status = o.status_id || 'Pending';
            const badgeClass = 
                status === 'Approved' ? 'bg-success' :
                status === 'Rejected' ? 'bg-danger' :
                status === 'Done' ? 'bg-info text-dark' : 'bg-warning text-dark';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${o.order_id}</td>
                <td>${o.order_date}</td>
                <td>${o.patient_name || '—'}</td>
                <td>${o.staff_name || '—'}</td>
                <td><span class="badge ${badgeClass}">${status}</span></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="viewOrder('${o.order_id}')">View</button>
                    <button class="btn btn-success btn-sm" onclick="openConfirm('${o.order_id}','approve')">Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="openConfirm('${o.order_id}','reject')">Reject</button>
                    <button class="btn btn-info btn-sm" onclick="openConfirm('${o.order_id}','done')">Done</button>
                </td>`;
            tbody.appendChild(row);
        });
    });
}

// Load orders when page loads
document.addEventListener('DOMContentLoaded', () => loadOrders());

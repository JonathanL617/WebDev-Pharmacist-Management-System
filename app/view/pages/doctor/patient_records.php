<?php require_once __DIR__ . '/../../../config/config.php'; ?>

<div class="container py-4">
    <h2 class="mb-4 text-primary fw-bold">Patient Order History</h2>

    <!-- Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <input type="text" class="form-control form-control-lg" id="searchPatient" placeholder="Search by Patient ID or Name...">
        </div>
    </div>

    <!-- Patients List -->
    <div class="card shadow" id="patientsCard">
        <div class="card-header bg-primary text-white fs-5">All Patients</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                        <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order History Section -->
    <div id="orderHistorySection" style="display:none;">
        <div class="card bg-primary text-white mb-4 shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 id="bannerPatientName" class="mb-0"></h4>
                    <p id="bannerPatientId" class="mb-0 opacity-75"></p>
                </div>
                <button class="btn btn-light" id="backToPatients">Back to List</button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">Patient Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><strong>DOB:</strong> <span id="detailDob">-</span></div>
                    <div class="col-md-6"><strong>Phone:</strong> <span id="detailPhone">-</span></div>
                    <div class="col-md-6"><strong>Gender:</strong> <span id="detailGender">-</span></div>
                    <div class="col-md-6"><strong>Email:</strong> <span id="detailEmail">-</span></div>
                    <div class="col-md-12"><strong>Address:</strong> <span id="detailAddress">-</span></div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-light">Order History</div>
            <div class="card-body" id="ordersContainer">
                <p class="text-center text-muted">Loading orders...</p>
            </div>
        </div>
    </div>
</div>

<!-- FINAL WORKING SCRIPT — 100% GUARANTEED -->
<script>
    window.BASE_URL = "/WebDev-Pharmacist-Management-System/";
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tbody = document.getElementById("patientsTableBody");
    const searchInput =  document.getElementById("searchPatient");
    const orderSection = document.getElementById("orderHistorySection");
    const patientsCard = document.getElementById("patientsCard");
    const backBtn = document.getElementById("backToPatients");

    const API_URL = window.BASE_URL + "app/controller/DoctorController2.php";
    let allPatients = [];

    function formatDate(dateStr) {
        if (!dateStr || dateStr === '0000-00-00') return '-';
        return new Date(dateStr).toLocaleDateString('en-GB');
    }

    function renderPatients(data) {
        tbody.innerHTML = "";
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No patients found</td></tr>`;
            return;
        }
        data.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${p.patient_id}</strong></td>
                    <td>${p.patient_name}</td>
                    <td>${formatDate(p.patient_date_of_birth)}</td>
                    <td>${p.patient_phone || '-'}</td>
                    <td>${p.patient_gender || '-'}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="viewPatientOrders('${p.patient_id}', '${p.patient_name}')">
                            View Orders
                        </button>
                    </td>
                </tr>`;
        });
    }

    async function loadPatients() {
        try {
            const res = await fetch(API_URL + "?action=patients");
            const json = await res.json();

            if (json.status === "success") {
                allPatients = json.data;
                renderPatients(allPatients);
            } else {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Failed to load patients</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Network error</td></tr>`;
            console.error(err);
        }
    }

    // Search functionality
    searchInput.addEventListener("input", () => {
        const term = searchInput.value.toLowerCase().trim();
        const filtered = allPatients.filter(p =>
            p.patient_id.toLowerCase().includes(term) ||
            p.patient_name.toLowerCase().includes(term)
        );
        renderPatients(filtered);
    });

    // View patient orders
    window.viewPatientOrders = async function(id, name) {
        document.getElementById("bannerPatientName").textContent = name;
        document.getElementById("bannerPatientId").textContent = "ID: " + id;

        const patient = allPatients.find(p => p.patient_id === id);
        document.getElementById("detailDob").textContent = formatDate(patient.patient_date_of_birth);
        document.getElementById("detailPhone").textContent = patient.patient_phone || "-";
        document.getElementById("detailGender").textContent = patient.patient_gender || "-";
        document.getElementById("detailEmail").textContent = patient.patient_email || "-";
        document.getElementById("detailAddress").textContent = patient.patient_address || "-";

        const container = document.getElementById("ordersContainer");
        container.innerHTML = `<p class="text-center"><div class="spinner-border text-primary"></div></p>`;

        try {
            const res = await fetch(API_URL + "?action=orders&patient_id=" + id);
            const json = await res.json();

            if (json.status === "success" && json.orders && json.orders.length > 0) {
                container.innerHTML = json.orders.map(order => {
                    let total = 0;
                    const meds = order.medicines.map(m => {
                        const sub = m.medicine_quantity * m.medicine_price;
                        total += sub;
                        return `<tr>
                            <td>${m.medicine_name || 'Unknown'}</td>
                            <td>${m.medicine_quantity}</td>
                            <td>RM ${Number(m.medicine_price).toFixed(2)}</td>
                            <td>RM ${sub.toFixed(2)}</td>
                        </tr>`;
                    }).join("");

                    return `
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h6>Order #${order.order_id} • ${formatDate(order.order_date)}</h6>
                            <table class="table table-sm mt-2">
                                <thead class="table-light">
                                    <tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                                </thead>
                                <tbody>${meds}
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Grand Total:</td>
                                        <td>RM ${total.toFixed(2)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>`;
                }).join("");
            } else {
                container.innerHTML = `<p class="text-center text-muted fs-5">No order history found.</p>`;
            }
        } catch (err) {
            container.innerHTML = `<p class="text-center text-danger">Failed to load orders</p>`;
        }

        patientsCard.style.display = "none";
        orderSection.style.display = "block";
    };

    // Back button
    backBtn.addEventListener("click", () => {
        orderSection.style.display = "none";
        patientsCard.style.display = "block";
    });

    // Load patients on start
    loadPatients();
});
</script>
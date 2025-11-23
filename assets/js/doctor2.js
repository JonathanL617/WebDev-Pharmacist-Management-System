document.addEventListener("DOMContentLoaded", function () {
    const tbody = document.getElementById("patientsTableBody");
    const searchInp = document.getElementById("searchPatient");
    const backBtn = document.getElementById("backToPatients");
    const orderSec = document.getElementById("orderHistorySection");
    const patientsCard = document.getElementById("patientsCard");
    let patients = [];

    // THIS IS THE CORRECT URL — 100% WORKING
    const API_URL = window.BASE_URL + "app/controller/DoctorController2.php";

    async function loadPatients(query = "") {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;
        
        try {
            const res = await fetch(API_URL + "?action=patients");
            const json = await res.json();

            if (json.status !== "success") {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Failed to load patients</td></tr>`;
                return;
            }

            const term = query.toLowerCase().trim();
            patients = term
                ? json.data.filter(p => 
                    p.patient_id.toLowerCase().includes(term) || 
                    p.patient_name.toLowerCase().includes(term)
                  )
                : json.data;

            renderPatients();
        } catch (err) {
            console.error("Network Error:", err);
            tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">
                Network error<br>
                <small>Check: <code>${API_URL}?action=patients</code></small>
            </td></tr>`;
        }
    }

    function renderPatients() {
        tbody.innerHTML = "";
        if (!patients.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No patients found</td></tr>`;
            return;
        }

        patients.forEach(p => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><strong>${p.patient_id}</strong></td>
                <td>${p.patient_name}</td>
                <td>${formatDate(p.patient_date_of_birth)}</td>
                <td>${p.patient_phone || '-'}</td>
                <td>${p.patient_gender || '-'}</td>
                <td><button class="btn btn-primary btn-sm" onclick="viewOrders('${p.patient_id}', this)">View Orders</button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    window.viewOrders = async function(id, btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const patient = patients.find(p => p.patient_id === id);
        if (!patient) return alert("Patient not found");

        document.getElementById("bannerPatientName").textContent = patient.patient_name;
        document.getElementById("bannerPatientId").textContent = "ID: " + id;
        document.getElementById("detailDob").textContent = formatDate(patient.patient_date_of_birth);
        document.getElementById("detailPhone").textContent = patient.patient_phone || "-";
        document.getElementById("detailGender").textContent = patient.patient_gender || "-";
        document.getElementById("detailEmail").textContent = patient.patient_email || "-";
        document.getElementById("detailAddress").textContent = patient.patient_address || "-";

        const container = document.getElementById("ordersContainer");
        container.innerHTML = `<p class="text-center"><div class="spinner-border text-primary"></div></p>`;

        try {
            const res = await fetch(`${window.BASE_URL}/app/controller/DoctorController2.php?action=orders&patient_id=${id}`);
            const json = await res.json();

            if (json.status !== "success" || !json.orders || json.orders.length === 0) {
                container.innerHTML = `<p class="text-center text-muted fs-5">No order history found.</p>`;
            } else {
                container.innerHTML = json.orders.map(order => {
                    let total = 0;
                    const rows = order.medicines.map(m => {
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
                        <div class="border rounded p-4 mb-3 bg-light shadow-sm">
                            <h6 class="text-primary mb-1">Order #${order.order_id}</h6>
                            <small class="text-muted">${formatDate(order.order_date)}</small>
                            <table class="table table-sm mt-3">
                                <thead class="table-light"><tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                                <tbody>${rows}
                                    <tr class="fw-bold table-warning">
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td>RM ${total.toFixed(2)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>`;
                }).join("");
            }

            patientsCard.style.display = "none";
            orderSec.style.display = "block";
        } catch (err) {
            container.innerHTML = `<p class="text-center text-danger">Failed to load orders</p>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = "View Orders";
        }
    };

    backBtn.addEventListener("click", () => {
        orderSec.style.display = "none";
        patientsCard.style.display = "block";
    });

    let timeout;
    searchInp.addEventListener("input", () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => loadPatients(searchInp.value), 400);
    });

    loadPatients();
});
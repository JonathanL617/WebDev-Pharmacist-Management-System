document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('patientsTableBody');
    const searchInput = document.getElementById('searchPatient');
    const clearBtn = document.getElementById('clearSearch');

    const patientsCard = document.getElementById('patientsCard');
    const orderHistorySection = document.getElementById('orderHistorySection');
    const backBtn = document.getElementById('backToPatients');

    // Load patients
    function loadPatients(search = '') {
        fetch(`../../app/controller/DoctorController.php?action=getPatients&search=${search}`)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (!data.success || data.patients.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No patients found</td></tr>';
                    return;
                }

                data.patients.forEach(p => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${p.patient_id}</td>
                        <td>${p.patient_name}</td>
                        <td>${p.patient_date_of_birth}</td>
                        <td>${p.patient_phone}</td>
                        <td>${p.patient_gender}</td>
                        <td>
                            <button class="btn btn-sm btn-info text-white view-btn" 
                                data-id="${p.patient_id}"
                                data-name="${p.patient_name}"
                                data-dob="${p.patient_date_of_birth}"
                                data-phone="${p.patient_phone}"
                                data-gender="${p.patient_gender}"
                                data-email="${p.patient_email}"
                                data-address="${p.patient_address}">
                                View History
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // Add listeners to view buttons
                document.querySelectorAll('.view-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        showHistory(btn.dataset);
                    });
                });
            })
            .catch(err => console.error(err));
    }

    // Show History
    function showHistory(data) {
        // Hide list, show details
        patientsCard.style.display = 'none';
        orderHistorySection.style.display = 'block';

        // Fill Info
        document.getElementById('bannerPatientName').textContent = data.name;
        document.getElementById('bannerPatientId').textContent = 'Patient ID: ' + data.id;
        document.getElementById('detailDob').textContent = data.dob;
        document.getElementById('detailPhone').textContent = data.phone;
        document.getElementById('detailGender').textContent = data.gender;
        document.getElementById('detailEmail').textContent = data.email;
        document.getElementById('detailAddress').textContent = data.address;

        // Fetch Orders
        const container = document.getElementById('ordersContainer');
        container.innerHTML = '<p class="text-center">Loading history...</p>';

        fetch(`../../app/controller/DoctorController.php?action=getPatientHistory&id=${data.id}`)
            .then(res => res.json())
            .then(resData => {
                container.innerHTML = '';
                if (!resData.success || resData.history.length === 0) {
                    container.innerHTML = '<p class="text-muted">No order history found.</p>';
                    return;
                }

                resData.history.forEach(order => {
                    const div = document.createElement('div');
                    div.className = 'border-bottom pb-2 mb-2';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <strong>Order #${order.order_id}</strong>
                            <span class="badge bg-${getStatusColor(order.status_id)}">${order.status_id}</span>
                        </div>
                        <small class="text-muted">${order.order_date}</small>
                        <p class="mb-0 mt-1">${order.medicines}</p>
                    `;
                    container.appendChild(div);
                });
            });
    }

    function getStatusColor(status) {
        if (status === 'Pending') return 'warning';
        if (status === 'Approved') return 'success';
        if (status === 'Rejected') return 'danger';
        return 'secondary';
    }

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', (e) => loadPatients(e.target.value));
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            loadPatients();
        });
    }

    if (backBtn) {
        backBtn.addEventListener('click', () => {
            orderHistorySection.style.display = 'none';
            patientsCard.style.display = 'block';
        });
    }

    // Initial Load
    loadPatients();
});

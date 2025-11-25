let allMedicines = [];
loggedInUser = document.body.dataset.staffId;
console.log('Staff ID from data attribute:', loggedInUser); // Add this line
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('prescriptionsTable');
    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('createPrescriptionForm');
    const patientSelect = document.getElementById('prescriptionPatient');

    // Initial Load
    loadPrescriptions();
    loadPatients();
    loadMedicinesList();
    loadDoctorStats();
    addMedicineRow(); // Add first row by default

    // Load Prescriptions
    function loadPrescriptions(search = '') {
    fetch(`../../app/controller/DoctorController.php?action=getPrescriptionsByStaff&search=${search}&staff_id=${loggedInUser}`)
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (!data.success || data.prescriptions.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No prescriptions found</td></tr>';
                return;
            }

            data.prescriptions.forEach(p => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${p.order_id}</td>
                    <td>${p.order_date}</td>
                    <td>${p.patient_name}</td>
                    <td>${p.staff_name || '—'}</td>
                    <td><span class="badge bg-${getStatusColor(p.status_id)}">${p.status_id}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewPrescription('${p.order_id}')">View</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(err => console.error(err));
}

    // Load Patients for Dropdown
    function loadPatients() {
        fetch('../../app/controller/DoctorController.php?action=getPatients')
            .then(res => res.json())
            .then(data => {
                patientSelect.innerHTML = '<option value="">Select Patient</option>';
                if (data.success) {
                    data.patients.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.patient_id;
                        opt.textContent = `${p.patient_name} (${p.patient_id})`;
                        patientSelect.appendChild(opt);
                    });
                }
            });
    }

    // Load Medicines for Dropdown
    function loadMedicinesList() {
        fetch('../../app/controller/DoctorController.php?action=getMedicines')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allMedicines = data.medicines;
                    // Update existing rows if any
                    document.querySelectorAll('.medicine-select').forEach(select => {
                        if (select.options.length <= 1) populateMedicineSelect(select);
                    });
                }
            });
    }

    // Load Doctor Statistics
    function loadDoctorStats() {
        console.log('Loading doctor stats for staff:', loggedInUser);
        
        fetch('../../app/controller/DoctorController.php?action=getDoctorStats')
            .then(res => res.json())
            .then(data => {
                console.log('Doctor stats response:', data);
                if (data.success) {
                    // Update the stats in your HTML
                    document.getElementById('totalPrescriptions').textContent = data.stats.total_prescriptions || '0';
                    document.getElementById('pendingPrescriptions').textContent = data.stats.pending_prescriptions || '0';
                    document.getElementById('approvedPrescriptions').textContent = data.stats.approved_prescriptions || '0';
                    document.getElementById('rejectedPrescriptions').textContent = data.stats.rejected_prescriptions || '0';
                } else {
                    console.error('Failed to load doctor stats:', data.msg);
                }
            })
            .catch(err => console.error('Error loading doctor stats:', err));
    }

    // Form Submission - UPDATED WITH STAFF ID
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const patientId = patientSelect.value;
            const medicineRows = document.querySelectorAll('.medicine-row');
            const medicines = [];

            medicineRows.forEach(row => {
                const select = row.querySelector('.medicine-select');
                const qty = row.querySelector('.medicine-qty');
                if (select.value && qty.value > 0) {
                    medicines.push({ id: select.value, qty: parseInt(qty.value) });
                }
            });

            if (medicines.length === 0) {
                alert('Please add at least one medicine.');
                return;
            }

            fetch('../../app/controller/DoctorController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'createPrescription',
                    patient_id: patientId,
                    medicines: medicines,
                    staff_id: loggedInUser // ADDED STAFF ID FROM SESSION
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Prescription created successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('createPrescriptionModal')).hide();
                        form.reset();
                        document.getElementById('medicineList').innerHTML = '';
                        addMedicineRow();
                        loadPrescriptions();
                    } else {
                        alert('Error: ' + data.msg);
                    }
                })
                .catch(err => console.error(err));
        });
    }

    // Search Listener
    if (searchInput) {
        searchInput.addEventListener('input', (e) => loadPrescriptions(e.target.value));
    }
});

// Global function to add medicine row
function addMedicineRow() {
    const container = document.getElementById('medicineList');
    const div = document.createElement('div');
    div.className = 'row mb-2 medicine-row';
    div.innerHTML = `
        <div class="col-8">
            <select class="form-select medicine-select" required>
                <option value="">Select Medicine</option>
            </select>
        </div>
        <div class="col-3">
            <input type="number" class="form-control medicine-qty" placeholder="Qty" min="1" required>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.medicine-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);

    // Populate select
    const select = div.querySelector('.medicine-select');
    populateMedicineSelect(select);
}

function populateMedicineSelect(select) {
    allMedicines.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.medicine_id;
        opt.textContent = `${m.medicine_name} (Stock: ${m.medicine_quantity})`;
        select.appendChild(opt);
    });
}

function getStatusColor(status) {
    const statusLower = status.toLowerCase();
    if (statusLower === 'pending') return 'warning';
    if (statusLower === 'approved') return 'success';
    if (statusLower === 'rejected') return 'danger';
    if (statusLower === 'done' || statusLower === 'completed') return 'info';
    return 'secondary';
}


// Function to view prescription details
function viewPrescription(orderId) {
    console.log('Viewing prescription:', orderId);
    
    // Show loading state
    document.getElementById('viewMedicineList').innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
    
    // Fetch prescription details
    fetch(`../../app/controller/DoctorController.php?action=getPrescriptionDetails&order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Populate modal with basic info
                document.getElementById('viewOrderId').textContent = data.prescription.order_id;
                document.getElementById('viewPatientName').textContent = data.prescription.patient_name;
                document.getElementById('viewDoctorName').textContent = data.prescription.staff_name;
                document.getElementById('viewOrderDate').textContent = data.prescription.order_date;
                
                // Set status with badge
                const statusElement = document.getElementById('viewStatus');
                statusElement.innerHTML = `<span class="badge bg-${getStatusColor(data.prescription.status_id)}">${data.prescription.status_id}</span>`;
                
                // Populate medicine list
                const medicineList = document.getElementById('viewMedicineList');
                medicineList.innerHTML = '';
                
                if (data.medicines && data.medicines.length > 0) {
                    data.medicines.forEach(med => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${med.medicine_name}</td>
                            <td>${med.medicine_quantity}</td>
                        `;
                        medicineList.appendChild(row);
                    });
                } else {
                    medicineList.innerHTML = '<tr><td colspan="2" class="text-center">No medicines found</td></tr>';
                }
                
                // Show the modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewPrescriptionModal'));
                viewModal.show();
                
            } else {
                alert('Error loading prescription details: ' + data.msg);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error loading prescription details');
        });

        let allMedicines = [];
loggedInUser = document.body.dataset.staffId;
console.log('Staff ID from data attribute:', loggedInUser); // Add this line
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('prescriptionsTable');
    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('createPrescriptionForm');
    const patientSelect = document.getElementById('prescriptionPatient');

    // Initial Load
    loadPrescriptions();
    loadPatients();
    loadMedicinesList();
    loadDoctorStats();
    addMedicineRow(); // Add first row by default

    // Load Prescriptions
    function loadPrescriptions(search = '') {
    fetch(`../../app/controller/DoctorController.php?action=getPrescriptionsByStaff&search=${search}&staff_id=${loggedInUser}`)
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (!data.success || data.prescriptions.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No prescriptions found</td></tr>';
                return;
            }

            data.prescriptions.forEach(p => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${p.order_id}</td>
                    <td>${p.order_date}</td>
                    <td>${p.patient_name}</td>
                    <td>${p.staff_name || '—'}</td>
                    <td><span class="badge bg-${getStatusColor(p.status_id)}">${p.status_id}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewPrescription('${p.order_id}')">View</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(err => console.error(err));
}

    // Load Patients for Dropdown
    function loadPatients() {
        fetch('../../app/controller/DoctorController.php?action=getPatients')
            .then(res => res.json())
            .then(data => {
                patientSelect.innerHTML = '<option value="">Select Patient</option>';
                if (data.success) {
                    data.patients.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.patient_id;
                        opt.textContent = `${p.patient_name} (${p.patient_id})`;
                        patientSelect.appendChild(opt);
                    });
                }
            });
    }

    // Load Medicines for Dropdown
    function loadMedicinesList() {
        fetch('../../app/controller/DoctorController.php?action=getMedicines')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allMedicines = data.medicines;
                    // Update existing rows if any
                    document.querySelectorAll('.medicine-select').forEach(select => {
                        if (select.options.length <= 1) populateMedicineSelect(select);
                    });
                }
            });
    }

    // Load Doctor Statistics
    function loadDoctorStats() {
        console.log('Loading doctor stats for staff:', loggedInUser);
        
        fetch('../../app/controller/DoctorController.php?action=getDoctorStats')
            .then(res => res.json())
            .then(data => {
                console.log('Doctor stats response:', data);
                if (data.success) {
                    // Update the stats in your HTML
                    document.getElementById('totalPrescriptions').textContent = data.stats.total_prescriptions || '0';
                    document.getElementById('pendingPrescriptions').textContent = data.stats.pending_prescriptions || '0';
                    document.getElementById('approvedPrescriptions').textContent = data.stats.approved_prescriptions || '0';
                    document.getElementById('rejectedPrescriptions').textContent = data.stats.rejected_prescriptions || '0';
                } else {
                    console.error('Failed to load doctor stats:', data.msg);
                }
            })
            .catch(err => console.error('Error loading doctor stats:', err));
    }

    // Form Submission - UPDATED WITH STAFF ID
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const patientId = patientSelect.value;
            const medicineRows = document.querySelectorAll('.medicine-row');
            const medicines = [];

            medicineRows.forEach(row => {
                const select = row.querySelector('.medicine-select');
                const qty = row.querySelector('.medicine-qty');
                if (select.value && qty.value > 0) {
                    medicines.push({ id: select.value, qty: parseInt(qty.value) });
                }
            });

            if (medicines.length === 0) {
                alert('Please add at least one medicine.');
                return;
            }

            fetch('../../app/controller/DoctorController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'createPrescription',
                    patient_id: patientId,
                    medicines: medicines,
                    staff_id: loggedInUser // ADDED STAFF ID FROM SESSION
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Prescription created successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('createPrescriptionModal')).hide();
                        form.reset();
                        document.getElementById('medicineList').innerHTML = '';
                        addMedicineRow();
                        loadPrescriptions();
                    } else {
                        alert('Error: ' + data.msg);
                    }
                })
                .catch(err => console.error(err));
        });
    }

    // Search Listener
    if (searchInput) {
        searchInput.addEventListener('input', (e) => loadPrescriptions(e.target.value));
    }
});

// Global function to add medicine row
function addMedicineRow() {
    const container = document.getElementById('medicineList');
    const div = document.createElement('div');
    div.className = 'row mb-2 medicine-row';
    div.innerHTML = `
        <div class="col-8">
            <select class="form-select medicine-select" required>
                <option value="">Select Medicine</option>
            </select>
        </div>
        <div class="col-3">
            <input type="number" class="form-control medicine-qty" placeholder="Qty" min="1" required>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.medicine-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);

    // Populate select
    const select = div.querySelector('.medicine-select');
    populateMedicineSelect(select);
}

function populateMedicineSelect(select) {
    allMedicines.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.medicine_id;
        opt.textContent = `${m.medicine_name} (Stock: ${m.medicine_quantity})`;
        select.appendChild(opt);
    });
}

function getStatusColor(status) {
    const statusLower = status.toLowerCase();
    if (statusLower === 'pending') return 'warning';
    if (statusLower === 'approved') return 'success';
    if (statusLower === 'rejected') return 'danger';
    if (statusLower === 'done' || statusLower === 'completed') return 'info';
    return 'secondary';
}

// Function to view prescription details
function viewPrescription(orderId) {
    console.log('Viewing prescription:', orderId);
    
    // Show loading state
    document.getElementById('viewMedicineList').innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
    
    // Fetch prescription details
    fetch(`../../app/controller/DoctorController.php?action=getPrescriptionDetails&order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Populate modal with basic info
                document.getElementById('viewOrderId').textContent = data.prescription.order_id;
                document.getElementById('viewPatientName').textContent = data.prescription.patient_name;
                document.getElementById('viewDoctorName').textContent = data.prescription.staff_name;
                document.getElementById('viewOrderDate').textContent = data.prescription.order_date;
                
                // Set status with badge
                const statusElement = document.getElementById('viewStatus');
                statusElement.innerHTML = `<span class="badge bg-${getStatusColor(data.prescription.status_id)}">${data.prescription.status_id}</span>`;
                
                // Populate medicine list
                const medicineList = document.getElementById('viewMedicineList');
                medicineList.innerHTML = '';
                
                if (data.medicines && data.medicines.length > 0) {
                    data.medicines.forEach(med => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${med.medicine_name}</td>
                            <td>${med.medicine_quantity}</td>
                        `;
                        medicineList.appendChild(row);
                    });
                } else {
                    medicineList.innerHTML = '<tr><td colspan="2" class="text-center">No medicines found</td></tr>';
                }
                
                // Show the modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewPrescriptionModal'));
                viewModal.show();
                
            } else {
                alert('Error loading prescription details: ' + data.msg);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error loading prescription details');
        });
}

}


// Global variables
let editMode = false;
let currentMedicineId = '';

// Load medicines on page load
document.addEventListener('DOMContentLoaded', () => {
    loadMedicines();

    // Search input listener
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => loadMedicines(e.target.value));
    }
});

function loadMedicines(search = '') {
    fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'list_medicines', search: search })
    })
    .then(r => r.json())
    .then(d => {
        const tbody = document.querySelector('tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!d.success || !d.medicines || d.medicines.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No medicines found</td></tr>";
            updateMedicinesDashboardStats([]); // Update dashboard with empty data
            return;
        }

        // Display medicines in table
        d.medicines.forEach(m => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${m.medicine_id}</td>
                <td>${m.medicine_name}</td>
                <td>${parseFloat(m.medicine_price).toFixed(2)}</td>
                <td>${m.medicine_quantity}</td>
                <td>${m.medicine_description || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick="openEditModal('${m.medicine_id}', '${m.medicine_name}', ${m.medicine_price}, ${m.medicine_quantity}, '${m.medicine_description || ''}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMedicine('${m.medicine_id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Update medicines dashboard statistics
        updateMedicinesDashboardStats(d.medicines);
    })
    .catch(error => {
        console.error('Error loading medicines:', error);
        const tbody = document.querySelector('tbody');
        if (tbody) tbody.innerHTML = "<tr><td colspan='6' class='text-center text-danger'>Error loading data</td></tr>";
        updateMedicinesDashboardStats([]); // Update dashboard with empty data on error
    });
}

// Add this function to calculate and update medicines dashboard statistics
function updateMedicinesDashboardStats(medicines) {
    if (!medicines || medicines.length === 0) {
        // Set all stats to zero if no medicines
        document.getElementById('totalMedicines').textContent = '0';
        document.getElementById('lowStockItems').textContent = '0';
        document.getElementById('outOfStock').textContent = '0';
        document.getElementById('totalValue').textContent = '$0';
        return;
    }

    // Calculate statistics
    const totalMedicines = medicines.length;
    
    // Count low stock items (quantity less than 10 but greater than 0)
    const lowStockItems = medicines.filter(m => {
        const qty = parseInt(m.medicine_quantity);
        return qty > 0 && qty < 10;
    }).length;
    
    // Count out of stock items (quantity = 0)
    const outOfStock = medicines.filter(m => parseInt(m.medicine_quantity) === 0).length;
    
    // Calculate total inventory value
    const totalValue = medicines.reduce((sum, m) => {
        return sum + (parseFloat(m.medicine_price) * parseInt(m.medicine_quantity));
    }, 0);

    // Update the dashboard elements
    document.getElementById('totalMedicines').textContent = totalMedicines;
    document.getElementById('lowStockItems').textContent = lowStockItems;
    document.getElementById('outOfStock').textContent = outOfStock;
    document.getElementById('totalValue').textContent = '$' + totalValue.toFixed(2);
}

function openAddModal() {
    editMode = false;
    currentMedicineId = '';
    document.getElementById('medicineForm').reset();
    document.getElementById('medicineModalLabel').textContent = 'Add New Medicine';
    new bootstrap.Modal(document.getElementById('medicineModal')).show();
}

function openEditModal(id, name, price, qty, desc) {
    editMode = true;
    currentMedicineId = id;

    document.getElementById('medicine_name').value = name;
    document.getElementById('medicine_price').value = price;
    document.getElementById('medicine_quantity').value = qty;
    document.getElementById('medicine_description').value = desc;

    document.getElementById('medicineModalLabel').textContent = 'Edit Medicine';
    new bootstrap.Modal(document.getElementById('medicineModal')).show();
}

function saveMedicine() {
    const form = document.getElementById('medicineForm');
    if (!validateForm(form)) return;

    const data = {
        action: editMode ? 'update_medicine' : 'add_medicine',
        id: currentMedicineId,
        name: form.medicine_name.value,
        price: form.medicine_price.value,
        quantity: form.medicine_quantity.value,
        description: form.medicine_description.value
    };

    fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.msg);
            bootstrap.Modal.getInstance(document.getElementById('medicineModal')).hide();
            loadMedicines(); // This will also update the dashboard
        } else {
            alert('Error: ' + d.msg);
        }
    })
    .catch(error => console.error('Error saving medicine:', error));
}

function deleteMedicine(id) {
    if (!confirm('Are you sure you want to delete this medicine?')) return;

    fetch('/WebDev-Pharmacist-Management-System/app/controller/PharmacistController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete_medicine', id: id })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.msg);
            loadMedicines(); // This will also update the dashboard
        } else {
            alert('Error: ' + d.msg);
        }
    })
    .catch(error => console.error('Error deleting medicine:', error));
}

function validateForm(form) {
    const name = form.medicine_name.value.trim();
    const price = parseFloat(form.medicine_price.value);
    const quantity = parseInt(form.medicine_quantity.value);

    if (name === '') { alert('Name cannot be empty'); return false; }
    if (isNaN(price) || price <= 0) { alert('Price must be a positive number'); return false; }
    if (isNaN(quantity) || quantity < 0) { alert('Quantity cannot be negative'); return false; }

    return true;
}
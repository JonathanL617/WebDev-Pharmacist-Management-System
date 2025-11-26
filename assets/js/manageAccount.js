function createAdminAccount() {
    const form = document.getElementById('createAdminForm');
    
    // First, get a generated admin ID
    fetch('../../app/controller/SuperAdminController.php?action=generateAdminId')
        .then(r => r.json())
        .then(idData => {
            const formData = new FormData(form);
            formData.append('admin_id', idData.id);
            
            return fetch('../../app/controller/SuperAdminController.php?action=create', {
                method: 'POST',
                body: formData
            });
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                loadAdminAccounts();
                updateCounters();
                bootstrap.Modal.getInstance(document.getElementById('createAdminModal')).hide();
                form.reset();
                alert('Admin account created successfully!');
            } else {
                alert(res.message || 'Failed to create admin account');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred while creating the admin account');
        });
}


function loadAdminAccounts() {
    fetch('../../app/controller/SuperAdminController.php?action=getAdmin')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('adminAccountsTable');
            tableBody.innerHTML = '';

            data.forEach(admin => {
                tableBody.innerHTML += `
                <tr>
                    <td><input type="checkbox"></td>
                    <td>${admin.id}</td>
                    <td>${admin.username}</td>
                    <td>${admin.email}</td>
                    <td>${admin.dob}</td>
                    <td>${admin.registered_by}</td>
                    <td>
                        <span class="badge bg-${admin.status === 'active' ? 'success' : 'danger'}">
                            ${admin.status}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-${admin.status === 'active' ? 'warning' : 'success'}"
                                onclick="toggleAdminStatus('${admin.id}', '${admin.status}')">
                            ${admin.status === 'active' ? 'Deactivate' : 'Activate'}
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteAdmin('${admin.id}')">
                            Delete
                        </button>
                    </td>
                </tr>
            `;
            });
        });
}

function toggleAdminStatus(adminId, currentStatus) {
    fetch('../../app/controller/SuperAdminController.php?action=toggleStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            adminId: adminId,
            status: currentStatus === 'active' ? 'inactive' : 'active'
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAdminAccounts();
                updateCounters();
            }
        });
}

//counter function
function updateCounters() {
    fetch(`../../app/controller/SuperAdminController.php`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalAdmins').textContent = data.total;
            document.getElementById('activeAdmins').textContent = data.active;
            document.getElementById('inActiveAdmins').textContent = data.inactive;
            document.getElementById('registeredByCurrent').textContent = data.registered_by_current;
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadAdminAccounts();
    updateCounters();

    // Refresh every 30 seconds
    setInterval(() => {
        loadAdminAccounts();
        updateCounters();
    }, 30000);
});
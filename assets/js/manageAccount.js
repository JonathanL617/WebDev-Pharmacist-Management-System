function createAdminAccount(){
    const form = document.getElementById('createAdminForm');
    const formData = new FormData(form);

    fetch('../../app/controller/SuperAdminController.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            loadAdminAccounts();

            const modal = document.getElementById('createAdminModal');
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            bootstrapModal.hide();
            form.reset();
        }
        else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the account');
    });
}


function loadAdminAccounts(){
    fetch('../../app/controller/SuperAdminController.php?action=getAdmin')
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById('adminAccountsTable');
        tableBody.innerHTML = '';

        data.forEach(admin => {
            tableBody.innerHTML += `
                <tr>
                    <td>${admin.id}</td>
                    <td>${admin.username}</td>
                    <td>${admin.email}</td>
                    <td>
                        <span class="badge bg-${admin.status === 'active' ? 'success' : 'danger'}">
                            ${admin.status}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-${admin.status === 'active' ? 'warning' : 'success'}"
                                onclick="toggleAdminStatus(${admin.id}, '${admin.status}')">
                            ${admin.status === 'active' ? 'Deactivate' : 'Activate'}
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteAdmin(${admin.id})">
                            Delete
                        </button>
                    </td>
                </tr>
            `;
        });
    });
}

function toggleAdminStatus(adminId, currentStatus){
    fetch('../../app/controller/SuperAdminController.php?action=toggleStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            adminId: adminId,
            status: currentStatus === 'active' ? 'inactive':'active'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            loadAdminAccounts();
        }
    });
}

//counter function
window.refreshData = function(){
    function updateCounters(){
        fetch(`{$BASE_URL}/app/controller/SuperAdminController.php`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalAdmins').textContent       = data.total;
            document.getElementById('activeAdmins').textContent      = data.active;
            document.getElementById('inactiveAdmins').textContent    = data.inactive;
            document.getElementById('registeredByCurrent').textContent = data.registered_by_current;
        })
        .catch(err => console.error('Stats error: ', err))
    }
}
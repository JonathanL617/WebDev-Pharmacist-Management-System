function createAdminAccount(){
    const form = document.getElementById('createAdminForm');
    const formData = new FormData(form);

    fetch('include/create_admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            loadAdminAccounts();
            $('#CreateAdminModal').modal('hide');
            form.reset();
        }
        else {
            alert(data.message);
        }
    })
}


function loadAdminAccounts(){
    fetch('include/fetch_admins.php')
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
    fetch('include/toggle_admin_status.php', {
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
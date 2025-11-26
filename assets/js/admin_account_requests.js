document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('staffRequestForm');
    const roleSelect = document.getElementById('staff_role');
    const staffIdInput = document.getElementById('staff_id');
    const registeredBySelect = document.getElementById('registered_by');

    // Load current admin ID into registered_by field
    // Since we have loggedInUserId global variable from dashboard.php
    if (typeof loggedInUserId !== 'undefined') {
        registeredBySelect.innerHTML = `<option value="${loggedInUserId}" selected>${loggedInUserId}</option>`;
    }

    // Generate ID when role changes
    roleSelect.addEventListener('change', function () {
        const role = this.value;
        if (role) {
            fetch(`../../app/controller/AdminController.php?action=generateStaffId&role=${role}`)
                .then(response => response.json())
                .then(data => {
                    if (data.id) {
                        staffIdInput.value = data.id;
                    }
                })
                .catch(error => console.error('Error generating ID:', error));
        }
    });

    // Handle form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Add registered_by if not in form data (though it is in the select)
        if (!data.registered_by && typeof loggedInUserId !== 'undefined') {
            data.registered_by = loggedInUserId;
        }

        fetch('../../app/controller/AdminController.php?action=createStaffRequest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Staff request submitted successfully!');
                    form.reset();
                    // Reset ID field
                    staffIdInput.value = '';
                    // Restore registered_by
                    if (typeof loggedInUserId !== 'undefined') {
                        registeredBySelect.innerHTML = `<option value="${loggedInUserId}" selected>${loggedInUserId}</option>`;
                    }

                    // Update stats if needed (optional, requires another fetch)
                } else {
                    alert('Error: ' + (result.message || result.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the request.');
            });
    });
});

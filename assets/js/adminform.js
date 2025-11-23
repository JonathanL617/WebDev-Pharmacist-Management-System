// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {
    const staffRoleSelect = document.getElementById('staff_role');
    const staffIdInput = document.getElementById('staff_id');
    const registeredBySelect = document.getElementById('registered_by');
    const form = document.getElementById('staffRequestForm');

    // Function to fetch admin IDs and populate the registered_by dropdown
    async function populateAdminIds() {
        try {
            const response = await fetch('/api/admins');
            if (!response.ok) throw new Error('Failed to fetch admin IDs');
            const adminIds = await response.json(); // Expecting array like ["ADM001", "ADM002", "ADM003"]

            registeredBySelect.innerHTML = '<option value="" disabled selected>Select Admin ID</option>';
            adminIds.forEach(id => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = id;
                registeredBySelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching admin IDs:', error);
            registeredBySelect.innerHTML = '<option value="" disabled selected>Error loading Admin IDs</option>';
        }
    }

    // Function to generate unique staff ID based on role
    async function generateStaffId(role) {
        try {
            const response = await fetch(`/api/next-staff-id?role=${role}`);
            if (!response.ok) throw new Error('Failed to generate staff ID');
            const data = await response.json(); // Expecting { staffId: "D001" } or { staffId: "P001" }
            staffIdInput.value = data.staffId;
        } catch (error) {
            console.error('Error generating staff ID:', error);
            staffIdInput.value = 'Error generating ID';
        }
    }

    // Populate admin IDs on page load
    populateAdminIds();

    // Generate staff ID when role is selected
    if (staffRoleSelect) {
        staffRoleSelect.addEventListener('change', () => {
            const role = staffRoleSelect.value;
            if (role === 'doctor' || role === 'pharmacist') {
                generateStaffId(role);
            } else {
                staffIdInput.value = '';
            }
        });
    }
});
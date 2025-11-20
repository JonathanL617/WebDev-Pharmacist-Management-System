// assets/js/manage_users.js
document.addEventListener('DOMContentLoaded', () => {
    const staffTableBody = document.getElementById('staffTable');
    const patientTableBody = document.getElementById('patientTable');
    const staffSearch = document.getElementById('staffSearch');
    const patientSearch = document.getElementById('patientSearch');
    const editStaffForm = document.getElementById('editStaffForm');
    const editPatientForm = document.getElementById('editPatientForm');
    const staffFormErrors = document.getElementById('staffFormErrors');
    const patientFormErrors = document.getElementById('patientFormErrors');

    // Validation patterns
    const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
    const phonePattern = /^[0-9]{10,15}$/;
    const namePattern = /^[a-zA-Z\s]{1,100}$/;
    const specializationPattern = /^[a-zA-Z\s]{1,100}$/;
    const addressPattern = /^.{1,255}$/;
    const dobPattern = /^\d{4}-\d{2}-\d{2}$/;

    // Cache for search filtering
    let staffDataCache = [];
    let patientDataCache = [];

    // Populate Staff table
    function populateStaffTable(data) {
        staffTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            staffTableBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No staff found.</td></tr>';
            return;
        }
        data.forEach(staff => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${staff.staff_id}</td>
                <td>${staff.staff_name}</td>
                <td>${staff.staff_dob}</td>
                <td>${staff.staff_specialization}</td>
                <td>${staff.staff_role}</td>
                <td>${staff.staff_phone}</td>
                <td>${staff.staff_email}</td>
                <td>${staff.registered_by}</td>
                <td>${staff.staff_status}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editStaffModal" data-id="${staff.staff_id}">Edit</button>
                </td>
            `;
            staffTableBody.appendChild(row);
        });
    }

    // Populate Patient table
    function populatePatientTable(data) {
        patientTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            patientTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No patients found.</td></tr>';
            return;
        }
        data.forEach(patient => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${patient.patient_id}</td>
                <td>${patient.patient_name}</td>
                <td>${patient.patient_date_of_birth}</td>
                <td>${patient.patient_phone}</td>
                <td>${patient.patient_gender}</td>
                <td>${patient.patient_email}</td>
                <td>${patient.patient_address}</td>
                <td>${patient.registered_by}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editPatientModal" data-id="${patient.patient_id}">Edit</button>
                </td>
            `;
            patientTableBody.appendChild(row);
        });
    }

    // Fetch and populate tables
    function loadTables() {
        // Fetch staff data
        fetch('/api/staff')
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch staff');
                return response.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);
                staffDataCache = data;
                populateStaffTable(data);
            })
            .catch(error => {
                console.error('Error fetching staff:', error);
                staffTableBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Error loading staff.</td></tr>';
            });

        // Fetch patient data
        fetch('/api/patients')
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch patients');
                return response.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);
                patientDataCache = data;
                populatePatientTable(data);
            })
            .catch(error => {
                console.error('Error fetching patients:', error);
                patientTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Error loading patients.</td></tr>';
            });
    }

    // Search functionality for Staff
    staffSearch.addEventListener('input', () => {
        const query = staffSearch.value.toLowerCase();
        const filteredData = staffDataCache.filter(staff =>
            staff.staff_id.toLowerCase().includes(query) ||
            staff.staff_name.toLowerCase().includes(query)
        );
        populateStaffTable(filteredData);
    });

    // Search functionality for Patients
    patientSearch.addEventListener('input', () => {
        const query = patientSearch.value.toLowerCase();
        const filteredData = patientDataCache.filter(patient =>
            patient.patient_id.toLowerCase().includes(query) ||
            patient.patient_name.toLowerCase().includes(query)
        );
        populatePatientTable(filteredData);
    });

    // Handle Edit button clicks
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('edit-btn')) {
            const id = e.target.getAttribute('data-id');
            const modal = e.target.getAttribute('data-bs-target');

            if (modal === '#editStaffModal') {
                fetch(`/api/staff/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to fetch staff');
                        return response.json();
                    })
                    .then(staff => {
                        if (staff.error) throw new Error(staff.error);
                        document.getElementById('edit_staff_id').value = staff.staff_id;
                        document.getElementById('edit_staff_name').value = staff.staff_name;
                        document.getElementById('edit_staff_dob').value = staff.staff_dob;
                        document.getElementById('edit_staff_specialization').value = staff.staff_specialization;
                        document.getElementById('edit_staff_role').value = staff.staff_role;
                        document.getElementById('edit_staff_phone').value = staff.staff_phone;
                        document.getElementById('edit_staff_email').value = staff.staff_email;
                        document.getElementById('edit_staff_registered_by').value = staff.registered_by;
                        document.getElementById('edit_staff_status').value = staff.staff_status;
                        staffFormErrors.style.display = 'none';
                        staffFormErrors.innerHTML = '';
                    })
                    .catch(error => {
                        staffFormErrors.style.display = 'block';
                        staffFormErrors.innerHTML = 'Error loading staff data: ' + error.message;
                    });
            } else if (modal === '#editPatientModal') {
                fetch(`/api/patients/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to fetch patient');
                        return response.json();
                    })
                    .then(patient => {
                        if (patient.error) throw new Error(patient.error);
                        document.getElementById('edit_patient_id').value = patient.patient_id;
                        document.getElementById('edit_patient_name').value = patient.patient_name;
                        document.getElementById('edit_patient_dob').value = patient.patient_date_of_birth;
                        document.getElementById('edit_patient_phone').value = patient.patient_phone;
                        document.getElementById('edit_patient_gender').value = patient.patient_gender;
                        document.getElementById('edit_patient_email').value = patient.patient_email;
                        document.getElementById('edit_patient_address').value = patient.patient_address;
                        document.getElementById('edit_patient_registered_by').value = patient.registered_by;
                        patientFormErrors.style.display = 'none';
                        patientFormErrors.innerHTML = '';
                    })
                    .catch(error => {
                        patientFormErrors.style.display = 'block';
                        patientFormErrors.innerHTML = 'Error loading patient data: ' + error.message;
                    });
            }
        }
    });

    // Validate and submit Staff form
    editStaffForm.addEventListener('submit', (e) => {
        e.preventDefault();
        let errors = [];

        const staffName = document.getElementById('edit_staff_name').value;
        const staffDob = document.getElementById('edit_staff_dob').value;
        const staffSpecialization = document.getElementById('edit_staff_specialization').value;
        const staffRole = document.getElementById('edit_staff_role').value;
        const staffPhone = document.getElementById('edit_staff_phone').value;
        const staffEmail = document.getElementById('edit_staff_email').value;
        const staffStatus = document.getElementById('edit_staff_status').value;

        if (!namePattern.test(staffName)) {
            errors.push('Name must be 1-100 characters and contain only letters and spaces.');
        }
        if (!dobPattern.test(staffDob)) {
            errors.push('Invalid date of birth format (YYYY-MM-DD).');
        }
        if (!specializationPattern.test(staffSpecialization)) {
            errors.push('Specialization must be 1-100 characters and contain only letters and spaces.');
        }
        if (!['doctor', 'pharmacist'].includes(staffRole)) {
            errors.push('Please select a valid role.');
        }
        if (!phonePattern.test(staffPhone)) {
            errors.push('Phone must be 10-15 digits.');
        }
        if (!emailPattern.test(staffEmail)) {
            errors.push('Invalid email format.');
        }
        if (!['active', 'inactive', 'blocked'].includes(staffStatus)) {
            errors.push('Please select a valid status.');
        }

        if (errors.length > 0) {
            staffFormErrors.style.display = 'block';
            staffFormErrors.innerHTML = errors.join('<br>');
            return;
        }

        fetch('/api/staff/update', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                staff_id: document.getElementById('edit_staff_id').value,
                staff_name: staffName,
                staff_dob: staffDob,
                staff_specialization: staffSpecialization,
                staff_role: staffRole,
                staff_phone: staffPhone,
                staff_email: staffEmail,
                staff_status: staffStatus
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to update staff');
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            alert('Staff updated successfully.');
            bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
            loadTables();
        })
        .catch(error => {
            staffFormErrors.style.display = 'block';
            staffFormErrors.innerHTML = 'Error updating staff: ' + error.message;
        });
    });

    // Validate and submit Patient form
    editPatientForm.addEventListener('submit', (e) => {
        e.preventDefault();
        let errors = [];

        const patientName = document.getElementById('edit_patient_name').value;
        const patientDob = document.getElementById('edit_patient_dob').value;
        const patientPhone = document.getElementById('edit_patient_phone').value;
        const patientGender = document.getElementById('edit_patient_gender').value;
        const patientEmail = document.getElementById('edit_patient_email').value;
        const patientAddress = document.getElementById('edit_patient_address').value;

        if (!namePattern.test(patientName)) {
            errors.push('Name must be 1-100 characters and contain only letters and spaces.');
        }
        if (!dobPattern.test(patientDob)) {
            errors.push('Invalid date of birth format (YYYY-MM-DD).');
        }
        if (!phonePattern.test(patientPhone)) {
            errors.push('Phone must be 10-15 digits.');
        }
        if (!['Male', 'Female', 'Other'].includes(patientGender)) {
            errors.push('Please select a valid gender.');
        }
        if (!emailPattern.test(patientEmail)) {
            errors.push('Invalid email format.');
        }
        if (!addressPattern.test(patientAddress)) {
            errors.push('Address must be 1-255 characters.');
        }

        if (errors.length > 0) {
            patientFormErrors.style.display = 'block';
            patientFormErrors.innerHTML = errors.join('<br>');
            return;
        }

        fetch('/api/patients/update', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                patient_id: document.getElementById('edit_patient_id').value,
                patient_name: patientName,
                patient_date_of_birth: patientDob,
                patient_phone: patientPhone,
                patient_gender: patientGender,
                patient_email: patientEmail,
                patient_address: patientAddress
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to update patient');
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            alert('Patient updated successfully.');
            bootstrap.Modal.getInstance(document.getElementById('editPatientModal')).hide();
            loadTables();
        })
        .catch(error => {
            patientFormErrors.style.display = 'block';
            patientFormErrors.innerHTML = 'Error updating patient: ' + error.message;
        });
    });

    // Initialize tables
    loadTables();
});
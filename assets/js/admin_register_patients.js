// assets/js/register_patients.js
document.addEventListener('DOMContentLoaded', () => {
    const patientsTableBody = document.getElementById('patientsTable');
    const patientForm = document.getElementById('patientRegisterForm');
    const patientIdInput = document.getElementById('patient_id');
    const formErrors = document.getElementById('formErrors');

    // Fetch and display all patients
    function loadPatients() {
        if (!patientsTableBody) return;

        fetch('../../app/controller/AdminController.php?action=getPatients')
            .then(res => res.json())
            .then(data => {
                patientsTableBody.innerHTML = '';
                if (!data || data.length === 0) {
                    patientsTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No patients found.</td></tr>';
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
                    `;
                    patientsTableBody.appendChild(row);
                });
            })
            .catch(err => {
                console.error('Load patients error:', err);
                patientsTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading patients.</td></tr>';
            });
    }

    // Generate new patient ID
    function generatePatientId() {
        if (!patientIdInput) return;
        fetch('../../app/controller/AdminController.php?action=generatePatientId')
            .then(res => res.json())
            .then(data => { patientIdInput.value = data.id || 'Error'; })
            .catch(() => { patientIdInput.value = 'Error generating ID'; });
    }

    // Initial load
    loadPatients();
    generatePatientId();

    // Form submission
    if (patientForm) {
        patientForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = {
                patient_id: patientIdInput.value,
                patient_name: document.getElementById('patient_name').value.trim(),
                patient_date_of_birth: document.getElementById('patient_date_of_birth').value,
                patient_phone: document.getElementById('patient_phone').value.trim(),
                patient_gender: document.getElementById('patient_gender').value,
                patient_email: document.getElementById('patient_email').value.trim(),
                patient_address: document.getElementById('patient_address').value.trim(),
                registered_by: document.getElementById('registered_by').value
            };

            // Validation
            const errors = [];

            if (!formData.patient_id || formData.patient_id.includes('Error')) {
                errors.push('Invalid Patient ID.');
            }
            if (!formData.patient_name || formData.patient_name.length > 100) {
                errors.push('Name is required and must be ≤ 100 characters.');
            }
            if (!formData.patient_date_of_birth) {
                errors.push('Date of Birth is required.');
            } else {
                const dob = new Date(formData.patient_date_of_birth);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Real today
                if (dob > today) {
                    errors.push('Date of Birth cannot be in the future.');
                }
            }
            if (!formData.patient_phone || !/^\+?[\d\s-]{7,15}$/.test(formData.patient_phone)) {
                errors.push('Valid phone number required (7-15 digits).');
            }
            if (!['Male', 'Female', 'Other'].includes(formData.patient_gender)) {
                errors.push('Please select a valid gender.');
            }
            if (!formData.patient_email || !/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(formData.patient_email)) {
                errors.push('Valid email required.');
            }
            if (!formData.patient_address || formData.patient_address.length > 255) {
                errors.push('Address is required and must be ≤ 255 characters.');
            }
            if (formData.registered_by !== 'SA001') {
                errors.push('Registered by must be SA001.');
            }

            if (errors.length > 0) {
                // Assuming there is an error container, if not create alert
                // The original code had formErrors, let's assume it exists or use alert
                if (formErrors) {
                    formErrors.style.display = 'block';
                    formErrors.innerHTML = errors.join('<br>');
                } else {
                    alert(errors.join('\n'));
                }
                return;
            }

            if (formErrors) formErrors.style.display = 'none';

            // REAL SUBMISSION
            fetch('../../app/controller/AdminController.php?action=registerPatient', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(res => {
                    if (!res.ok) throw new Error('Server error');
                    return res.json();
                })
                .then(result => {
                    if (result.success) {
                        alert(`Patient ${formData.patient_id} registered successfully!`);

                        // Refresh everything
                        loadPatients();
                        patientForm.reset();
                        generatePatientId();
                        document.getElementById('registered_by').value = 'SA001';

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('registerPatientModal'));
                        if (modal) modal.hide();
                    } else {
                        throw new Error(result.error || 'Registration failed');
                    }
                })
                .catch(err => {
                    if (formErrors) {
                        formErrors.style.display = 'block';
                        formErrors.innerHTML = 'Error: ' + err.message;
                    } else {
                        alert('Error: ' + err.message);
                    }
                });
        });
    }
});
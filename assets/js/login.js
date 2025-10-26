function showLogin() {
    document.getElementById('loginCard').style.display = 'block';
    document.getElementById('resetPasswordCard').style.display = 'none';
}

function showResetPassword(){
    document.getElementById('loginCard').style.display = 'none';
    document.getElementById('resetPasswordCard').style.display = 'block';
}

function showSuccess() {
    document.getElementById('successCard').style.display = 'block';
    document.getElementById('resetPasswordCard').style.display = 'none';
}

function handleLogin(event) {
    event.preventDefault();
    const form = document.getElementById('login-form');
    const formData = new FormData(form);
    fetch('', {  // Self-post
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse JSON response
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect; // Use redirect URL from server
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorDiv = document.createElement('div'); 
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = 'An error occurred. Please try again.';
        document.getElementById('loginCard').insertBefore(errorDiv, document.getElementById('login-form'));
        setTimeout(() => errorDiv.remove(), 2000);
    });
}
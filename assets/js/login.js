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
function openTab(tabId, clickedTab){
    //update url with new page
    window.location.href = `dashboard.php?page=${tabId}`;
}

document.addEventListener('DOMContentLoaded', function(){
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page');
    const tabs = document.getElementsByClassName('tab');

    // Default page based on role
    const roleDefaults = {
        'superadmin': 'manage_accounts',
        'admin': 'manage_users',
        'pharmacist': 'stock_management',
        'doctor': 'patient_records'
    };

    const activePage = currentPage || roleDefaults[userRole];

    for (let tab of tabs) {
        const tabPage = tab.getAttribute('data-page');
        if (tabPage === activePage) {
            tab.classList.add('active');
            break; // Exit loop once active tab is found
        } else {
            tab.classList.remove('active');
        }
    }

    // Update URL with default page if not present
    if (!currentPage) {
        window.history.replaceState({}, document.title, window.location.pathname + '?page=' + activePage);
    }
});


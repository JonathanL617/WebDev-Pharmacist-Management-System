const REFRESH_INTERVAL = 5000;

let refreshTimer = null;

function startAutoRefresh(){
    if(typeof window.refreshData === 'function'){
        window.refreshData
        refreshTimer = setInterval(window.refreshData, REFRESH_INTERVAL);
    }
}

function stopAutoRefresh(){
    if(refreshTimer){
        clearInterval(refreshTimer);
    }
}

document.addEventListener('DOMContentLoaded', startAutoRefresh);

document.addEventListener('visibilitychange', () => {
    if(document.hidden){
        stopAutoRefresh();
    }
    else {
        startAutoRefresh();
    }
})
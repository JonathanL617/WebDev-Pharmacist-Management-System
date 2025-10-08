function openTab(tabId, clickedTab){
    //hide all tab content
    const tabContent = document.getElementsByClassName('tab-content');
    for(let content of tabContent){
        content.style.display = 'none';
    }

    //remove active class from all tabs
    const tabs = document.getElementsByClassName('tab');
    for(let tab of tabs){
        tab.classList.remove('active');
    }

    //show selected tab content and activate tab
    document.getElementById(tabId).style.display = 'block';
    clickedTab.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function(){
    const defaultTab = document.querySelector('.tab');

    if(defaultTab){
        defaultTab.click();
    }
});


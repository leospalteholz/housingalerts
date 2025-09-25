// Calendar Button Functionality
// Include this script on any page that uses the calendar-button component

document.addEventListener('DOMContentLoaded', function() {
    initializeCalendarButtons();
    setupAutoInitialization();
});

function setupAutoInitialization() {
    // Create a MutationObserver to watch for new calendar buttons
    const observer = new MutationObserver(function(mutations) {
        let shouldReinit = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node contains calendar buttons
                        if (node.querySelector && node.querySelector('.calendar-menu-button')) {
                            shouldReinit = true;
                        }
                        // Check if the added node itself is a calendar button
                        if (node.classList && node.classList.contains('calendar-menu-button')) {
                            shouldReinit = true;
                        }
                    }
                });
            }
        });
        
        if (shouldReinit) {
            console.log('New calendar buttons detected, re-initializing...');
            initializeCalendarButtons();
        }
    });
    
    // Start observing the entire document for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

function initializeCalendarButtons() {
    const calendarButtons = document.querySelectorAll('.calendar-menu-button');
    
    calendarButtons.forEach(button => {
        // Remove any existing event listeners to prevent duplicates
        button.removeEventListener('click', handleCalendarButtonClick);
        button.addEventListener('click', handleCalendarButtonClick);
    });
    
    // Close dropdowns when clicking outside
    document.removeEventListener('click', handleOutsideClick);
    document.addEventListener('click', handleOutsideClick);
}

function handleCalendarButtonClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const hearingId = this.dataset.hearingId;
    const dropdown = document.querySelector(`.calendar-dropdown[data-hearing-id="${hearingId}"]`);
    
    if (!dropdown) {
        console.warn('Calendar dropdown not found for hearing ID:', hearingId);
        return;
    }
    
    // Close all other dropdowns
    document.querySelectorAll('.calendar-dropdown').forEach(dd => {
        if (dd !== dropdown) {
            dd.classList.add('hidden');
        }
    });
    
    // Toggle this dropdown
    dropdown.classList.toggle('hidden');
}

function handleOutsideClick(event) {
    const isCalendarButton = event.target.closest('.calendar-menu-button');
    const isCalendarDropdown = event.target.closest('.calendar-dropdown');
    
    if (!isCalendarButton && !isCalendarDropdown) {
        document.querySelectorAll('.calendar-dropdown').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
}

// Export functions for use in other scripts if needed
window.CalendarButton = {
    initialize: initializeCalendarButtons,
    handleButtonClick: handleCalendarButtonClick,
    handleOutsideClick: handleOutsideClick
};

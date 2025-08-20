
/*window.fp = flatpickr("#date-range", {
    mode: "range",
    dateFormat: "Y-m-d"
});*/

function initializeDateRangePicker(selector, daysLimit) {
    let firstSelectedDate = null;
    let currentStartDate = null;

    window.fp = flatpickr(selector, {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates) {
            if(daysLimit!=null&&daysLimit!="")
            {
                if (selectedDates.length === 1) {
                // Set new start date and update constraints
                    currentStartDate = selectedDates[0];
                firstSelectedDate = currentStartDate; // Reset the first selected date
            } else if (selectedDates.length === 2) {
                // If two dates are selected
                const [startDate, endDate] = selectedDates;
                const differenceInDays = Math.floor((endDate - startDate) / (24 * 60 * 60 * 1000));

                if (differenceInDays > daysLimit) {
                    // Adjust end date if the difference exceeds daysLimit
                    fp.setDate([startDate, new Date(startDate.getTime() + (daysLimit * 24 * 60 * 60 * 1000))], true);
                }
                
                // Update the current start date
                currentStartDate = startDate;
            }
            
            // Calculate the min and max dates based on the current start date
            if (currentStartDate) {
                const minDate = new Date(currentStartDate.getTime() - (daysLimit * 24 * 60 * 60 * 1000));
                const maxDate = new Date(currentStartDate.getTime() + (daysLimit * 24 * 60 * 60 * 1000));
                fp.set('minDate', minDate);
                fp.set('maxDate', maxDate);
            }
        }
    },
    onReady: function() {
            // Create and add the clear button when flatpickr is ready
        const calendarContainer = document.querySelector('.flatpickr-calendar');
        if (calendarContainer && !document.querySelector('.clear-button')) {
            const clearButton = document.createElement('button');
            clearButton.type = 'button';
            clearButton.textContent = 'Clear';
            clearButton.className = 'clear-button';
            clearButton.addEventListener('click', function() {
                    fp.clear(); // Clear the selected dates
                    firstSelectedDate = null; // Reset the first selected date
                    currentStartDate = null; // Reset the current start date
                    resetConstraints(); // Reset constraints
                });
            calendarContainer.appendChild(clearButton);
        }
    }
});



    function resetConstraints() {
        // Reset min and max date constraints
        fp.set('minDate', null);
        fp.set('maxDate', null);
    }

}


// Export the function if using a module system
if (typeof module !== 'undefined' && module.exports) {
    module.exports = initializeDateRangePicker;
}



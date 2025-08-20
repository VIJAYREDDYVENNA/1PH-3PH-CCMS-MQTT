
// Initialize time picker dropdowns
function initializeTimePicker() {
    const hoursOptions = Array.from({
        length: 24
    }, (_, i) =>
        i.toString().padStart(2, '0')
    );

    const minutesOptions = Array.from({
        length: 60
    }, (_, i) =>
        i.toString().padStart(2, '0')
    );

    // Initialize hours dropdowns
    ['fromHours', 'toHours'].forEach(id => {
        const select = document.getElementById(id);
        hoursOptions.forEach(hour => {
            const option = new Option(`${hour}h`, hour);
            select.add(option);
        });
    });

    // Initialize minutes dropdowns
    ['fromMinutes', 'toMinutes'].forEach(id => {
        const select = document.getElementById(id);
        minutesOptions.forEach(minute => {
            const option = new Option(`${minute}m`, minute);
            select.add(option);
        });
    });
}

// Get formatted time with seconds
function getFormattedTime(hourId, minuteId) {
    const hours = parseInt(document.getElementById(hourId).value, 10);
    const minutes = parseInt(document.getElementById(minuteId).value, 10);

    if (isNaN(hours) || isNaN(minutes)) return null;

    const paddedHours = hours.toString().padStart(2, '0');
    const paddedMinutes = minutes.toString().padStart(2, '0');

    return `${paddedHours}:${paddedMinutes}:00`;
}

// Handle form submission
// Handle form submission
function handleSubmit(event) {
    event.preventDefault();

    const fromDate = document.getElementById('fromDate').value?.trim();
    const toDate = document.getElementById('toDate').value?.trim();
    const fromTime = getFormattedTime('fromHours', 'fromMinutes');
    const toTime = getFormattedTime('toHours', 'toMinutes');

    const resultsContainer = document.getElementById('results-container');
    const statusElement = document.getElementById('status');
    const resultsContent = document.getElementById('results-content');

    // Reset display states
    resultsContainer.style.display = 'none';
    statusElement.innerHTML = '';
    resultsContent.style.display = 'none';

    try {
        // Validate all fields are filled
        if (!fromDate || !toDate || !fromTime || !toTime) {
            throw new Error('Please fill out all date and time fields.');
        }

        // Validate device ID
        const device_id = localStorage.getItem('SELECTED_ID') || document.getElementById('device_id')?.value?.trim();
        if (!device_id) {
            throw new Error('No device selected.');
        }

        // Validate date and time formats
        const fromDateTime = new Date(`${fromDate}T${fromTime}`);
        const toDateTime = new Date(`${toDate}T${toTime}`);

        if (isNaN(fromDateTime.getTime()) || isNaN(toDateTime.getTime())) {
            throw new Error('Invalid date or time format.');
        }

        if (fromDateTime >= toDateTime) {
            throw new Error("'From' datetime must be earlier than 'To' datetime.");
        }

        // Make AJAX request
        $.ajax({
            url: '../energy-consumption/code/fetch_consumption.php',
            type: 'POST',
            dataType: 'json',
            data: {
                energyconsumption: true,
                D_id: device_id,
                fromdate: fromDate,
                fromtime: fromTime,
                todate: toDate,
                totime: toTime
            },
            success: function (data) {
                resultsContainer.style.display = 'block';

                if (data.status === 'error') {
                    statusElement.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    resultsContent.style.display = 'none';
                    return;
                }

                if (data.status === 'success') {
                    statusElement.innerHTML = '';
                    resultsContent.style.display = 'block';

                    document.getElementById('requested-time-range').innerHTML = `
                        From: ${fromDate} ${fromTime}<br>
                        To: ${toDate} ${toTime}
                    `;

                    document.getElementById('actual-time-range').innerHTML = `
                        From: ${data.data.actual_from_time}<br>
                        To: ${data.data.actual_to_time}
                    `;

                    document.getElementById('kwh-value').textContent = parseFloat(data.data.diff_kwh).toFixed(2);
                    document.getElementById('kvah-value').textContent = parseFloat(data.data.diff_kvah).toFixed(2);
                } else {
                    statusElement.innerHTML = `<div class="alert alert-danger">${data.message || 'Unknown error occurred'}</div>`;
                    resultsContent.style.display = 'none';
                }
            },
            error: function (xhr, status, error) {
                resultsContainer.style.display = 'block';
                statusElement.innerHTML = `<div class="alert alert-danger">Error: ${xhr.status} - ${xhr.statusText || error}</div>`;
                resultsContent.style.display = 'none';
            }
        });

    } catch (error) {
        resultsContainer.style.display = 'block';
        statusElement.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        resultsContent.style.display = 'none';
    }
}



// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeTimePicker);

function applyTheme() {
    const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.setAttribute('data-theme', isDarkMode ? 'dark' : 'light');
}

// Apply theme on page load
applyTheme();

// Listen for theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);

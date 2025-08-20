// Define alert types and icons
const alertTypes = {
    'power-restored': {
        icon: 'fa-bolt',
        text: 'Power Restored'
    },
    'power-disconnected': {
        icon: 'fa-power-off',
        text: 'Power Disconnected'
    },
    'overload': {
        icon: 'fa-triangle-exclamation',
        text: 'Overload'
    },
    'drive-run': {
        icon: 'fa-play',
        text: 'Drive Run'
    }
};

// Get UI elements
let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

// Initialize toast notifications
const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);

// Motor selection handling
let device_id_list = document.getElementById('motor-list');
let device_id = device_id_list.value;

device_id_list.addEventListener('change', function() {
    device_id = device_id_list.value;
    // Save to localStorage
    localStorage.setItem('selected_motor_id', device_id);
    
    // Show loader
    document.getElementById('pre-loader').style.display = 'block';
    
    // Get date filter value
    const dateFilter = document.getElementById('dateFilter').value;
    
    // Fetch alerts with selected motor and date
    fetchAlerts(device_id, dateFilter);
});

// Date filter event listener
document.getElementById('dateFilter').addEventListener('change', function() {
    const selectedDate = this.value;
    const selectedMotor = document.getElementById('motor-list').value;
    
    // Show loader
    document.getElementById('pre-loader').style.display = 'block';
    
    // Fetch alerts with selected date
    fetchAlerts(selectedMotor, selectedDate);
});

// On page load
document.addEventListener('DOMContentLoaded', function() {
    // Set current date in date filter
    // const today = new Date();
    // const formattedDate = today.toISOString().split('T')[0];
    // document.getElementById('dateFilter').value = formattedDate;
    
    // Get saved motor ID from localStorage
    let saved_device_id = localStorage.getItem('selected_motor_id');
    
    if (saved_device_id) {
        // Try to set the saved value as selected
        let motorList = document.getElementById('motor-list');
        motorList.value = saved_device_id;
        
        // If the value doesn't exist in options (e.g., removed device), fallback
        if (motorList.value !== saved_device_id) {
            saved_device_id = motorList.options[0].value;
            motorList.value = saved_device_id;
        }
    } else {
        // Default to first one if nothing stored
        saved_device_id = document.getElementById('motor-list').value;
    }
    
    // Initial data load - empty date means today
    fetchAlerts(saved_device_id, "");
});

// Function to fetch alerts from the server
function fetchAlerts(deviceId, searchDate) {
    // Show loading state
    const alertsList = document.getElementById('alertsList');
    alertsList.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading alerts...</p>
        </div>
    `;
    
    // Send AJAX request to fetch alerts
    $.ajax({
        type: "POST",
        url: '../alerts/code/motor-alerts.php',
        data: { 
            D_ID: deviceId,
            DATE: searchDate 
        },
        success: function(response) {
            // Hide loader
            $("#pre-loader").css('display', 'none');
            
            if (response.status === 'success') {
                // Update alerts list with retrieved data
                updateAlertsList(response.alerts);
                
                // Show success message if needed
                if (response.alerts.length === 0) {
                    success_message_text.textContent = "No alerts found for the selected criteria";
                    success_toast.show();
                }
            } else {
                // Show error message
                error_message_text.textContent = response.message || "Error retrieving alerts";
                error_toast.show();
                
                // Show empty state
                alertsList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error loading alerts. Please try again.</p>
                    </div>
                `;
            }
        },
        error: function(xhr, status, error) {
            // Hide loader
            $("#pre-loader").css('display', 'none');
            
            // Show error message
            error_message_text.textContent = "Server error: " + error;
            error_toast.show();
            
            // Show empty state
            alertsList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error connecting to server. Please try again.</p>
                </div>
            `;
        }
    });
}

// Update the alerts list
function updateAlertsList(alerts) {
    const alertsList = document.getElementById('alertsList');
    
    // Clear previous content
    alertsList.innerHTML = '';
    
    if (!alerts || alerts.length === 0) {
        alertsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <p>No alerts found for the selected criteria</p>
            </div>
        `;
        return;
    }
    
    // Create alert items
    alerts.forEach(alert => {
        const alertType = alertTypes[alert.type];
        
        // Extract motor number from ID if available, otherwise use the full ID
        let motorNumber = alert.motorId;
        if (alert.motorId.includes('_')) {
            motorNumber = alert.motorId.split('_')[1];
        }
        
        const alertElement = document.createElement('div');
        alertElement.className = `alert-item ${alert.type}`;
        
        alertElement.innerHTML = `
            <div class="motor-id">
                Motor ${motorNumber}
            </div>
            <div class="alert-message">
                <span class="alert-icon ${alert.type}">
                    <i class="fas ${alertType.icon}"></i>
                </span>
                ${alertType.text}
            </div>
            <div class="alert-datetime">
                ${alert.timestamp}
            </div>
        `;
        
        alertsList.appendChild(alertElement);
    });
}

// Refresh data function
function refreshData() {
    const selectedMotor = document.getElementById('motor-list').value;
    const selectedDate = document.getElementById('dateFilter').value;
    
    // Show loader
    document.getElementById('pre-loader').style.display = 'block';
    
    // Fetch alerts with current filters
    fetchAlerts(selectedMotor, selectedDate);
}
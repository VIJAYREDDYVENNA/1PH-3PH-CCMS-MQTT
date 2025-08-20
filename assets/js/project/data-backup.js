// Keep track of device ID
var group_name = localStorage.getItem("GroupNameValue")
if (group_name == "" || group_name == null) {
    group_name = "ALL";
}

let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
    device_id = document.getElementById('device_id').value;
}

let device_id_list = document.getElementById('device_id');
if (device_id_list) {
    device_id_list.addEventListener('change', function() {
        device_id = document.getElementById('device_id').value;
    });
}
function reset()
{
    $.ajax({
        type: "POST",
        url: '../data-backup/code/reset.php',
        traditional: true,
        data: { GROUP_ID: group_id },
        dataType: "json",
        success: function (data) {
           
            $("#pre-loader").css('display', 'none');
        },
        error: function (textStatus, errorThrown) {
            error_message_text.textContent="Error getting the data";
            error_toast.show();
            $("#pre-loader").css('display', 'none');
        },
        failure: function () {
         error_message_text.textContent="Failed to get the data";
         error_toast.show();

         error_message_text.textContent="Failed to get the data";
         error_toast.show();
         $("#pre-loader").css('display', 'none');
     }
 });
}


// function data_backup(parameter) {
//     // Check if device ID is selected
//     if (!device_id || device_id === "") {
//         alert("Please select a device before downloading backup.");
//         return;
//     }
    
//     // Confirmation before downloading
//     let fileType = parameter === "backup-sql" ? "SQL" : "CSV";
//     let confirmMessage = `Are you sure you want to download ${fileType} backup for device ${device_id}?`;
    
//     if (!confirm(confirmMessage)) {
//         return; // User cancelled the operation
//     }
    
//     // Show loading indicator
//     $("#pre-loader").css('display', 'block');
    
//     // Create a form dynamically
//     var form = document.createElement('form');
//     form.setAttribute('method', 'post');
    
//     // Set the action based on the parameter
//     if (parameter == "backup-sql") {
//         form.setAttribute('action', '../data-backup/code/data-backup.php');
//     } else if (parameter == "backup-excel") {
//         form.setAttribute('action', '../data-backup/code/excel-backup.php'); // New file for Excel export
//     }
    
//     // Add device ID input
//     var deviceInput = document.createElement('input');
//     deviceInput.setAttribute('type', 'hidden');
//     deviceInput.setAttribute('name', 'D_ID');
//     deviceInput.setAttribute('value', device_id);
//     form.appendChild(deviceInput);
    
//     // Add parameter input
//     var paramInput = document.createElement('input');
//     paramInput.setAttribute('type', 'hidden');
//     paramInput.setAttribute('name', 'PARAMETER');
//     paramInput.setAttribute('value', parameter);
//     form.appendChild(paramInput);
    
//     // Add a hidden iframe to detect when download is complete
//     var downloadFrame = document.createElement('iframe');
//     downloadFrame.style.display = 'none';
//     downloadFrame.name = 'download_frame';
//     document.body.appendChild(downloadFrame);
    
//     // Set form target to our hidden iframe
//     form.setAttribute('target', 'download_frame');
    
//     // Add the form to the document body and submit it
//     document.body.appendChild(form);
//     form.submit();
    
//     // Remove the form after submission and show success message
//     setTimeout(function() {
//         document.body.removeChild(form);
//         $("#pre-loader").css('display', 'none');
        
//         // Show success alert
//         alert(`${fileType} backup for ${device_id} has been successfully downloaded.`);
        
//         // Remove the iframe after a delay
//         setTimeout(function() {
//             document.body.removeChild(downloadFrame);
//         }, 1000);
//     }, 2000);
// }

// Reset data functionality
var resetButton = document.getElementById('reset-data');
if (resetButton) {
    resetButton.addEventListener('click', function () {
        if (confirm('Are you sure you want to reset all data? This action cannot be undone!')) {
            // Add reset functionality when implemented
            alert('Data reset has been initiated.');
        }
    });
}
function data_backup(parameter) {
    // Check if device ID is selected
    if (!device_id || device_id === "") {
        alert("Please select a device before downloading backup.");
        return;
    }
    
    // Confirmation before downloading
    let fileType = parameter === "backup-sql" ? "SQL" : "CSV";
    let confirmMessage = `Are you sure you want to download ${fileType} backup for device ${device_id}?`;
    
    if (!confirm(confirmMessage)) {
        return; // User cancelled the operation
    }
    
    // Show loading indicator
    $("#pre-loader").css('display', 'block');
    
    // Determine the URL based on parameter
    let url = parameter === "backup-sql" 
        ? '../data-backup/code/data-backup.php'
        : '../data-backup/code/excel-backup.php';
    
    // Create the data object to send
    let requestData = {
        D_ID: device_id,
        PARAMETER: parameter
    };
    
    // Using jQuery AJAX with xhr to handle binary data
    $.ajax({
        url: url,
        type: 'POST',
        data: requestData,
        xhrFields: {
            responseType: 'blob' // Important for handling binary data
        },
        success: function(data, status, xhr) {
            // Get the filename from the Content-Disposition header if available
            let filename = "backup_" + device_id + "_" + new Date().toISOString().replace(/[:.]/g, "-") + 
                (parameter === "backup-sql" ? ".sql" : ".csv");
            
            let contentDisposition = xhr.getResponseHeader('Content-Disposition');
            if (contentDisposition) {
                let filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch && filenameMatch[1]) {
                    filename = filenameMatch[1];
                }
            }
            
            // Create a download link and trigger it
            let blobUrl = window.URL.createObjectURL(data);
            let link = document.createElement('a');
            link.href = blobUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            
            // Clean up
            window.URL.revokeObjectURL(blobUrl);
            document.body.removeChild(link);
            
            // Show success message
            setTimeout(function() {
                alert(`${fileType} backup for ${device_id} has been successfully downloaded.`);
            }, 500);
        },
        error: function(xhr, status, error) {
            // Parse error response if possible
            let errorMessage = "Download failed. Please try again.";
            
            try {
                let response = JSON.parse(xhr.responseText);
                if (response && response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                // If parsing fails, use the default message
            }
            
            alert("Error: " + errorMessage);
        },
        complete: function() {
            // Hide loading indicator when done (success or error)
            $("#pre-loader").css('display', 'none');
        }
    });
}
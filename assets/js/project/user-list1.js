$(document).ready(function () {
    const initialLimit = $('#items-per-page').val();
    fetchData(1, initialLimit, "");

    $(document).on('click', '#pagination .page-link', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        let limit = $('#items-per-page').val();
        let search_user = document.getElementById("searchInput").value.toLowerCase().trim();
        if (search_user == "" || search_user == null) {
            search_user = "";
        }
        fetchData(page, limit, search_user);

    });

    $('#items-per-page').change(function () {


        let limit = $(this).val();
        let search_user = document.getElementById("searchInput").value.toLowerCase().trim();
        if (search_user == "" || search_user == null) {
            search_user = "";
        }

        fetchData(1, limit, search_user);
    });
});
function addUser() {
    const modal = new bootstrap.Modal(document.getElementById('adduser'));
    document.getElementById('password').value = "";
    // var strengthDiv=document.getElementById('passwordStrength');
    // strengthDiv.textContent = "Password must include: at least 8 characters, an uppercase letter, a lowercase letter, a number, a special character";
    // strengthDiv.classList.remove("text-success");
    // strengthDiv.classList.add("text-danger-emphasis");
    modal.show();
}

function search_users() {
    var search_user = document.getElementById("searchInput").value.toLowerCase().trim();
    var limit = $('#items-per-page').val();

    fetchData(1, limit, search_user)

}


function fetchData(page, limit, search_user) {
    $("#pre-loader").css('display', 'block');
    $.ajax({
        url: '../account/code/user-list.php',
        method: 'GET',
        data: { page: page, limit: limit, search_user: search_user },
        success: function (response) {
            let tableBody = $('#user_list_table');
            let pagination = $('#pagination');
            $("#pre-loader").css('display', 'none');
            tableBody.empty();
            pagination.empty();
            tableBody.append(response.data);
            // Populate table
            /*response.data.forEach(item => {
             tableBody.append(`
              <tr>
              <td>${item.name}</td>
              <td>${item.user_id}</td>
              <td>${item.role}</td>
              <td>${item.mobile_no}</td>
              <td>${item.email_id}</td>
              <td>${item.status}</td>
              <td>
              <div class="btn-group dropend p-0 z-3 popup-btn-group" >                                           
              <button class="btn p-0" type="button" data-bs-toggle="dropdown" style="border:none"><i class="bi bi-three-dots-vertical"></i></button>
              <ul class="dropdown-menu p-0 border-0" style='width:200px'>
              <div class="list-group">
              <button type="button" onclick="editMainTableDetails('${item.id}', '${item.mobile_no}', '${item.name}', this)" class="list-group-item list-group-item-action text-primary" aria-current="true"><i class="bi bi-pen-fill "></i><strong> Edit</strong></button>
              <button type="button" class="list-group-item list-group-item-action text-danger" onclick="deleteRow('${item.id}', '${item.mobile_no}', '${item.name}', this)"><i class="bi bi-trash-fill"></i><strong> Delete</strong></button>
              <button type="button" class="list-group-item list-group-item-action text-success-emphasis" onclick="permissionModal('${item.id}', '${item.mobile_no}', '${item.name}')"><i class="bi bi-shield-lock-fill pe-1"></i><strong>Permissions</strong></button>
              <button type="button" class="list-group-item list-group-item-action" onclick="managing_devices('${item.id}', '${item.mobile_no}', '${item.name}')"><i class="bi bi-cpu pe-1"></i><strong>Managing Devices</strong></button><button type="button" class="list-group-item list-group-item-action text-info" onclick="device_group('${item.id}', '${item.mobile_no}', '${item.name}')"><i class="bi bi-person-lines-fill"></i><strong>Group/Area View</strong></button>
              </div>
              </ul>
              </div>
              </td>
              </tr>
              `);
           });*/
            const totalPages = response.totalPages;
            pagination_fun(pagination, totalPages, page);
        }
    });
}
function permissionModal(userId, mobileNo, name) {
    document.getElementById('userid_per').textContent = userId;
    loadUserPermissions(userId);
    var permission = new bootstrap.Modal(document.getElementById("permission")).show();

}
function editMainTableDetails(userId, mobileNo, name, element) {
    if (!element) {
        console.error('No element provided to editMainTableDetails');
        return;
    }

    currentRow = element.closest('tr');
    if (!currentRow) {
        console.error('No row found for the provided element');
        return;
    }

    document.getElementById('userid_').textContent = userId;
    document.getElementById('edituserName').value = currentRow.cells[0].innerText;
    document.getElementById('edituserid').value = currentRow.cells[1].innerText;

    document.getElementById('edituserMobile').value = currentRow.cells[3].innerText;
    document.getElementById('edituserEmail').value = currentRow.cells[4].innerText;


    var selectedValue = currentRow.cells[2].innerText.trim();
    document.getElementById('editUserRole').value = selectedValue;
    var selectElement = document.getElementById('editUserRole');


    if (Array.from(selectElement.options).some(option => option.value === selectedValue)) {
        selectElement.value = selectedValue;
    } else {

        selectElement.selectedIndex = 0;
    }

    var editmodal = new bootstrap.Modal(document.getElementById('edit'));
    editmodal.show();


}
function deleteRow(userId, mobileNo, name, button) {
    if (confirm(`Are you sure you want to delete the user : ${name}?`)) {
        $("#pre-loader").css('display', 'block');
        $.ajax({
            url: '../account/code/update-user-details.php', // PHP file to handle the update
            type: 'POST',
            data: {
                USERID: userId,
                USERMOBILE: mobileNo,
                UPDATE: "DELETE"
            },
            success: function (response) {
                $("#pre-loader").css('display', 'none');
                if (response.status === 'success') {
                    $(button).closest('tr').remove();
                    alert(response.message);

                } else if (response.status === 'error') {

                    var errorMessages = response.errors.join('\n');
                    alert('Error: ' + response.message + '\n' + errorMessages);
                }
            },
            error: function (xhr, status, error) {
                // Handle any errors that occurred during the AJAX request
                $("#pre-loader").css('display', 'none');
                console.error('AJAX error: ' + error);
                alert('An error occurred while processing your request. Please try again.');
            }

        });
    }
}

document.getElementById('submitButton').addEventListener('click', function () {
    // Get the values of each input field
    var userName = document.getElementById('edituserName').value.trim();
    var userId = document.getElementById('edituserid').value.trim();
    var userRole = document.getElementById('editUserRole').value;
    var userEmail = document.getElementById('edituserEmail').value.trim();
    var userMobile = document.getElementById('edituserMobile').value.trim();
    var id = document.getElementById('userid_').textContent.trim();

    // Initialize a variable to track if all fields are valid
    var allFieldsValid = true;

    // Check if each field is not empty
    if (userName === '') {
        alert('Name field is required.');
        allFieldsValid = false;
    } if (id === '' || id === null) {
        alert('Name field is required.');
        allFieldsValid = false;
    }
    if (userId === '') {
        alert('User_ID field is required.');
        allFieldsValid = false;
    }
    if (userRole === '') {
        alert('User_Role field is required.');
        allFieldsValid = false;
    }
    if (userEmail === '') {
        alert('Email field is required.');
        allFieldsValid = false;
    } else if (!validateEmail(userEmail)) {
        alert('Please enter a valid email address.');
        allFieldsValid = false;
    }
    if (userMobile === '') {
        alert('Mobile field is required.');
        allFieldsValid = false;
    } else if (userMobile.length !== 10 || isNaN(userMobile)) {
        alert('Please enter a valid 10-digit mobile number.');
        allFieldsValid = false;
    }

    // If all fields are valid, you can proceed with form submission or further actions
    if (allFieldsValid) {
        if (confirm(`Are you sure you want to Update the user details ?`)) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: '../account/code/update-user-details.php', // PHP file to handle the update
                type: 'POST',
                data: {
                    USERNAME: userName,
                    USERID: userId,
                    USERROLE: userRole,
                    USEREMAIL: userEmail,
                    USERMOBILE: userMobile,
                    ID: id,
                    UPDATE: "EDIT"
                },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === 'success') {
                        // Display success message
                        alert(response.message);
                        // Optionally, you can clear the form fields or reload the page
                    } else if (response.status === 'error') {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    // Handle any errors that occurred during the AJAX request
                    console.error('AJAX error: ' + error);
                    alert('An error occurred while processing your request. Please try again.');
                }

            });
        }

    }
});

function adding_new_user() {
    var userName = document.getElementById('newprofileName').value.trim();
    var userId = document.getElementById('newuserid').value.trim();
    var userRole = document.getElementById('newUserRole').value.trim();
    var userMobile = document.getElementById('newuserMobile').value.trim();
    var userEmail = document.getElementById('newuserEmail').value.trim();

    var client_login_page = document.getElementById("client_dashboard");
    var client_login_page_value = "USER";
    if (client_login_page) {
        client_login_page_value = client_login_page.value;
    }

    // Initialize a variable to track if all fields are valid
    var allFieldsValid = true;

    // Check if each field is not empty
    if (userName === '') {
        alert('Name field is required.');
        allFieldsValid = false;
    }
    if (userId === '') {
        alert('User_ID field is required.');
        allFieldsValid = false;
    }
    if (userRole === '') {
        alert('User_Role field is required.');
        allFieldsValid = false;
    }
    if (userEmail === '') {
        alert('Email field is required.');
        allFieldsValid = false;
    } else if (!validateEmail(userEmail)) {
        alert('Please enter a valid email address.');
        allFieldsValid = false;
    }
    if (userMobile === '') {
        alert('Mobile field is required.');
        allFieldsValid = false;
    } else if (userMobile.length !== 10 || isNaN(userMobile)) {
        alert('Please enter a valid 10-digit mobile number.');
        allFieldsValid = false;
    }

    const password = document.getElementById('password').value.trim();
    const confirmpassword = document.getElementById('confirmpassword').value.trim();
    const mismatchDiv = document.getElementById('passwordMismatch');

    // if (password !== "" && confirmpassword !== "") {
    //   if (mismatchDiv.style.display === 'block' || !document.getElementById('passwordStrength').textContent.includes('Strong Password')) {
    //     alert('Please ensure the passwords match and meet the strength requirements.');
    //     allFieldsValid = false;
    //   }
    // } else {
    //   mismatchDiv.style.display = 'block';
    //   mismatchDiv.textContent = 'Both password fields are required';
    //   allFieldsValid = false;
    // }

    // If all fields are valid, proceed with form submission or further actions
    if (allFieldsValid) {
        if (confirm(`Are you sure you want to Create new user ?`)) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: '../account/code/update-user-details.php', // PHP file to handle the update
                type: 'POST',
                data: {
                    USERNAME: userName,
                    USERID: userId,
                    USERROLE: userRole,
                    USEREMAIL: userEmail,
                    USERMOBILE: userMobile,
                    USERMOBILE: userMobile,
                    LOGIN_PAGE: client_login_page_value,
                    PASSWORD: password,
                    REENTERPASSWORD: confirmpassword,
                    UPDATE: "NEW_USER"
                },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === 'success') {
                        // Display success message
                        alert(response.message);
                        // Optionally, you can clear the form fields or reload the page
                    } else if (response.status === 'error') {
                        // Display error message and validation errors

                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    // Handle any errors that occurred during the AJAX request
                    console.error('AJAX error: ' + error);
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        }
    }
}

// Function to validate email format
function validateEmail(email) {
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

// document.getElementById('password').addEventListener('keyup', validatePassword);
// document.getElementById('confirmpassword').addEventListener('keyup', checkPasswordMatch);

function validatePassword() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('passwordStrength');
    const mismatchDiv = document.getElementById('passwordMismatch');

    // Regular expressions to check character types
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecialChar = /[\W_]/.test(password);
    const isLongEnough = password.length >= 8;

    let strengthMessage = 'Weak password (must include: ';
    let missingTypes = [];

    // Check for missing character types
    if (!isLongEnough) {
        missingTypes.push('at least 8 characters');
    }
    if (!hasUpperCase) {
        missingTypes.push('an uppercase letter');
    }
    if (!hasLowerCase) {
        missingTypes.push('a lowercase letter');
    }
    if (!hasNumber) {
        missingTypes.push('a number');
    }
    if (!hasSpecialChar) {
        missingTypes.push('a special character');
    }

    // Show which character types are missing
    if (missingTypes.length > 0) {
        strengthMessage += missingTypes.join(', ') + ')';
        strengthDiv.textContent = strengthMessage;
        strengthDiv.classList.remove("text-success");
        strengthDiv.classList.add("text-danger-emphasis");
    } else {
        strengthDiv.textContent = 'Strong Password';
        strengthDiv.classList.remove("text-danger-emphasis");
        strengthDiv.classList.add("text-success");
        mismatchDiv.style.display = 'none';
    }

    checkPasswordMatch();
}

function checkPasswordMatch() {
    const mismatchDiv = document.getElementById('passwordMismatch');
    const confirmpassword = document.getElementById('confirmpassword').value;
    const password = document.getElementById('password').value;

    if (password && confirmpassword && password !== confirmpassword) {
        mismatchDiv.style.display = 'block';
        mismatchDiv.textContent = 'Passwords do not match';
    } else if (password && confirmpassword) {
        mismatchDiv.style.display = 'none';
    }
}

function managing_devices(user_id, mobile, name) {
    const userviewModal = new bootstrap.Modal(document.getElementById('view_managing_device_list'));
    userviewModal.show();
    const initialLimit = $('#items-per-page-user-devices').val();
    document.getElementById('userid_devices').textContent = user_id;
    fetchUserDevices(user_id, 1, initialLimit, "");

    var tabButton = document.getElementById('view-user-devices-list-tab');
    if (tabButton) {
        tabButton.click();
    }
}

function device_group(user_id, mobile, name) {
    const viewModal = new bootstrap.Modal(document.getElementById('device_group'));
    viewModal.show();
    // const initialLimit = $('#items-per-page-user-devices').val();
    document.getElementById('userid_group_devices').textContent = user_id;

}


function assign_group() {
    let userId = document.getElementById('userid_group_devices').textContent.trim();
    userId = Number(userId);
    if (isNaN(userId) || userId <= 0) {
        alert("Invalid User ID. Please try again.");
        return;
    }
    let selected_group = document.getElementById('assing-group').value.trim();

    if (confirm('Are you sure you want to update the user Group?')) {
        $("#pre-loader").css('display', 'block');
        $.ajax({
            url: '../account/code/assign-group-view.php',  // Adjust the path to the actual PHP script
            type: 'POST',
            data: { user_id: userId, group: selected_group },
            dataType: 'json',
            success: function (response) {
                $("#pre-loader").css('display', 'none');
                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                $("#pre-loader").css('display', 'none');
                alert('Failed to update the Group.');
            }
        });
    }

}

function addUserDevice() {
    const initialLimit = $('#items-per-page-user-devices').val();
    let userid_devices = document.getElementById('userid_devices').textContent.trim();

    let search_device = document.getElementById("devicehandlesearch").value.toLowerCase().trim();
    if (search_device == "" || search_device == null) {
        search_device = "";
    }
    fetchUserDevices(userid_devices, 1, initialLimit, search_device);
}
// Set to track selected user devices across searches
let selectedUserDeviceIds = new Set();

function clientSideUserDeviceSearch() {
    const searchTerm = document.getElementById("devicehandlesearch").value.toLowerCase().trim();

    // Before filtering, save current selections
    document.querySelectorAll('#managing_devices_table tr .selectDeviceForDelete:checked').forEach(checkbox => {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        selectedUserDeviceIds.add(deviceId);
    });

    // If search term is empty, show all rows
    if (searchTerm === "") {
        document.querySelectorAll('#managing_devices_table tr').forEach(row => {
            row.style.display = '';
            
            // Restore selections
            const deviceId = row.querySelector('td:nth-child(2)')?.textContent.trim();
            if (deviceId && selectedUserDeviceIds.has(deviceId)) {
                const checkbox = row.querySelector('.selectDeviceForDelete');
                if (checkbox) checkbox.checked = true;
            }
        });
        // Update the count of visible devices that are checked
        updateUserVisibleSelectedCount();
        return true; // Return true indicating search was handled client-side
    }

    let foundMatches = false;
    // Loop through all table rows and hide those that don't match the search query
    document.querySelectorAll('#managing_devices_table tr').forEach(row => {
        const deviceId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const deviceName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        const groupArea = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

        if (deviceId.includes(searchTerm) ||
            deviceName.includes(searchTerm) ||
            groupArea.includes(searchTerm)) {
            row.style.display = '';
            foundMatches = true;
            
            // Restore selection if this device was previously selected
            const deviceIdTrim = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
            if (selectedUserDeviceIds.has(deviceIdTrim)) {
                const checkbox = row.querySelector('.selectDeviceForDelete');
                if (checkbox) checkbox.checked = true;
            }
        } else {
            row.style.display = 'none';
        }
    });

    // Update the count of visible and selected devices
    updateUserVisibleSelectedCount();

    return foundMatches; // Return whether any matches were found
}

// Modified devicehandleSearch to try client-side search first
function devicehandleSearch() {
    const searchTerm = document.getElementById("devicehandlesearch").value.toLowerCase().trim();
    
    // Save current selections before search
    document.querySelectorAll('#managing_devices_table tr .selectDeviceForDelete:checked').forEach(checkbox => {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        selectedUserDeviceIds.add(deviceId);
    });

    // Try client-side search first
    const foundClientSide = clientSideUserDeviceSearch();

    // If nothing found client-side and search term isn't empty, do server-side search
    if (!foundClientSide && searchTerm !== "") {
        let limit = $('#items-per-page-user-devices').val();
        let userid_devices = document.getElementById('userid_devices').textContent.trim();
        fetchUserDevices(userid_devices, 1, limit, searchTerm);
    }
}

// Modified version of fetchUserDevices to maintain selections
function fetchUserDevices(userid_devices, page, limit, search_device) {
    // Don't reset the selectAll checkbox or clear selection count
    // Don't clear selectedUserDeviceIds to persist selections across searches
    
    $("#pre-loader").css('display', 'block');
    $.ajax({
        url: '../account/code/user-handling-devices-table.php',
        method: 'GET',
        data: { page: page, limit: limit, search_item: search_device, user_devices: userid_devices },
        success: function (response) {
            $("#pre-loader").css('display', 'none');
            let tableBody = $('#managing_devices_table');
            let pagination = $('#pagination-user-devices');

            tableBody.empty();
            pagination.empty();

            // Populate table
            if (response.totalPages > 0) {
                response.data.forEach(item => {
                    // Create the row
                    const row = $(`
                        <tr>
                            <td class="select-option"><input type="checkbox" class="selectDeviceForDelete pointer" onclick="fun_check()" /></td>
                            <td>${item.device_id}</td>
                            <td>${item.device_name}</td>
                            <td>${item.device_group_or_area}</td>
                        </tr>
                    `);
                    
                    // Check if this device was previously selected
                    if (selectedUserDeviceIds.has(item.device_id)) {
                        row.find('.selectDeviceForDelete').prop('checked', true);
                    }
                    
                    tableBody.append(row);
                });
            }
            
            const totalPages = response.totalPages;
            pagination_fun(pagination, totalPages, page);
            
            // Update the count of visible and selected devices
            updateUserVisibleSelectedCount();
        }
    });
}

// Function to update the count of selected devices
function updateUserVisibleSelectedCount() {
    const visibleSelectedBoxes = document.querySelectorAll('#managing_devices_table tr:not([style*="display: none"]) .selectDeviceForDelete:checked').length;
    document.getElementById('selected_count').textContent = visibleSelectedBoxes;
}

// Modified function for checking/unchecking devices
function fun_check() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const totalVisible = document.querySelectorAll('#managing_devices_table tr:not([style*="display: none"]) .selectDeviceForDelete').length;
    const totalVisibleChecked = document.querySelectorAll('#managing_devices_table tr:not([style*="display: none"]) .selectDeviceForDelete:checked').length;

    // Update the "Select All" checkbox based on visible rows
    selectAllCheckbox.checked = (totalVisibleChecked > 0 && totalVisibleChecked === totalVisible);

    // Update selectedUserDeviceIds based on individual checkbox changes
    document.querySelectorAll('#managing_devices_table tr .selectDeviceForDelete').forEach(function(checkbox) {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        if (checkbox.checked) {
            selectedUserDeviceIds.add(deviceId);
        } else {
            selectedUserDeviceIds.delete(deviceId);
        }
    });

    // Update the selected count
    updateUserVisibleSelectedCount();
}

// Modified DeleteSelectedDevices function to update the selection set
function DeleteSelectedDevices() {
    let selectedDevices = [];
    let selectedRows = []; // Array to store the rows that will be deleted

    // Gather all selected devices and their corresponding rows
    document.querySelectorAll('.selectDeviceForDelete:checked').forEach(function (checkbox) {
        let row = checkbox.closest('tr');
        let device = row.querySelector('td:nth-child(2)').textContent.trim();
        selectedDevices.push(device);
        selectedRows.push(row); // Store the row for later removal
    });

    if (selectedDevices.length > 0) {
        let selectedDevicesText = selectedDevices.join(',');
        let userid_devices = document.getElementById('userid_devices').textContent.trim();

        if (confirm('Are you sure you want to delete the device(s) from the user account?')) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: '../account/code/handling-devices.php',
                method: 'POST',
                data: { STATUS: "DELETE", DEVICES: selectedDevicesText, USERID: userid_devices },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === 'success') {
                        // Display success message
                        alert(response.message);
                        
                        // Reset the select all checkbox if it was checked
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox.checked) {
                            selectAllCheckbox.checked = false;
                        }
                        
                        // Remove deleted devices from the selection set
                        selectedDevices.forEach(deviceId => selectedUserDeviceIds.delete(deviceId));
                        
                        // Remove the rows from the table
                        selectedRows.forEach(function (row) {
                            row.remove();
                        });
                        
                        // Update the selected count
                        updateUserVisibleSelectedCount();
                    } else if (response.status === 'error') {
                        // Display error message
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    alert('An error occurred while processing your request: ' + error);
                }
            });
        }
    } else {
        alert("Please select device(s) to delete");
    }
}

// Add event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    // Add event listener for the devicehandlesearch input
    document.getElementById('devicehandlesearch').addEventListener('keyup', function(e) {
        // If Enter key is pressed, do the full search
        if (e.key === 'Enter') {
            devicehandleSearch();
        } else {
            // Otherwise just do client-side filtering
            clientSideUserDeviceSearch();
        }
    });
    
    // Update the Select All functionality for user devices
    document.getElementById('selectAll').addEventListener('click', function() {
        const isChecked = this.checked;
        // Check or uncheck all VISIBLE checkboxes with the class 'selectDeviceForDelete'
        document.querySelectorAll('#managing_devices_table tr:not([style*="display: none"]) .selectDeviceForDelete').forEach(function(checkbox) {
            checkbox.checked = isChecked;
            // Update selectedUserDeviceIds based on checkbox state
            const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            if (isChecked) {
                selectedUserDeviceIds.add(deviceId);
            } else {
                selectedUserDeviceIds.delete(deviceId);
            }
        });

        // Update the selected count
        updateUserVisibleSelectedCount();
    });
    
    // Handle pagination and items per page changes
    $(document).on('click', '#pagination-user-devices .page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        let limit = $('#items-per-page-user-devices').val();
        let userid_devices = document.getElementById('userid_devices').textContent.trim();
        fetchUserDevices(userid_devices, page, limit, "");
    });

    $('#items-per-page-user-devices').change(function() {
        let limit = $(this).val();
        let userid_devices = document.getElementById('userid_devices').textContent.trim();
        let search_device = document.getElementById("devicehandlesearch").value.toLowerCase().trim();
        if (search_device == "" || search_device == null) {
            search_device = "";
        }
        fetchUserDevices(userid_devices, 1, limit, search_device);
    });
});


function pagination_fun(pagination, totalPages, page) {

    const maxPagesToShow = 5;
    const windowSize = Math.floor(maxPagesToShow / 2);
    let startPage = Math.max(1, page - windowSize);
    let endPage = Math.min(totalPages, page + windowSize);

    if (page - windowSize < 1) {
        endPage = Math.min(totalPages, endPage + (windowSize - (page - 1)));
    }

    if (page + windowSize > totalPages) {
        startPage = Math.max(1, startPage - (page + windowSize - totalPages));
    }

    // Add "First" button
    if (page > 1) {
        pagination.append(`
      <li class="page-item">
      <a class="page-link" href="#" data-page="1">First</a>
      </li>
    `);
    }

    // Add "Previous" button
    if (page > 1) {
        pagination.append(`
      <li class="page-item">
      <a class="page-link" href="#" data-page="${page - 1}">Previous</a>
      </li>
    `);
    }

    // Add page number buttons
    for (let i = startPage; i <= endPage; i++) {
        pagination.append(`
      <li class="page-item ${i === page ? 'active' : ''}">
      <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>
    `);
    }

    // Add "Next" button
    if (page < totalPages) {
        pagination.append(`
      <li class="page-item">
      <a class="page-link" href="#" data-page="${page + 1}">Next</a>
      </li>
    `);
    }

    // Add "Last" button
    if (page < totalPages) {
        pagination.append(`
      <li class="page-item">
      <a class="page-link" href="#" data-page="${totalPages}">Last</a>
      </li>
    `);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////


document.addEventListener("DOMContentLoaded", function () {
    // "Select All" checkbox click event
    document.getElementById('selectAll').addEventListener('click', function () {
        const isChecked = this.checked;
        // Check or uncheck all checkboxes with the class 'selectDeviceForDelete'
        document.querySelectorAll('.selectDeviceForDelete').forEach(function (checkbox) {
            checkbox.checked = isChecked;
        });

        // Update the selected count
        const allChecked = document.querySelectorAll('.selectDeviceForDelete:checked').length;
        document.getElementById('selected_count').textContent = allChecked;
    });
});

function fun_check() {
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox.checked) {
        if (document.querySelectorAll('.selectDeviceForDelete:checked').length < document.querySelectorAll('.selectDeviceForDelete').length) {
            selectAllCheckbox.checked = false;
        }
    }

    // Update the selected count
    const allChecked = document.querySelectorAll('.selectDeviceForDelete:checked').length;
    document.getElementById('selected_count').textContent = allChecked;
}

// function DeleteSelectedDevices() {
//     let selectedDevices = [];
//     let selectedRows = []; // Array to store the rows that will be deleted

//     // Gather all selected devices and their corresponding rows
//     document.querySelectorAll('.selectDeviceForDelete:checked').forEach(function (checkbox) {
//         let row = checkbox.closest('tr');
//         let device = row.querySelector('td:nth-child(2)').textContent.trim();
//         selectedDevices.push(device);
//         selectedRows.push(row); // Store the row for later removal
//     });

//     if (selectedDevices.length > 0) {
//         let selectedDevicesText = selectedDevices.join(',');
//         let userid_devices = document.getElementById('userid_devices').textContent.trim();

//         if (confirm('Are you sure you want to delete the device(s) from the user account?')) {
//             $("#pre-loader").css('display', 'block');
//             $.ajax({
//                 url: '../account/code/handling-devices.php',
//                 method: 'POST',
//                 data: { STATUS: "DELETE", DEVICES: selectedDevicesText, USERID: userid_devices },
//                 success: function (response) {
//                     $("#pre-loader").css('display', 'none');
//                     if (response.status === 'success') {
//                         // Display success message
//                         alert(response.message);
//                         const selectAllCheckbox = document.getElementById('selectAll');
//                         if (selectAllCheckbox.checked) {
//                             selectAllCheckbox.checked = false;
//                         }
//                         const allChecked = document.querySelectorAll('.selectDeviceForDelete:checked').length;
//                         document.getElementById('selected_count').textContent = allChecked;
//                         selectedRows.forEach(function (row) {
//                             row.remove();
//                         });
//                     } else if (response.status === 'error') {
//                         alert('Error: ' + response.message);
//                     }
//                 },
//                 error: function (xhr, status, error) {
//                     $("#pre-loader").css('display', 'none');
//                     alert('An error occurred while processing your request: ' + error);
//                 }
//             });
//         }
//     } else {
//         alert("Please select device(s)");
//     }
// }


function syncToMyAccount() {
    let selectedDevices = [];
    let selectedRows = []; // Array to store the rows that will be deleted

    // Gather all selected devices and their corresponding rows
    document.querySelectorAll('.selectDeviceForDelete:checked').forEach(function (checkbox) {
        let row = checkbox.closest('tr');
        let device = row.querySelector('td:nth-child(2)').textContent.trim();
        selectedDevices.push(device);
        selectedRows.push(row);
    });

    if (selectedDevices.length > 0) {
        let selectedDevicesText = selectedDevices.join(',');
        let userid_devices = document.getElementById('userid_devices').textContent.trim();

        if (confirm('Are you sure you want to sync the device(s) with your account?')) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: '../account/code/handling-devices.php',
                method: 'POST',
                data: { STATUS: "SYNC", DEVICES: selectedDevicesText, USERID: userid_devices },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === 'success') {

                        alert(response.message);
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox.checked) {
                            selectAllCheckbox.checked = false;
                        }
                        let checkboxes = document.getElementsByClassName('selectDeviceForDelete');
                        for (let checkbox of checkboxes) {
                            checkbox.checked = false;
                        }
                        const allChecked = document.querySelectorAll('.selectDeviceForDelete:checked').length;
                        document.getElementById('selected_count').textContent = allChecked;

                    } else if (response.status === 'error') {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    alert('An error occurred while processing your request: ' + error);
                }
            });
        }
    } else {
        alert("Please select device(s)");
    }
}





/////////////////////////////////////////////////////////////////////////////////////////////

// Define selectedDeviceIds as a global Set to persist selections
let selectedDeviceIds = new Set();

function clientSideSearch() {
    const searchTerm = document.getElementById("adminhandlesearch").value.toLowerCase().trim();

    // Before filtering, save current selections
    document.querySelectorAll('#admin-managing_devices_table tr .selectDeviceForAdd:checked').forEach(checkbox => {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        selectedDeviceIds.add(deviceId);
    });

    // If search term is empty, show all rows
    if (searchTerm === "") {
        document.querySelectorAll('#admin-managing_devices_table tr').forEach(row => {
            row.style.display = '';
            
            // Restore selections
            const deviceId = row.querySelector('td:nth-child(2)')?.textContent.trim();
            if (deviceId && selectedDeviceIds.has(deviceId)) {
                const checkbox = row.querySelector('.selectDeviceForAdd');
                if (checkbox) checkbox.checked = true;
            }
        });
        // Update the count of visible devices that are checked
        updateVisibleSelectedCount();
        return true; // Return true indicating search was handled client-side
    }

    let foundMatches = false;
    // Loop through all table rows and hide those that don't match the search query
    document.querySelectorAll('#admin-managing_devices_table tr').forEach(row => {
        const deviceId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const deviceName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        const groupArea = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

        if (deviceId.includes(searchTerm) ||
            deviceName.includes(searchTerm) ||
            groupArea.includes(searchTerm)) {
            row.style.display = '';
            foundMatches = true;
            
            // Restore selection if this device was previously selected
            const deviceIdTrim = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
            if (selectedDeviceIds.has(deviceIdTrim)) {
                const checkbox = row.querySelector('.selectDeviceForAdd');
                if (checkbox) checkbox.checked = true;
            }
        } else {
            row.style.display = 'none';
        }
    });

    // Update the count of visible and selected devices
    updateVisibleSelectedCount();

    return foundMatches; // Return whether any matches were found
}

function addnewDevice() {
    const initialLimit = $('#items-per-page-user-devices').val();
    let user_id = document.getElementById('userid_devices').textContent.trim();
    let search_device = document.getElementById("adminhandlesearch").value.toLowerCase().trim();
    if (search_device == "" || search_device == null) {
        search_device = "";
    }
    // Clear selections when adding new devices
    selectedDeviceIds.clear();
    fetchAdminDevices(user_id, 1, initialLimit, search_device);
}

$(document).on('click', '#pagination-admin-devices .page-link', function (e) {
    e.preventDefault();

    let page = $(this).data('page');
    let limit = $('#items-per-page-admin-devices').val();
    let userid_devices = document.getElementById('userid_devices').textContent.trim();
    fetchAdminDevices(userid_devices, page, limit, "");
});

$('#items-per-page-admin-devices').change(function () {
    let limit = $(this).val();
    let userid_devices = document.getElementById('userid_devices').textContent.trim();
    let search_device = document.getElementById("adminhandlesearch").value.toLowerCase().trim();
    if (search_device == "" || search_device == null) {
        search_device = "";
    }
    fetchAdminDevices(userid_devices, 1, limit, search_device);
});

function adminhandlesearch() {
    const searchTerm = document.getElementById("adminhandlesearch").value.toLowerCase().trim();

    // Save current selections before search
    document.querySelectorAll('#admin-managing_devices_table tr .selectDeviceForAdd:checked').forEach(checkbox => {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        selectedDeviceIds.add(deviceId);
    });

    // Try client-side search first
    const foundClientSide = clientSideSearch();

    // If nothing found client-side and search term isn't empty, do server-side search
    if (!foundClientSide && searchTerm !== "") {
        let limit = $('#items-per-page-admin-devices').val();
        let userid_devices = document.getElementById('userid_devices').textContent.trim();
        fetchAdminDevices(userid_devices, 1, limit, searchTerm);
    }
}

function fetchAdminDevices(userid_devices, page, limit, search_device) {
    // Don't reset the selectAllAdd checkbox or clear selection count
    // Don't clear the selectedDeviceIds here to persist selections across searches
    
    $("#pre-loader").css('display', 'block');
    $.ajax({
        url: '../account/code/admin-handling-devices-table.php',
        method: 'GET',
        data: { page: page, limit: limit, search_item: search_device, user_devices: userid_devices },
        success: function (response) {
            $("#pre-loader").css('display', 'none');
            let tableBody = $('#admin-managing_devices_table');
            let pagination = $('#pagination-admin-devices');

            tableBody.empty();
            pagination.empty();

            // Populate table
            if (response.totalPages > 0) {
                response.data.forEach(item => {
                    // Create the row
                    const row = $(`
                        <tr>
                            <td class="select-option"><input type="checkbox" class="selectDeviceForAdd pointer" onclick="fun_check_for_add()" /></td>
                            <td>${item.device_id}</td>
                            <td>${item.device_name}</td>
                            <td>${item.device_group_or_area}</td>
                        </tr>
                    `);
                    
                    // Check if this device was previously selected
                    if (selectedDeviceIds.has(item.device_id)) {
                        row.find('.selectDeviceForAdd').prop('checked', true);
                    }
                    
                    tableBody.append(row);
                });
            }
            
            const totalPages = response.totalPages;
            pagination_fun(pagination, totalPages, page);
            
            // Update the count of visible and selected devices
            updateVisibleSelectedCount();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Update the Select All functionality
    document.getElementById('selectAllAdd').addEventListener('click', function () {
        const isChecked = this.checked;
        // Check or uncheck all VISIBLE checkboxes with the class 'selectDeviceForAdd'
        document.querySelectorAll('#admin-managing_devices_table tr:not([style*="display: none"]) .selectDeviceForAdd').forEach(function (checkbox) {
            checkbox.checked = isChecked;
            // Update selectedDeviceIds based on checkbox state
            const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            if (isChecked) {
                selectedDeviceIds.add(deviceId);
            } else {
                selectedDeviceIds.delete(deviceId);
            }
        });

        // Update the selected count
        updateVisibleSelectedCount();
    });
});

function fun_check_for_add() {
    const selectAllCheckbox = document.getElementById('selectAllAdd');
    const totalVisible = document.querySelectorAll('#admin-managing_devices_table tr:not([style*="display: none"]) .selectDeviceForAdd').length;
    const totalVisibleChecked = document.querySelectorAll('#admin-managing_devices_table tr:not([style*="display: none"]) .selectDeviceForAdd:checked').length;

    // Update the "Select All" checkbox based on visible rows
    selectAllCheckbox.checked = (totalVisibleChecked > 0 && totalVisibleChecked === totalVisible);

    // Update selectedDeviceIds based on individual checkbox changes
    document.querySelectorAll('#admin-managing_devices_table tr .selectDeviceForAdd').forEach(function(checkbox) {
        const deviceId = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        if (checkbox.checked) {
            selectedDeviceIds.add(deviceId);
        } else {
            selectedDeviceIds.delete(deviceId);
        }
    });

    // Update the selected count
    updateVisibleSelectedCount();
}

function updateVisibleSelectedCount() {
    const visibleSelectedBoxes = document.querySelectorAll('#admin-managing_devices_table tr:not([style*="display: none"]) .selectDeviceForAdd:checked').length;
    document.getElementById('selected_count_add_device').textContent = visibleSelectedBoxes;
}

function addSelectedDevices() {
    let selectedDevices = [];
    let selectedRows = []; // Array to store the rows that will be deleted

    // Gather all selected devices and their corresponding rows
    document.querySelectorAll('.selectDeviceForAdd:checked').forEach(function (checkbox) {
        let row = checkbox.closest('tr');
        let device = row.querySelector('td:nth-child(2)').textContent.trim();
        selectedDevices.push(device);
        selectedRows.push(row); // Store the row for later removal
    });

    if (selectedDevices.length > 0) {
        let selectedDevicesText = selectedDevices.join(',');
        let userid_devices = document.getElementById('userid_devices').textContent.trim();

        if (confirm('Are you sure you want to add the device(s) to the user account?')) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: '../account/code/handling-devices.php',
                method: 'POST',
                data: { STATUS: "ADD", DEVICES: selectedDevicesText, USERID: userid_devices },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === 'success') {
                        // Display success message
                        alert(response.message);
                        
                        // Reset the select all checkbox if it was checked
                        const selectAllCheckbox = document.getElementById('selectAllAdd');
                        if (selectAllCheckbox.checked) {
                            selectAllCheckbox.checked = false;
                        }
                        
                        // Remove added devices from the selection set
                        selectedDevices.forEach(deviceId => selectedDeviceIds.delete(deviceId));
                        
                        // Remove the rows from the table
                        selectedRows.forEach(function (row) {
                            row.remove();
                        });
                        
                        // Update the selected count
                        updateVisibleSelectedCount();
                    } else if (response.status === 'error') {
                        // Display error message
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    alert('An error occurred while processing your request: ' + error);
                }
            });
        }
    } else {
        alert("Please select device(s)");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('adminhandlesearch').addEventListener('keyup', function (e) {
        // If Enter key is pressed, do the full search
        if (e.key === 'Enter') {
            adminhandlesearch();
        } else {
            // Otherwise just do client-side filtering
            clientSideSearch();
        }
    });
});

//////////////////////////////////////////////////////////////////////////////////////
function updateSelectedPermissions() {
    let selectedPermissions = {};

    // Collect all permissions and whether they are checked or not
    document.querySelectorAll('#permissions-form input[type="checkbox"]').forEach(function (checkbox) {
        selectedPermissions[checkbox.id] = checkbox.checked ? 1 : 0;
    });

    let userId = document.getElementById('userid_per').textContent.trim();
    userId = Number(userId);  // Try converting to a number
    if (isNaN(userId) || userId <= 0) {
        alert("Invalid User ID. Please try again.");
        return;  // Stop the function if the userId is not a valid number
    }

    if (confirm('Are you sure you want to update the user Permissions?')) {
        $("#pre-loader").css('display', 'block');
        $.ajax({
            url: '../account/code/permissions-save.php',  // Adjust the path to the actual PHP script
            type: 'POST',
            data: { user_id: userId, permissions: selectedPermissions },
            dataType: 'json',
            success: function (response) {
                $("#pre-loader").css('display', 'none');
                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                $("#pre-loader").css('display', 'none');
                alert('Failed to update permissions.');
            }
        });
    }
}

function loadUserPermissions(userId) {
    $("#pre-loader").css('display', 'block');
    $.ajax({
        url: '../account/code/permissions-list.php',  // Adjust the path to the actual PHP script
        type: 'POST',
        data: { user_id: userId },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                let permissions = response.permissions;

                // Iterate over the permissions object and set checkboxes
                let htmlid = "";
                for (let permName in permissions) {
                    if (permissions.hasOwnProperty(permName)) {

                        htmlid = htmlid + permName + ",";
                        $('#' + permName).prop('checked', permissions[permName] == 1);
                    }
                }
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('Failed to fetch permissions.');
        }
    });
}



/////////////////////////////////////////////////////////////////////////////////////////////

function menu_permission(user_id, mobile, name) {
    const viewModal = new bootstrap.Modal(document.getElementById('menu_permission'));
    loadUserMenuPermissions(user_id);
    viewModal.show();
    // const initialLimit = $('#items-per-page-user-devices').val();
    document.getElementById('userid_menu').textContent = user_id;

}

function updateSelectedMenuPermissions() {
    let selectedMenuPermissions = {};

    // Collect all permissions and whether they are checked or not
    document.querySelectorAll('#menu-permissions-form input[type="checkbox"]').forEach(function (checkbox) {
        selectedMenuPermissions[checkbox.id] = checkbox.checked ? 1 : 0;
    });

    let userId = document.getElementById('userid_menu').textContent.trim();
    userId = Number(userId);  // Try converting to a number
    if (isNaN(userId) || userId <= 0) {
        alert("Invalid User ID. Please try again.");
        return;  // Stop the function if the userId is not a valid number
    }
    console.log(selectedMenuPermissions);
    if (confirm('Are you sure you want to update the user menu Permissions?')) {
        $("#pre-loader").css('display', 'block');
        $.ajax({
            url: '../account/code/menu-permissions-save.php',  // Adjust the path to the actual PHP script
            type: 'POST',
            data: { user_id: userId, permissions: selectedMenuPermissions },
            dataType: 'json',
            success: function (response) {
                $("#pre-loader").css('display', 'none');
                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                $("#pre-loader").css('display', 'none');
                alert('Failed to update permissions.');
            }
        });
    }
}

function loadUserMenuPermissions(userId) {
    $("#pre-loader").css('display', 'block');
    $.ajax({
        url: '../account/code/menu-permissions-list.php',  // Adjust the path to the actual PHP script
        type: 'POST',
        data: { user_id: userId },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                let permissions = response.permissions;

                // Iterate over the permissions object and set checkboxes
                let htmlid = "";
                for (let permName in permissions) {
                    if (permissions.hasOwnProperty(permName)) {

                        htmlid = htmlid + permName + ",";
                        $('#' + permName).prop('checked', permissions[permName] == 1);
                    }
                }
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('Failed to fetch permissions.');
        }
    });
}


/////////////////////////////////////////////////////////////////////////////////////////////

function account_action(user_id, mobile, name) {
    const viewModal = new bootstrap.Modal(document.getElementById('user_action'));
    viewModal.show();
    document.getElementById('userid_action').textContent = user_id;

}
function saveAction() {

    let userId = document.getElementById('userid_action').textContent.trim();
    userId = Number(userId);
    if (isNaN(userId) || userId <= 0) {
        alert("Invalid User ID. Please try again.");
        return;
    }
    let selectedAction = document.getElementById('select_action').value.trim();

    if (confirm('Are you sure you want to update the user menu Permissions?')) {
        $("#pre-loader").css('display', 'block');
        $.ajax({
            url: '../account/code/account-action.php',  // Adjust the path to the actual PHP script
            type: 'POST',
            data: { user_id: userId, action: selectedAction },
            dataType: 'json',
            success: function (response) {
                $("#pre-loader").css('display', 'none');
                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                $("#pre-loader").css('display', 'none');
                alert('Failed to update Action.');
            }
        });
    }

}


/////////////////////////////////////////////////////////////////////////////////////////////





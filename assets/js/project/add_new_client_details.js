function validateForm(clientId, clientName) {
    let valid = true;
    let errorMessage = '';

    // Check if clientId and clientName are strings and not empty
    if (typeof clientId !== 'string' || clientId.trim() === '') {
        errorMessage += '<div class="alert alert-danger">Client ID is invalid.</div>';
        valid = false;
    }

    if (typeof clientName !== 'string' || clientName.trim() === '') {
        errorMessage += '<div class="alert alert-danger">Client Name is invalid.</div>';
        valid = false;
    }

    // Check if the input contains potential harmful characters to avoid hacking attempts
    const forbiddenCharacters = /[<>{}()'";]/;
    if (forbiddenCharacters.test(clientId)) {
        errorMessage += '<div class="alert alert-danger">Client ID contains invalid characters.</div>';
        valid = false;
    }

    if (forbiddenCharacters.test(clientName)) {
        errorMessage += '<div class="alert alert-danger">Client Name contains invalid characters.</div>';
        valid = false;
    }

    // Display the error message if any
    document.getElementById('response-message').innerHTML = errorMessage;

    return valid;
}

function submitClientForm() {
    const clientId = document.getElementById('client-id').value;
    const clientName = document.getElementById('client-name').value;

    // Validate input fields
    if (!validateForm(clientId, clientName)) {
        return;
    }

    // Show a confirmation alert before submission
    const confirmation = confirm("Are you sure you want to submit this client's details?");
    if (!confirmation) {
        return;  // If the user clicks "Cancel", stop the form submission
    }

    // AJAX request using jQuery
    $.ajax({
        type: "POST",
        url: '../account/code/add-new-client-details.php',  // Update with the actual URL
        data: { 
            'client-id': clientId, 
            'client-name': clientName 
        },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                document.getElementById('response-message').innerHTML = '<div class="alert alert-success">' + response.message + '</div>';
                document.getElementById('new-client-data').reset(); // Clear form on success
            } else {
                document.getElementById('response-message').innerHTML = '<div class="alert alert-danger">' + response.message + '</div>';
            }
        },
        error: function() {
            document.getElementById('response-message').innerHTML = '<div class="alert alert-danger">An error occurred while submitting the form.</div>';
        }
    });
}

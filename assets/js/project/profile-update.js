function openEditModal() {
  var edit = document.getElementById("editModal");
  var editOpen = new bootstrap.Modal(edit);
  editOpen.show();
}


function openPasswordModal() {
  var edit = document.getElementById("passwordModal");
  document.getElementById('newPassword').value="";
  var strengthDiv =document.getElementById('passwordStrength');
  strengthDiv.classList.remove("text-success");
  strengthDiv.classList.add("text-danger-emphasis");
  strengthDiv.textContent="Password must include: at least 8 characters, an uppercase letter, a number, a special character";
  var editpasswordOpen = new bootstrap.Modal(edit);
  editpasswordOpen.show();
}


function openUsernameModal() {
  var edit = document.getElementById("usernameModal");
  var editusernameOpen = new bootstrap.Modal(edit);
  editusernameOpen.show();
}

document.getElementById('submitButton').addEventListener('click', function() {

 var userName = document.getElementById('edituserName').value.trim();
 var userEmail = document.getElementById('edituserEmail').value.trim();
 var userMobile = document.getElementById('edituserMobile').value.trim();

    // Initialize a variable to track if all fields are valid
 var allFieldsValid = true;

    // Check if each field is not empty
 if (userName === '') {
  alert('Name field is required.');
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
  if (confirm(`Are you sure you want to update the account details?`)) {
   $("#pre-loader").css('display', 'block'); 
   $.ajax({
            url: '../profile/code/profile-update.php', // PHP file to handle the update
            type: 'POST',
            data: {
            	USERNAME: userName,
            	USEREMAIL: userEmail,
            	USERMOBILE: userMobile,
            	
            },
            success: function(response) {
             $("#pre-loader").css('display', 'none'); 
             if (response.status === 'success') {

              document.getElementById('empname').textContent=userName;
              document.getElementById('mobile').textContent=userMobile;
              document.getElementById('email').textContent=userEmail;
                    // Display success message
              alert(response.message);
                    // Optionally, you can clear the form fields or reload the page
            } else if (response.status === 'error') {
                    // Display error message and validation errors
              var errorMessages = response.errors.join('\n');
              alert('Error: ' + response.message + '\n' + errorMessages);
            }
          },
          error: function(xhr, status, error) {
                // Handle any errors that occurred during the AJAX request
            $("#pre-loader").css('display', 'none'); 
            alert('An error occurred while processing your request. Please try again.');
          }

        });
 }
}
});


document.getElementById('update_user_id').addEventListener('click', function() {
    // Get the values of each input field
 var oldusername = document.getElementById('oldusername').value.trim();
 var newusername = document.getElementById('newusername').value.trim();
 
    // Initialize a variable to track if all fields are valid
 var allFieldsValid = true;

    // Check if each field is not empty
 if (oldusername === '') {
  alert('Old user Id/Name field is required.');
  allFieldsValid = false;
}
if (newusername === '') {
  alert('New user Id/Namefield is required.');
  allFieldsValid = false;
}
else if (newusername.length < 6 ) {
  alert('Please enter new user ID/Name must be at least 6 characters long.');
  allFieldsValid = false;
}

if (allFieldsValid) {
 if (confirm(`Are you sure you want to update the account details?`)) {
   $("#pre-loader").css('display', 'block'); 
   $.ajax({
    url: '../profile/code/profile-update.php',
    type: 'POST',
    data: {
      OLDUSERNAME: oldusername,
      NEWUSERNAME: newusername,
    },
    success: function(response) {
     $("#pre-loader").css('display', 'none'); 
     if (response.status === 'success') {
      document.getElementById('username').textContent=newusername;
      alert(response.message);

    } else if (response.status === 'error') {

      var errorMessages = response.errors.join('\n');
      alert('Error: ' + response.message + '\n' + errorMessages);
    }
  },
  error: function(xhr, status, error) {
   $("#pre-loader").css('display', 'none'); 
   console.error('AJAX error: ' + error);
   alert('An error occurred while processing your request. Please try again.');
 }

});
 }
}
});

// Function to validate email format
function validateEmail(email) {
 var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
 return emailPattern.test(email);
}


document.getElementById('newPassword').addEventListener('keyup', validatePassword);
document.getElementById('reenterPassword').addEventListener('keyup', checkPasswordMatch);

const reenterPassword = document.getElementById('reenterPassword').value;


function validatePassword() {
  const password = document.getElementById('newPassword').value;
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
    
   /* mismatchDiv.style.display = 'block';
    mismatchDiv.textContent = strengthMessage;*/
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
  const strengthDiv = document.getElementById('passwordStrength');
  const reenterPassword = document.getElementById('reenterPassword').value;
  const password = document.getElementById('newPassword').value;
  if (password && reenterPassword && password !== reenterPassword) {
    mismatchDiv.style.display = 'block';
    mismatchDiv.textContent = 'Passwords do not match';
  } else if (password && reenterPassword) {
    mismatchDiv.style.display = 'none';
  }
}

function change_password() {
  const password = document.getElementById('newPassword').value.trim();
  const reenterPassword = document.getElementById('reenterPassword').value.trim();

  if(password!=""&&reenterPassword!="")
  {

  // Check for password strength and match
    if (document.getElementById('passwordMismatch').style.display === 'block' || !document.getElementById('passwordStrength').textContent.includes('Strong Password')) {
      alert('Please ensure the passwords match and meet the strength requirements.');
      return false;
    }
    if (confirm(`Are you sure you want to chnage password?`)) {
     $("#pre-loader").css('display', 'block'); 
     $.ajax({
      url: '../profile/code/profile-update.php',
      type: 'POST',
      data: {
        PASSWORD: password,
        REENTERPASSWORD: reenterPassword

      },
      success: function(response) {
       $("#pre-loader").css('display', 'none'); 
       if (response.status === 'success') {         
        alert(response.message);

      } else if (response.status === 'error') {
        let strengthDiv = document.getElementById('passwordStrength');
        strengthDiv.textContent = response.message;
        strengthDiv.classList.remove("text-success");
        strengthDiv.classList.add("text-danger-emphasis");
      }
    },
    error: function(xhr, status, error) {
     $("#pre-loader").css('display', 'none'); 
     console.error('AJAX error: ' + error);
     alert('An error occurred while processing your request. Please try again.');
   }

 });
   }
 }
 else
 {
  const mismatchDiv = document.getElementById('passwordMismatch');
  mismatchDiv.style.display = 'block';
  mismatchDiv.textContent = 'Both password fields are required';
}
}





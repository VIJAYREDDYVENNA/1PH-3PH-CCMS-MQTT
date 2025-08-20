function forgotpassword()
{
	let user_id = document.getElementById('ForgotPassword').value;
	user_id=user_id.trim();	
	if(user_id!=""&&user_id!=null)
	{
		if (confirm(`Are you sure ?`)) {
			$("#pre-loader").css('display', 'block'); 
			$.ajax({
				type: "POST",
				url: '../login/send-password.php',
				traditional: true,
				data: { USER_ID: user_id},
				dataType: "json",
				success: function(response) {
					$("#pre-loader").css('display', 'none');					
					alert(response);

				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#pre-loader").css('display', 'none');
					alert(`Error: ${textStatus}, ${errorThrown}`);
				}
			});
		}
	}
	else
	{
		alert("Please enter valid mobile-no/user ID..");
	}
}
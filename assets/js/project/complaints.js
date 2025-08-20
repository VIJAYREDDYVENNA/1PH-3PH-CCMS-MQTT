let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast= bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast= bootstrap.Toast.getOrCreateInstance(success_message);

const group_list = document.getElementById('group-list');

var group_name=localStorage.getItem("GroupNameValue")
if(group_name==""||group_name==null)
{
	group_name="ALL";
}
get_table_data(group_name);

group_list.addEventListener('change', function() {
	let group_name = group_list.value;
	if (group_name !== "" && group_name !== null) {
		$("#pre-loader").css('display', 'block');
		
	}
});

const device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
	$("#pre-loader").css('display', 'block');
	var device_id = document.getElementById('device_id').value;
	var group_value = group_list.value;
	get_table_data(group_value);
	refresh_data();
});

setTimeout(refresh_data, 50);
setInterval(refresh_data, 20000);
function refresh_data() {
	if (typeof update_frame_time === "function") {
		device_id = document.getElementById('device_id').value;
		update_frame_time(device_id);
	} 
	/*let group_name = group_list.value;
	if (group_name !== "" && group_name !== null) {
		add_device_list(group_name);
	}*/
}



$('#update_complaints').click(function(){

	var group_value = group_list.value;
	get_table_data(group_value);
	$('#complaints_filter_Modal').modal('hide');
	$("#pre-loader").css('display', 'block');

});


function get_table_data(group_value){

	var device_id = document.getElementById('device_id').value;
	var selection=$('#selection').val();
	var complaint__list=$('#complaint_status').val();

	if(device_id!="")
	{
		records="50";
		$.ajax({
			method : "POST",
			url :"../complaints/code/complaints-list.php",  
			data:{ ID : device_id, SELECTION:selection, GROUP:group_value, COMPLAINT_STATUS:complaint__list }, 

			success : function(result){
				$('#complaints_list_table tr').remove();
				$('#complaints_list_table').html(result);


			},
			error: function (textStatus, errorThrown) {
				error_message_text.textContent="Error getting the data..";
				error_toast.show();
			},
			failure: function()
			{
				error_message_text.textContent="Failed to get the data..";
				error_toast.show();
			}
		});
	}
}

$('#raise_new_complaint').click(function(){		

	var new_complaint=$('#new_complaint').val();

	if(new_complaint==""||new_complaint==null)
	{
		
		error_message_text.textContent="Please enter the Complaint..";
		error_toast.show();
		return false;
	}

	device_id=$('#device_id').val();

	if(device_id!="")
	{

		if (confirm('Are you sure you want to complain?')) {

			$.ajax({
				method : "POST",
				url :"../complaints/code/complaint_register.php",  
				data:{ ID : device_id, COMPLAINT:new_complaint}, 
				success : function(data){
					$('#new_complaint').val("");
					$('#raise_complaints_Modal').modal('hide');
					success_message_text.textContent=data;
					success_toast.show();
					
					var group_value = group_list.value;			
					get_table_data(group_value);

				},
				error: function (textStatus, errorThrown) {
					error_message_text.textContent="Error getting the data..";
					error_toast.show();
				},
				failure: function()
				{
					error_message_text.textContent="Failed to get the data..";
					error_toast.show();
				}
			});
		}
	}

});

$('#closing_complaint').click(function(){		

	var report=$('#complaint_update_status').val();
	var complaint_no=$('#complaint_id_close').text();
	var complaint_close="";
	if(complaint_no==""||complaint_no==null)
	{
		error_message_text.textContent="Complaint No. is not updated. Try again..";
		error_toast.show();
		
		return false;
	}

	if ($('#accept_close').is(":checked")) {
		complaint_close="CLOSE";
	}
	if(report!=""&&report!=null)
	{
		if (confirm('Are you sure?')) {

			$.ajax({
				method : "POST",
				url :"../complaints/code/complaint-update_status.php",  
				data:{ ID : complaint_no, COMPLAINT:report, CLOSE:complaint_close}, 
				success : function(data){
					$('#complaint_update_status').val("");
					$('#complaints_close_Modal').modal('hide');		
					var group_value = group_list.value;			
					get_table_data(group_value);
					success_message_text.textContent=data;
					success_toast.show();
					

				},
				error: function (textStatus, errorThrown) {
					error_message_text.textContent="Error getting the data..";
					error_toast.show();
				},
				failure: function()
				{
					error_message_text.textContent="Failed to get the data..";
					error_toast.show();

				}
			});

		}
	}
	else
	{
		error_message_text.textContent="Please enter the final report..";
		error_toast.show();

		return false;
	}

});

function fetch_more_records() {

	var device_id = document.getElementById('device_id').value;
	var selection=$('#selection').val();
	var complaint__list=$('#complaint_status').val();
	
	var group_value = group_list.value;
	records="50";
	$.ajax({
		method : "POST",
		url :"../complaints/code/complaints-list.php",  
		data:{ ID : device_id, SELECTION:selection, GROUP:group_value, COMPLAINT_STATUS:complaint__list, FETCH_MORE:"MORE" }, 

		success : function(result){
			$('#complaints_list_table').append(result);


		},
		error: function (textStatus, errorThrown) {
			error_message_text.textContent="Error getting the data..";
			error_toast.show();

		},
		failure: function()
		{
			error_message_text.textContent="Failed to get the data..";
			error_toast.show();
		}
	});

};



function check_track(device_id, reg_no)
{
	$.ajax({
		method : "POST",
		url :"../complaints/code/complaints-tracking.php",  
		data:{ ID : reg_no, COUNT:'50'}, 
		success : function(result){
			$('#tracking_complaints_Modal').modal('show');

			$(".complaint_id").text(reg_no);			
			$('#complaint_status_update tr').remove();
			$('#complaint_status_update').html(result);
		},
		error: function (textStatus, errorThrown) {
			error_message_text.textContent="Error getting the data..";
			error_toast.show();
		},
		failure: function()
		{
			error_message_text.textContent="Failed to get the data..";
			error_toast.show();
		}
	});
}
function show_more_compalints_history()
{
	$.ajax({
		method : "POST",
		url :"../complaints/code/complaints-tracking.php",  
		data:{ FETCH_MORE : "MORE"}, 
		success : function(result){
			$('#complaint_status_update').append(result);
		},
		error: function (textStatus, errorThrown) {
			error_message_text.textContent="Error getting the data..";
			error_toast.show();
		},
		failure: function()
		{
			error_message_text.textContent="Failed to get the data..";
			error_toast.show();
		}
	});

}


/*$("#complaints_list_table").on('click','.check_track',function(){

	var currentRow=$(this).closest("tr"); 
	var complaint_no=currentRow.find("td:eq(0)").text(); 


	


});*/
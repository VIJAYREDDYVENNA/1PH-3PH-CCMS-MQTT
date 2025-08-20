function update_frame_time(device_id)
{
	//console.log(device_id)
	if(device_id!=""||device_id!=null)
	{
		$.ajax({
			type: "POST",
			url: "../dashboard/code/device_latest_values_update.php",
			data: {
				DEVICE_ID: device_id 
			},
			dataType: "json",
			success: function(data) {
				$('#auto_update_date_time').text(data.DATE_TIME);  
			}
		});
	}
}
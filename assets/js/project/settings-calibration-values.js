

function read_iot_set_values(row_id, view)
{
	var device_id=$('#device_id').val();
	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			method : "POST",
			url:"../device-reports/code/calibration-values.php",
			data:{ D_ID : device_id, ROW:row_id, ROW_VIEW:view },
			traditional:true,
			dataType: "json", 
			success : function(result){
				$("#pre-loader").css('display', 'none'); 
				if(result.length>0)
				{
					data=result;
					var count= data[data.length-1];
					$('#row_count').html(count);
					$('#v_r').val(data[4]);
					$('#v_y').val(data[12]);
					$('#v_b').val(data[20]);

					$('#i_r').val(data[5]);
					$('#i_y').val(data[13]);
					$('#i_b').val(data[21]);

					$('#gain_r').val(data[6]);
					$('#gain_y').val(data[14]);
					$('#gain_b').val(data[22]);

					$('#angle_1_r').val(data[7]);
					$('#angle_1_y').val(data[15]);
					$('#angle_1_b').val(data[23]);

					$('#angle_2_r').val(data[8]);
					$('#angle_2_y').val(data[16]);
					$('#angle_2_b').val(data[24]);

					$('#awg_r').val(data[9]);
					$('#awg_y').val(data[17]);
					$('#awg_b').val(data[25]);

					$('#avag_r').val(data[10]);
					$('#avag_y').val(data[18]);
					$('#avag_b').val(data[26]);

					$('#avarg_r').val(data[11]);
					$('#avarg_y').val(data[19]);
					$('#avarg_b').val(data[27]);
				}
				else
				{
					$('#row_count').html(0);
					alert("Records are not found");
				}

			},
			error: function (textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none'); 
				alert("Error getting the data");
			},
			failure: function()
			{
				$("#loader").css('display', 'none');
				alert("Failed to get the data");
			}
		});
	}
}
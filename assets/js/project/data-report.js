let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast= bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast= bootstrap.Toast.getOrCreateInstance(success_message);

var group_name=localStorage.getItem("GroupNameValue")
if(group_name==""||group_name==null)
{
	group_name="ALL";
}


let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;
}
update_data_table(device_id, "LATEST", "");


let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
	device_id = document.getElementById('device_id').value;
	document.getElementById('view_all_group_device').checked=false;
	document.getElementById('pre-loader').style.display = 'block';
	update_data_table(device_id, "LATEST", "");

	if (typeof update_frame_time === "function") {
		update_frame_time(device_id);
	} 

});

let group_list = document.getElementById('group-list');
group_list.addEventListener('change', function() {
	document.getElementById('view_all_group_device').checked=false;
});


let view_all = document.getElementById('view_all_group_device');
view_all.addEventListener('change', function() {
	document.getElementById('search_date').value = "";
	if (this.checked) {		
		document.getElementById('pre-loader').style.display = 'block';
		update_all_group_data_table();
		
	} else {
		let device_id = document.getElementById('device_id').value;
		update_data_table(device_id, "LATEST", "");
		document.getElementById('pre-loader').style.display = 'block';
	}
});

//////////////////////////////////////////////////////////////////////////////

document.addEventListener('DOMContentLoaded', function() {
	update_frame_time(device_id); 
});
setInterval(refresh_data, 20000);
function refresh_data() {
	let device_id = document.getElementById('device_id').value;
	if (typeof update_frame_time === "function") {
		update_frame_time(device_id);
	} 
	let date_value=document.getElementById('search_date').value;
	if( date_value=="")
	{
		
		view_all = document.getElementById('view_all_group_device').checked
		if (view_all) {		
			update_all_group_data_table();
		} 
		else 
		{
			var scrollPosition = document.querySelector('.table-responsive').scrollTop;
			if(scrollPosition<=5)
			{
				update_data_table(device_id, "LATEST", "");
			}
		}
	}
}

//////////////////////////////////////////////////////////////////////////////


function search_records()
{

	let device_id = document.getElementById('device_id').value;
	let searched_date = document.getElementById('search_date').value;
	searched_date=searched_date.trim();	
	if (!document.getElementById('view_all_group_device').checked) {
		if(searched_date!=null&&searched_date!="")
		{

			update_data_table(device_id, "DATE", searched_date);

		}
		else
		{
			update_data_table(device_id, "LATEST", "");
		}
	}
	else
	{
		document.getElementById('search_date').value = "";
		update_all_group_data_table();
	}
	document.getElementById('pre-loader').style.display = 'block';

}

function add_more_records() {
	if (!document.getElementById('view_all_group_device').checked) {
		var device_id = document.getElementById('device_id').value;
		var row_cont = document.getElementById('frame_data_table').getElementsByTagName('tr').length;
		var date_id = document.querySelector('#frame_data_table tr:last-child td:nth-child(1)').innerHTML;
		if((row_cont>1 ) && (date_id.indexOf("Found")==-1))
		{
			var date_time = document.querySelector('#frame_data_table tr:last-child td:nth-child(2)').innerHTML;
			if(device_id!="")
			{

				document.getElementById('pre-loader').style.display = 'block';
				$.ajax({
					type: "POST",
					url: '../data-report/code/frame_data_table.php',
					traditional: true,
					data: { D_ID: device_id, RECORDS:"ADD", DATE_TIME:date_time},
					dataType: "json",
					success: function(response) {
						$("#pre-loader").css('display', 'none');
						$("#frame_data_table").append(response[0]);

					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#pre-loader").css('display', 'none');
						error_message_text.textContent="Error getting the data";
						error_toast.show();
					}
				});
			}
		}
		else
		{
			error_message_text.textContent="Records are not found";
			error_toast.show();
		}
	}


};




function update_data_table(device_id, records, searched_date ){

	$.ajax({
		type: "POST",
		url: '../data-report/code/frame_data_table.php',
		traditional: true,
		data: { D_ID: device_id, RECORDS:records, DATE:searched_date },
		dataType: "json",
		success: function(response) {
			$("#pre-loader").css('display', 'none');
			$("#frame_data_table_header").html("");
			if(response[1]=="3PH")
			{				
				$("#frame_data_table_header").html('<tr class="header-row-1"> <th class="table-header-row-1"></th> <th class="table-header-row-1 col-size-1" >Updated at</th> <th class="table-header-row-1">ON/OFF Status</th> <th class="table-header-row-1">Load</th> <th class="table-header-row-1" colspan="3">Phase Voltages (Volts)</th> <th class="table-header-row-1" colspan="3">Phase Currents (Amps)</th> <th class="table-header-row-1" colspan="4">KW</th> <th class="table-header-row-1" colspan="4">KVA</th> <th class="table-header-row-1" colspan="2">Energy (Units)</th> <th class="table-header-row-1" colspan="3">Power Factor</th> <th class="table-header-row-1" colspan="3">Frequency (Hz)</th> <th class="table-header-row-1">Battery</th> <th class="table-header-row-1">Signal Level</th> <th class="table-header-row-1">Location</th> </tr> <tr class="header-row-2"> <th class="table-header-row-2">Device Id</th> <th class="table-header-row-2 col-size-1"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Status</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">kWh</th> <th class="table-header-row-2">kVAh</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Voltage(mV)</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th>  </tr>');
			}
			else if(response[1]=="1PH"){
				
				$("#frame_data_table_header").html('<tr class="header-row-1"> <th class="table-header-row-1"></th> <th class="table-header-row-1 col-size-1" >Updated at</th> <th class="table-header-row-1">ON/OFF Status</th> <th class="table-header-row-1">Load</th> <th class="table-header-row-1" colspan="1">Voltages (Volts)</th> <th class="table-header-row-1" colspan="1">Currents (Amps)</th> <th class="table-header-row-1" colspan="1">KW</th> <th class="table-header-row-1" colspan="1">KVA</th> <th class="table-header-row-1" colspan="2">Energy (Units)</th> <th class="table-header-row-1" colspan="1">Power Factor</th> <th class="table-header-row-1" colspan="1">Frequency (Hz)</th> <th class="table-header-row-1">Battery</th> <th class="table-header-row-1">Signal Level</th><th class="table-header-row-1">Location</th> </tr> <tr class="header-row-2"> <th class="table-header-row-2">Device Id</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Status</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">kWh</th> <th class="table-header-row-2">kVAh</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Voltage(mV)</th> <th class="table-header-row-2"></th>  <th class="table-header-row-2"></th> </tr>');
			}

			$("#frame_data_table").html("");
			$("#frame_data_table").html(response[0]);

		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#pre-loader").css('display', 'none');
			error_message_text.textContent="Error getting the data";
			error_toast.show();
		}
	});
}

function update_all_group_data_table(){

	$.ajax({
		type: "POST",
		url: '../data-report/code/all-group-data.php',
		traditional: true,		
		dataType: "json",
		success: function(response) {
			$("#pre-loader").css('display', 'none');
			$("#frame_data_table_header").html("");
				console.log(response[1]);
			 if(response[1]=="1PH"){
				
				$("#frame_data_table_header").html('<tr class="header-row-1"> <th class="table-header-row-1"></th> <th class="table-header-row-1 col-size-1" >Updated at</th> <th class="table-header-row-1">ON/OFF Status</th> <th class="table-header-row-1">Load</th> <th class="table-header-row-1" colspan="1">Voltages (Volts)</th> <th class="table-header-row-1" colspan="1">Currents (Amps)</th> <th class="table-header-row-1" colspan="1">kW</th> <th class="table-header-row-1" colspan="1">kVA</th> <th class="table-header-row-1" colspan="2">Energy (Units)</th> <th class="table-header-row-1" colspan="1">Power Factor</th> <th class="table-header-row-1" colspan="1">Frequency (Hz)</th> <th class="table-header-row-1">Battery</th> <th class="table-header-row-1">Signal Level</th><th class="table-header-row-1">Location</th> </tr> <tr class="header-row-2"> <th class="table-header-row-2">Device Id</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Status</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">kWh</th> <th class="table-header-row-2">kVAh</th> <th class="table-header-row-2"></th> <th class="table-header-row-2"></th> <th class="table-header-row-2">Voltage(mV)</th> <th class="table-header-row-2"></th>  <th class="table-header-row-2"></th> </tr>');
			}
			else{

				$("#frame_data_table_header").html('<tr class="header-row-1"> <th class="table-header-row-1"></th> <th class="table-header-row-1 col-size-1" >Updated at</th> <th class="table-header-row-1">ON/OFF Status</th> <th class="table-header-row-1">Load</th> <th class="table-header-row-1" colspan="3">Phase Voltages (Volts)</th> <th class="table-header-row-1" colspan="3">Phase Currents (Amps)</th> <th class="table-header-row-1" colspan="4">KW</th> <th class="table-header-row-1" colspan="4">KVA</th> <th class="table-header-row-1" colspan="2">Energy (Units)</th> <th class="table-header-row-1" colspan="3">Power Factor</th> <th class="table-header-row-1" colspan="3">Frequency (Hz)</th> <th class="table-header-row-1">Battery</th><th class="table-header-row-1">Signal Level</th> <th class="table-header-row-1">Location</th> </tr> <tr class="header-row-2"> <th class="table-header-row-2">Device Id</th> <th class="table-header-row-2 col-size-1"></th> <th class="table-header-row-2 col-size-1"></th> <th class="table-header-row-2">Status</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Total</th> <th class="table-header-row-2">kWh</th> <th class="table-header-row-2">kVAh</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">R/Single Phase</th> <th class="table-header-row-2">Y</th> <th class="table-header-row-2">B</th> <th class="table-header-row-2">Voltage(mV)</th> <th class="table-header-row-2"></th>  <th class="table-header-row-2"></th> </tr>');
			}
			
			$("#frame_data_table").html("");
			$("#frame_data_table").html(response[0]);

		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#pre-loader").css('display', 'none');
			error_message_text.textContent="Error getting the data";
			error_toast.show();
		}
	});
}

var elements = document.getElementById('update_time').style.display = 'none';

document.addEventListener('DOMContentLoaded', function() {
	var deviceIdSection = document.getElementById('device_id_section');
	if (deviceIdSection) {
		deviceIdSection.style.setProperty('display', 'none', 'important');
	}
});

// window.onload = function() {
// 	document.getElementById('device_id').style.display = 'none';
// 	document.getElementById('device_id_section').style.display = 'none';

// };

// document.addEventListener('DOMContentLoaded', function() {
// 	document.getElementById('device_id').style.display = 'none';
// 	document.getElementById('device_id_section').style.display = 'none';
// });




function handleGroupChange() {
	const group_name = document.getElementById('group_name');
	const selectedValue = group_name.value;

	if (selectedValue === '_add_new_group') {

		$('#group-create-component').modal('show')
	}
}

function updateArea() {
	const stateSelect = document.getElementById('state');
	const districtSelect = document.getElementById('district');
	const townInput = document.getElementById('town').value.trim();
	const areaInput = document.getElementById('group').value.trim();

	const state = stateSelect.value;
	const district = districtSelect.value;

	let isValid = true;

	// Validate state
	if (state === '') {
		alert('Please select a state.');
		isValid = false;
	} else if (state === 'add-state' && document.getElementById('other-state').value.trim() === '') {
		alert('Please enter a state.');
		isValid = false;
	}

	// Validate district
	if (district === '') {
		alert('Please select a district.');
		isValid = false;
	} else if (district === 'add-district' && document.getElementById('other-district').value.trim() === '') {
		alert('Please enter a district.');
		isValid = false;
	}

	// Validate town
	if (townInput === '') {
		alert('Please enter a town or city.');
		isValid = false;
	}

	// Validate area
	if (areaInput === '') {
		alert('Please enter an area or group.');
		isValid = false;
		document.getElementById('group').classList.add('border-danger');
	}

	if (isValid) {
		document.getElementById('group').classList.remove('border-danger');

		if (areaInput !== "_NEW_GROUP_ADDED") {
			// Add the new area to the group_name select
			const groupSelect = document.getElementById('group_name');

			// Check if the area already exists
			let optionExists = false;
			for (let i = 0; i < groupSelect.options.length; i++) {
				if (groupSelect.options[i].text === areaInput && groupSelect.options[i].value === "_NEW_GROUP_ADDED") {
					optionExists = true;
					break;
				} else {
					if (groupSelect.options[i].value === "_NEW_GROUP_ADDED") {
						groupSelect.remove(i);
					}

				}
			}

			// Add new option if it doesn't already exist
			if (!optionExists) {
				const newOption = document.createElement('option');
				newOption.value = "_NEW_GROUP_ADDED";
				newOption.textContent = areaInput;
				groupSelect.appendChild(newOption);
			}

			// Select the new option
			groupSelect.value = "_NEW_GROUP_ADDED";
			$('#group-create-component').modal('hide');


			// Find the option with the specified value
			let value = "_add_new_group";
			let optionToMove = null;
			for (let i = 0; i < groupSelect.options.length; i++) {
				if (groupSelect.options[i].value === value) {
					optionToMove = groupSelect.options[i];
					groupSelect.remove(i); // Remove the option
					break;
				}
			}

			// Add the option back at the end if it was found
			if (optionToMove) {
				groupSelect.add(optionToMove);
			}

			// Optionally, you can handle the change event
			handleGroupChange();
		} else {
			document.getElementById('group').classList.add('border-danger');
			document.getElementById('group').value = "";

			alert("Please enter another Name, it's already reserved");
		}
	}
}
function updateDevice()
{

	let group="EXISTING";
	var multipleValues = $("#multi_selection_device_id").val() || [];
	let selected_devices = multipleValues.join(",");
	const group_id = document.getElementById('group_name');
	let group_name = group_id.value.trim();

	let state = "";
	let district = "";
	let town =  "";
	let new_group =  "";

	if(group_name==""||group_name==null||group_name=="_add_new_group")
	{
		alert("Please select Group/Area");
		return false;
	}

	if(group_name=="_NEW_GROUP_ADDED")
	{
		state = document.getElementById('state').value;
		district = document.getElementById('district').value;
		town = document.getElementById('town').value;
		
		group_name=group_id.options[group_id.selectedIndex].text;
		

		group="CREATE_NEW";

		if(state==""||state==null||district==""||district==null||town==""||town==null)
		{
			$('#group-create-component').modal('show');
			alert("Enter All field");
			return false;
		}

	}

	if (selected_devices.length > 0) {
		device_id = selected_devices;

	

		if (confirm(`Are you sure you want to update the assigned devices to the new group?`)) {
			$("#pre-loader").css('display', 'block'); 
			$.ajax({
				type: "POST",
				url: '../devices/code/create-group.php',
				traditional: true,

				data: { D_ID: device_id, GROUP:group, NEW_GROUP:group_name, STATE:state, DISTRICT:district, TOWN:town },
				dataType: "json",
				success: function(response) {
					$("#pre-loader").css('display', 'none');
					if(response.status=="success")
					{
						alert(response.message);
						var select_group_list = document.getElementById('group-list');
						var phase_selection = document.getElementById('phase-selection');
						select_group_list.innerHTML = '';
						select_group_list.appendChild(new Option('ALL', 'All-Groups/Locations')); // Add default option
						response.group_list.forEach(function(item) {
							var option = document.createElement('option');
							option.value = item.GROUP;
							option.textContent = item.GROUP;
							select_group_list.appendChild(option);
						});
						//select_group_list.dispatchEvent(new Event('change'));
						phase_selection.dispatchEvent(new Event('change'));
						$("#select_all").prop("checked", false);
						$('#multi_selection_device_id option').prop("selected", false);
						var count = $("#multi_selection_device_id :selected").length;        
						$('#selected_count').text(count);
					}
					else
					{
						alert(response.message);
					}
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
		alert("Please devices ");
		return false;
	}
}

function updateGroupDevice()
{

	var updating_group=document.getElementById('group_for_view').value;
	if(updating_group==""||updating_group==null)
	{
		alert("Please Select Group..");
		return false;

	}
	if (confirm(`Are you sure you want to update?`)) {
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			type: "POST",
			url: '../devices/code/update-group-view.php',
			traditional: true,

			data: { GROUP:updating_group },
			dataType: "json",
			success: function(response) {
				$("#pre-loader").css('display', 'none');
				if(response.status=="success")
				{
                console.log(response);
					alert(response.message);
					var select_group_list = document.getElementById('group-list');
					select_group_list.innerHTML = '';
						select_group_list.appendChild(new Option('All-Groups/Locations', 'ALL')); // Add default option
						response.group_list.forEach(function(item) {
							var option = document.createElement('option');
							option.value = item.GROUP;
							option.textContent = item.GROUP;
							select_group_list.appendChild(option);
						});
						select_group_list.dispatchEvent(new Event('change'));

					}
					else
					{
						alert(response.message);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#pre-loader").css('display', 'none');
					alert(`Error: ${textStatus}, ${errorThrown}`);
				}
			});
	}
}
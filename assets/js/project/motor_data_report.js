let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);


let device_id_list = document.getElementById('motor-list');
device_id = document.getElementById('motor-list').value;

device_id_list.addEventListener('change', function () {
    device_id = device_id_list.value;
    // Save to localStorage
    localStorage.setItem('selected_motor_id', device_id);

    document.getElementById('pre-loader').style.display = 'block';
    update_data_table(device_id, "LATEST", "");
});

document.addEventListener('DOMContentLoaded', function () {
    let saved_device_id = localStorage.getItem('selected_motor_id');

    if (saved_device_id) {
        // Try to set the saved value as selected
        let motorList = document.getElementById('motor-list');
        motorList.value = saved_device_id;

        // If the value doesn't exist in options (e.g., removed device), fallback
        if (motorList.value !== saved_device_id) {
            saved_device_id = motorList.options[0].value;
            motorList.value = saved_device_id;
        }
    } else {
        // Default to first one if nothing stored
        saved_device_id = document.getElementById('motor-list').value;
    }

    update_data_table(saved_device_id, "LATEST", "");
});

setInterval(refresh_data, 60000);
function refresh_data() {
    let device_id = document.getElementById('motor-list').value;
    // if (typeof update_frame_time === "function") {
    //     update_frame_time(device_id);
    // }
    let date_value = document.getElementById('search_date').value;
    if (date_value == "") {


        var scrollPosition = document.querySelector('.table-responsive').scrollTop;
        if (scrollPosition <= 5) {
            update_data_table(device_id, "LATEST", "");

        }
    }
}

//////////////////////////////////////////////////////////////////////////////


function search_records() {

    let device_id = document.getElementById('motor-list').value;
    let searched_date = document.getElementById('search_date').value;
    searched_date = searched_date.trim();

    if (searched_date != null && searched_date != "") {

        update_data_table(device_id, "DATE", searched_date);

    }
    else {
        update_data_table(device_id, "LATEST", "");
    }


    document.getElementById('pre-loader').style.display = 'block';

}

function add_more_records() {
    var device_id = document.getElementById('motor-list').value;
    var row_cont = document.getElementById('frame_data_table').getElementsByTagName('tr').length;
    var date_id = document.querySelector('#frame_data_table tr:last-child td:nth-child(1)').innerHTML;

    if ((row_cont > 1) && (date_id.indexOf("Found") == -1)) {
        var date_time = document.querySelector('#frame_data_table tr:last-child td:nth-child(2)').innerHTML;

        if (device_id !== "") {
            document.getElementById('pre-loader').style.display = 'block';

            $.ajax({
                type: "POST",
                url: '../data-report/code/frame_data_table.php',
                traditional: true,
                data: { D_ID: device_id, RECORDS: "ADD", DATE_TIME: date_time },
                success: function (response) {
                    $("#pre-loader").css('display', 'none');

                    // Trim and check if 'Records are not Found' is in response
                    if (response.includes("Records are not Found")) {
                        $("#pre-loader").css('display', 'none');
                        error_message_text.textContent = "Records are not found";
                        error_toast.show();
                    } else {
                        $("#frame_data_table").append(response);
                    }
                },
                error: function () {
                    $("#pre-loader").css('display', 'none');
                    error_message_text.textContent = "Error getting the data";
                    error_toast.show();
                }
            });
        }
    } else {
        error_message_text.textContent = "Records are not found";
        error_toast.show();
    }
}






function update_data_table(device_id, records, searched_date) {
    $.ajax({
        type: "POST",
        url: '../data-report/code/frame_data_table.php',
        traditional: true,
        data: { D_ID: device_id, RECORDS: records, DATE: searched_date },
        success: function (response) {
            $("#pre-loader").css('display', 'none');
            if (response.includes("Records are not Found")) {
                $("#pre-loader").css('display', 'none');
                error_message_text.textContent = "Records are not found";
                error_toast.show();
                $("#frame_data_table").html(response);

            }
            else
            $("#frame_data_table").html(response);
        },
        error: function () {
            $("#pre-loader").css('display', 'none');
            error_message_text.textContent = "Error getting the data";
            error_toast.show();
        }
    });
}


// function displayMotorData(data, append = false) {
//     const tableBody = document.getElementById('frame_data_table');

//     // If not appending, clear existing data
//     if (!append) {
//         tableBody.innerHTML = '';
//     }

//     // Check if data is empty
//     if (!data || data.length === 0) {
//         if (!append) {
//             // Show message only if it's the first load
//             tableBody.innerHTML = `
//                 <tr>
//                     <td colspan="21" class="text-center py-4">
//                         <i class="fas fa-exclamation-circle text-danger fa-2x mb-3"></i>
//                         <p class="text-danger">No records found for this motor</p>
//                     </td>
//                 </tr>
//             `;
//         } else {
//             // Show Bootstrap alert on top of the table container
//             showNoMoreRecordsNotification();
//         }
//         return;
//     }

//     // Populate table
//     data.forEach((item, index) => {
//         const row = document.createElement('tr');
//         row.className = (tableBody.rows.length + index) % 2 === 0 ? 'table-row-even' : 'table-row-odd';

//         row.innerHTML = `
//             <td class="fw-bold">${item.motor_id}</td>
//             <td>${item.date_time}</td>
//             <td class="text-center">
//                 <span class="status-badge ${item.on_off_status == 1 ? 'status-on' : 'status-off'}">
//                     <i class="fas fa-power-off me-1"></i>${item.on_off_status == 1 ? 'ON' : 'OFF'}
//                 </span>
//             </td>
//             <td>${parseFloat(item.voltage || 0).toFixed(1)}</td>
//             <td>${parseFloat(item.current || 0).toFixed(2)}</td>
//             <td>${parseFloat(item.energy_kwh || 0).toFixed(1)}</td>
//             <td>${parseFloat(item.energy_kvah || 0).toFixed(1)}</td>
//             <td>${parseFloat(item.flow_rate || 0).toFixed(2)}</td>
//             <td>${parseFloat(item.speed || 0).toFixed(3)}</td>
//             <td>${parseFloat(item.total_running_hours || 0).toFixed(1)}</td>
//         `;
//         tableBody.appendChild(row);
//     });
// }

// function showNoMoreRecordsNotification() {
//     const notifyDiv = document.getElementById('notification-area');
//     notifyDiv.innerHTML = `
//         <div class="alert alert-warning alert-dismissible fade show" role="alert">
//             <i class="fas fa-info-circle me-2"></i>
//             No more records found.
//             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//         </div>
//     `;
// }

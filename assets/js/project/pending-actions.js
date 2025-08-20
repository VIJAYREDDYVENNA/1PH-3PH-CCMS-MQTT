let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast= bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast= bootstrap.Toast.getOrCreateInstance(success_message);

let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
    device_id = document.getElementById('device_id').value;
}

update_data_table(device_id);

let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
  $("#pre-loader").css('display', 'block');
  device_id = document.getElementById('device_id').value;
  update_data_table(device_id);
  refresh_data();
});

setTimeout(refresh_data, 50);
setInterval(refresh_data, 20000);
function refresh_data() {
    if (typeof update_frame_time === "function") {
        device_id = document.getElementById('device_id').value;
        update_frame_time(device_id);
    } 
}

function btn_refresh_data() {
    $("#pre-loader").css('display', 'block'); 
    update_data_table(device_id);    
    refresh_data();
}

function cancel_update(parameter)
{
    if (confirm(`Are you sure you want to Cancel the ${parameter} Update ?`)) {
        $("#pre-loader").css('display', 'block'); 
        $.ajax({
            type: "POST",
            url: '../settings/code/pending-actions.php',
            traditional: true,
            data: { D_ID: device_id, CANCEL_PARAMTER:parameter},
            dataType: "json",
            success: function(response) {
                $("#pre-loader").css('display', 'none');

                $("#pending-action-table").html("");
                $("#pending-action-table").html(response); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#pre-loader").css('display', 'none');
                $("#pending-action-table").html("");
                error_message_text.textContent="Error getting the data";
                error_toast.show();

            }
        });
    }

}


function update_data_table(device_id ){
    $.ajax({
        type: "POST",
        url: '../settings/code/pending-actions.php',
        traditional: true,
        data: { D_ID: device_id},
        dataType: "json",
        success: function(response) {
            $("#pre-loader").css('display', 'none');

            $("#pending-action-table").html("");
            $("#pending-action-table").html(response); 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $("#pre-loader").css('display', 'none');
            $("#pending-action-table").html("");
            error_message_text.textContent="Error getting the data";
            error_toast.show();

        }
    });
}

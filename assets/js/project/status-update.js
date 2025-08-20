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
var activity  = document.getElementById('activity-selected').value;
update_data_table(device_id, "LATEST", activity);

let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
    device_id = document.getElementById('device_id').value;
    update_data_table(device_id, "LATEST", activity);
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


function getSelectedDateRange() {
    const selectedDates = window.fp.selectedDates;
    if (selectedDates.length === 2) {
        const [startDate, endDate] = selectedDates;
        return {
            startDate: startDate,
            endDate: endDate
        };
    } else {
        return null; 
    }
}

function get_data() {
    //const dateRange = getSelectedDateRange();
    activity  = document.getElementById('activity-selected').value;
    /*if (dateRange) {

        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        const startDateFormatted = dateRange.startDate.toLocaleDateString(undefined, options);
        const endDateFormatted = dateRange.endDate.toLocaleDateString(undefined, options);
        
        $("#pre-loader").css('display', 'block'); 
        update_data_table(device_id, "DATE-RANGE", activity, startDateFormatted, endDateFormatted )

    } else {*/
    $("#pre-loader").css('display', 'block'); 
    update_data_table(device_id, "LATEST", activity);

  //  }
}


function update_data_table(device_id, records, activity ){
    $.ajax({
        type: "POST",
        url: '../device-reports/code/device-settings-status.php',
        traditional: true,
        data: { D_ID: device_id, RECORDS:records, ALERT:activity},
        dataType: "json",
        success: function(response) {
            $("#pre-loader").css('display', 'none');

            $("#device_update").html("");
            $("#device_update").html(response);
            if(activity=="SAVED-SETTINGS")
            {
                $("#load_setting_btn").css('display', 'block'); 

            }
            else
            {
                $("#load_setting_btn").css('display', 'none'); 
            }


        },
        error: function(jqXHR, textStatus, errorThrown) {
            $("#pre-loader").css('display', 'none');
            error_message_text.textContent="Error getting the data";
            error_toast.show();
        }
    });
}

function readPreviousValues() {
    var row = document.querySelector('#row_count').textContent;
    if (row === "" || row === null || row === "0") {
        readIotSetValues(0, "LATEST");
    } else {
        readIotSetValues(parseInt(row), "PREV");
    }
}

function readNextValues() {
    var row = document.querySelector('#row_count').textContent;
    if (row === "" || row === null || row === "0") {
        readIotSetValues(0, "LATEST");
    } else {
        readIotSetValues(parseInt(row), "NEXT");
    }
}


function readIotSetValues(row_id, view)
{
    if(device_id!=""&&device_id!=null)
    {
        $("#loader").css('display', 'block');
        $.ajax({
            method : "POST",
            url:"../device-reports/code/calibration-values.php",
            data:{ D_ID : device_id, ROW:row_id, ROW_VIEW:view },
            traditional:true,
            dataType: "json", 
            success : function(result){
                $("#loader").css('display', 'none');
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
                $("#loader").css('display', 'none');

                error_message_text.textContent="Error getting the data"
                error_toast.show();
            },
            failure: function()
            {
                $("#loader").css('display', 'none');

                error_message_text.textContent="failure getting the data"
                error_toast.show();
            }
        });
    }
}


// Click event for the save settings button
document.querySelector('#save_settings').addEventListener('click', function() {
    checkRequiredInputs();
});

// Click event to reset border style for .validate_input elements
document.querySelectorAll('.validate_input').forEach(function(element) {
    element.addEventListener('click', function() {
        this.style.border = "1px solid #ddd";
    });
});

// Function to check required inputs
function checkRequiredInputs() {
    let frame = "";
    let count = true;

    document.querySelectorAll('.validate_input').forEach(function(element) {
        const value = element.value.trim();

        if (value === ""||value === null) {
            count = false;
            element.style.border = "1px solid red";
        } else {
            if (!isNumeric(value)) {
                count = false;
                element.style.border = "2px solid red";
            }
        }
        
        frame += value + ";";
    });
    
    if (count) {

        // Uncomment and adjust the following block if AJAX request is needed


        if (device_id) {

            if (confirm("Are you sure?")) {

                $.ajax({
                    method : "POST",
                    url:"../device-reports/code/save_calib_values.php",
                    data:{ D_ID : device_id,DATA: frame },
                    traditional:true,
                    dataType: "json", 
                    success : function(result){
                        success_message_text.textContent=result.message;
                        success_toast.show();                      

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $("#pre-loader").css('display', 'none');
                        error_message_text.textContent="Error getting the data";
                        error_toast.show();
                    }
                });
            }
        }

    } else {

       error_message_text.textContent="Please check all values";
       error_toast.show();
       return false;
   }
}

// Helper function to check if a value is numeric
function isNumeric(value) {
    return !isNaN(value) && isFinite(value);
}

// Blur event to validate .form-control elements
document.querySelectorAll('.validate_input').forEach(function(element) {
    element.addEventListener('blur', function() {
        const value = this.value.trim();
        if (value === "") {
            this.style.border = "1px solid red";
        } else {
            if (!isNumeric(value)) {
                this.style.border = "2px solid red";
            }
        }
    });
});



function read_iot_settings(){
    
    var formData = new FormData();
    formData.append('PARAMETER_VALUE', "READ");
    formData.append('UPDATED_STATUS', 'READ_SETTINGS');
    formData.append('D_ID', device_id);
    if (confirm(`Are you sure ?`)) {
        $("#pre-loader").css('display', 'block'); 
        $.ajax({
            type: "POST",
            url: '../settings/code/iot-settings.php',                   
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                $("#pre-loader").css('display', 'none');
                alert(response.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#pre-loader").css('display', 'none');
                alert(`Error: ${textStatus}, ${errorThrown}`);
            }
        });
    }
    
}


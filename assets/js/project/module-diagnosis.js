let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);

initializeDateRangePicker("#date-range", 29);

let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
    device_id = document.getElementById('device_id').value;
}
var selected_parameter = document.getElementById('selecting_paramter').value;
update_data_table(device_id, "LATEST", selected_parameter, "", "");

let device_id_list = document.getElementById('device_id');
device_id_list.addEventListener('change', function () {
    $("#pre-loader").css('display', 'block');
    device_id = document.getElementById('device_id').value;
    update_data_table(device_id, "LATEST", selected_parameter, "", "");
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
    const dateRange = getSelectedDateRange();
    selected_parameter = document.getElementById('selecting_paramter').value;
    if (dateRange) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        const startDateFormatted = formatDateToYMD(dateRange.startDate);
        const endDateFormatted = formatDateToYMD(dateRange.endDate);
        // console.log(startDateFormatted+"--"+endDateFormatted);

        $("#pre-loader").css('display', 'block');
        update_data_table(device_id, "DATE-RANGE", selected_parameter, startDateFormatted, endDateFormatted)

    } else {
        $("#pre-loader").css('display', 'block');
        update_data_table(device_id, "LATEST", selected_parameter, "", "");

    }
}

function formatDateToYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
function update_data_table(device_id, records, selected_parameter, start_date, end_date) {
    console.log(selected_parameter);
    $.ajax({
        type: "POST",
        url: '../device-reports/code/module-diagnosis.php',
        traditional: true,
        data: { D_ID: device_id, RECORDS: records, MODULE: selected_parameter, START_DATE: start_date, END_DATE: end_date },
        dataType: "json",
        success: function (response) {
            $("#pre-loader").css('display', 'none');

            $("#diagnosis_table").html("");
            $("#diagnosis_table").html(response);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#pre-loader").css('display', 'none');
            $("#diagnosis_table").html("");
            error_message_text.textContent = "Error getting the data";
            error_toast.show();
        }
    });
}

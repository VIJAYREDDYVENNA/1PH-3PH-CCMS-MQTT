// Initialize variables
let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);

// Set up refresh button event listener
document.getElementById('refresh-btn').addEventListener('click', function () {
    let searched_date = document.getElementById('search_date').value;
    searched_date = searched_date.trim();

    if (searched_date != null && searched_date != "") {
        update_data_table("DATE", searched_date);
    } else {
        update_data_table("LATEST", "");
    }

    document.getElementById('pre-loader').style.display = 'block';
    success_message_text.textContent = "Data refreshed successfully";
    success_toast.show();
});

// Initialize table with latest data
document.addEventListener('DOMContentLoaded', function () {
    update_data_table("LATEST", "");
});

// Auto refresh data every 30 seconds
// setInterval(refresh_data, 30000);

/**
 * Refreshes the data table with latest records
 */
function refresh_data() {
    update_data_table("LATEST", "");
}

/**
 * Search records based on the selected date
 */
function search_records() {
    let searched_date = document.getElementById('search_date').value;
    searched_date = searched_date.trim();

    if (searched_date != null && searched_date != "") {
        update_data_table("DATE", searched_date);
    } else {
        update_data_table("LATEST", "");
    }

    document.getElementById('pre-loader').style.display = 'block';
}

/**
 * Add more records to the existing table
 */
function add_more_records() {
    const tableBody = document.getElementById('crc_data_body');
    if (!tableBody || tableBody.rows.length === 0) {
        error_message_text.textContent = "No records available to load more";
        error_toast.show();
        return;
    }

    // Check if there's a "No records found" message
    if (tableBody.innerHTML.includes("No records found") || tableBody.innerHTML.includes("No data available")) {
        error_message_text.textContent = "No records available to load more";
        error_toast.show();
        return;
    }

    const lastRow = tableBody.rows[tableBody.rows.length - 1];
    if (!lastRow) return;

    const lastDateTime = lastRow.querySelector('td:nth-child(3)').innerText;

    document.getElementById('pre-loader').style.display = 'block';

    $.ajax({
        type: "POST",
        url: '../crc-failed-data/code/update_crc_table.php',
        traditional: true,
        data: { RECORDS: "ADD", DATE_TIME: lastDateTime },
        dataType: "json",
        success: function (response) {
            // Remove loading overlay
            $("#pre-loader").css('display', 'none');

            if (response && response.length > 0 && !response[0].includes("No records found")) {
                // Append new rows to the table
                $(tableBody).append(response[0]);
                // Update row numbers
                updateRowNumbers();

                success_message_text.textContent = "Additional records loaded";
                success_toast.show();
            } else {
                error_message_text.textContent = "No additional records found";
                error_toast.show();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Remove loading overlay
            $("#pre-loader").css('display', 'none');

            error_message_text.textContent = "Error getting the data";
            error_toast.show();
            console.error("AJAX Error:", textStatus, errorThrown);
        }
    });
}

/**
 * Updates row numbers in the table
 */
function updateRowNumbers() {
    const rows = document.querySelectorAll('#crc_data_body tr');
    rows.forEach((row, index) => {
        const snoCell = row.querySelector('td:first-child');
        if (snoCell) {
            snoCell.textContent = (index + 1).toString();
        }
    });
}

/**
 * Toggle the visibility of full frame data
 * @param {number} id - The row ID to toggle
 */
function toggleFrameData(id) {
    const fullFrame = document.getElementById(`full-frame-${id}`);
    const toggleBtn = document.getElementById(`toggle-btn-${id}`);

    if (fullFrame.style.display === 'none' || !fullFrame.style.display) {
        fullFrame.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-up me-1"></i>Show Less';
    } else {
        fullFrame.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-down me-1"></i>Show More';
    }
}

/**
 * Update the data table with records based on the specified criteria
 * @param {string} records - The type of records to fetch (LATEST, DATE, ADD)
 * @param {string} searched_date - The date to search for (if applicable)
 */
function update_data_table(records, searched_date) {
    $.ajax({
        type: "POST",
        url: '../crc-failed-data/code/update_crc_table.php',
        traditional: true,
        data: { RECORDS: records, DATE: searched_date },
        dataType: "json",
        success: function (response) {
            $("#pre-loader").css('display', 'none');

            if (response && response.length > 0) {
                // Update table with new data
                $("#crc_data_body").html(response[0]);

                // If no data was found
                if (response[0].includes("No records found")) {
                    document.getElementById('btn_add_more').disabled = true;
                    error_message_text.textContent = "No  records found";
                    error_toast.show();
                } else {
                    document.getElementById('btn_add_more').disabled = false;

                    // Setup event listeners for "show more" buttons
                    const showMoreButtons = document.querySelectorAll('.show-more-btn');
                    showMoreButtons.forEach(btn => {
                        btn.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            toggleFrameData(id);
                        });
                    });
                }
            } else {
                $("#crc_data_body").html('<tr><td colspan="3" class="text-center no-data-message">No data available</td></tr>');
                document.getElementById('btn_add_more').disabled = true;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#pre-loader").css('display', 'none');

            error_message_text.textContent = "Error getting the data";
            error_toast.show();
            console.error("AJAX Error:", textStatus, errorThrown);
            $("#crc_data_body").html('<tr><td colspan="3" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
        }
    });
}
function downTimeView(){
    var downTimeView=new bootstrap.Modal(document.getElementById("down-time-view")).show();
   }

   function downTimeView(deviceId) {
        // Set the device ID in the modal
        document.getElementById('deviceIdDisplay').innerText = deviceId;

        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('down-time-view'));
        modal.show();
    }

    function checkTableData() {
        var tableBody = document.getElementById('tableBody');
        var noDataRow = document.getElementById('noDataRow');
        if (tableBody.children.length === 1 && tableBody.children[0].id === 'noDataRow') {
            noDataRow.style.display = 'table-row'; 
        } else {
            noDataRow.style.display = 'none';
        }
    }
    checkTableData();

//Buttons Script
    function showTodayData() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        filterTableData(today, today);
    }

    // Function to show all data
    function showAllData() {
        filterTableData('', ''); // Empty dates will show all data
    }

    // Function to filter table data by date range
    function filterByDateRange() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        filterTableData(startDate, endDate);
    }

    // Function to filter table data by date range
    function filterTableData(startDate, endDate) {
        const table = document.getElementById('dataTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let dataAvailable = false;

        // Hide all rows initially
        for (let i = 0; i < rows.length; i++) {
            const dateCell = rows[i].getElementsByTagName('td')[1]; // Date is in the second column
            if (dateCell) {
                const rowDate = dateCell.textContent;
                if ((startDate === '' || rowDate >= startDate) && (endDate === '' || rowDate <= endDate)) {
                    rows[i].style.display = ''; // Show the row
                    dataAvailable = true;
                } else {
                    rows[i].style.display = 'none'; // Hide the row
                }
            }
        }

        // Show alert if no data is available
        if (!dataAvailable) {
            alert('No data available in the given date range.');
            showAllData();
        }
    }

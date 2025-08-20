document.getElementById('search-button').addEventListener('click', function() {
    const selectedDate = document.getElementById('simcom-status-date').value;
    const table = document.getElementById('dataTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');

    // Convert selectedDate to a Date object for easy comparison
    const selectedDateObj = new Date(selectedDate);

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        const updatedOnCell = cells[1];  // Correct column for 'Updated On'

        const updatedOnDate = updatedOnCell.textContent.split(' ')[1];
        const updatedOnDateObj = new Date(updatedOnDate.split('-').reverse().join('-'));

        if (selectedDateObj.getTime() === updatedOnDateObj.getTime()) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
});
document.getElementById('search-button').addEventListener('click', function () {
    const selectedDate = document.getElementById('simcom-select-date').value;
    const table = document.getElementById('dataTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');

    // Convert selectedDate to a Date object for easy comparison
    const selectedDateObj = new Date(selectedDate);

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        const failTimeCell = cells[0];
        const serverTimeCell = cells[1];

        const failTime = failTimeCell.textContent.split(' ')[1];
        const serverTime = serverTimeCell.textContent.split(' ')[1];

        const failTimeDateObj = new Date(failTime.split('-').reverse().join('-'));
        const serverTimeDateObj = new Date(serverTime.split('-').reverse().join('-'));

        if (selectedDateObj.getTime() === failTimeDateObj.getTime() || selectedDateObj.getTime() === serverTimeDateObj.getTime()) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
});
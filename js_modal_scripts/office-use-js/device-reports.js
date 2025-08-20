function performSearch() {
    // Get the search input value
    let input = document.getElementById('searchInput').value.toLowerCase();
    let table = document.getElementById('dataTable');
    let rows = table.getElementsByTagName('tr');

    // Loop through all table rows and hide those that don't match the search query
    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        let cells = rows[i].getElementsByTagName('td');
        let match = false;

        // Check if any cell in the row matches the search input
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerHTML.toLowerCase().indexOf(input) > -1) {
                match = true;
                break;
            }
        }

        // Hide the row if no match found, otherwise show it
        if (match) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
function SimListSearch() {
    var input = document.getElementById("deviceListInput").value.toLowerCase().trim();
    var rows = document.querySelectorAll(".SimListSearch tbody tr");

    rows.forEach(row => {
        var cells = row.querySelectorAll("td");

        // Check the second column (index 1)
        if (cells[1] && cells[1].innerText.toLowerCase().includes(input)) {
            row.style.display = ""; // Show the row
            row.classList.add("highlight");
        } else {
            row.style.display = "none"; // Hide the row
            row.classList.remove("highlight");
        }
    });
}


// Attach event listener for input
document.getElementById("deviceListInput").addEventListener("input", SimListSearch);
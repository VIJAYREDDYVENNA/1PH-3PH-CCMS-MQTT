//search script addnewuser
/*function searchTable() {
    var input = document.getElementById("searchInput").value.toLowerCase().trim();
    var rows = document.querySelectorAll(".resulttable tbody tr");

    rows.forEach(row => {
        var cells = row.querySelectorAll("td");
        var match = false;

        cells.forEach(cell => {
            if (cell.innerText.toLowerCase().includes(input)) {
                match = true;
            }
        });

        if (match) {
            row.style.display = ""; // Show matching rows
            row.classList.add("highlight");
        } else {
            row.style.display = "none"; // Hide non-matching rows
            row.classList.remove("highlight");
        }
    });
}*/
try{
document.getElementById("searchInput").addEventListener("input", searchTable);
}
catch (e) {
   
}

//serch script devicehandlesearch
function devicehandleSearch() {
    var input = document.getElementById("devicehandlesearch").value.toLowerCase().trim();
    var rows = document.querySelectorAll(".devicehandlesearch tbody tr");

    rows.forEach(row => {
        var cells = row.querySelectorAll("td");
        var match = false;

        cells.forEach(cell => {
            if (cell.innerText.toLowerCase().includes(input)) {
                match = true;
            }
        });

        if (match) {
            row.style.display = ""; // Show matching rows
            row.classList.add("highlight");
        } else {
            row.style.display = "none"; // Hide non-matching rows
            row.classList.remove("highlight");
        }
    });
}
try{
document.getElementById("devicehandlesearch").addEventListener("input", devicehandleSearch);
}
catch (e) {
   
}


//serch script addButtonSearch
function addButtonSearch() {
    var input = document.getElementById("addButtonSearch").value.toLowerCase().trim();
    var rows = document.querySelectorAll(".addButtonSearch tbody tr");

    rows.forEach(row => {
        var cells = row.querySelectorAll("td");
        var match = false;

        cells.forEach(cell => {
            if (cell.innerText.toLowerCase().includes(input)) {
                match = true;
            }
        });

        if (match) {
            row.style.display = ""; // Show matching rows
            row.classList.add("highlight");
        } else {
            row.style.display = "none"; // Hide non-matching rows
            row.classList.remove("highlight");
        }
    });
}
try{
document.getElementById("addButtonSearch").addEventListener("input", addButtonSearch);
}
catch (e) {
   
}
var interval_Id1 =interval_Id ;
//search script device list dastboard
function deviceListSearch() {
    clearInterval(interval_Id);
    var input = document.getElementById("deviceListInput").value.toLowerCase().trim();
    
    // Only start searching if input has at least 3 characters
    // if (input.length < 4) {
    //     document.querySelectorAll(".deviceListSearch tbody tr").forEach(row => {
    //         row.style.display = ""; // Show all rows when input is less than 3 characters
    //         row.classList.remove("highlight");
    //     });
    //     return;
    // }

    var rows = document.querySelectorAll(".deviceListSearch tbody tr");

    rows.forEach(row => {
        var cells = row.querySelectorAll("td");
        var match = false;

        cells.forEach(cell => {
            if (cell.innerText.toLowerCase().includes(input)) {
                match = true;
            }
        });

        if (match) {
            row.style.display = ""; // Show matching rows
            row.classList.add("highlight");
        } else {
            row.style.display = "none"; // Hide non-matching rows
            row.classList.remove("highlight");
        }
    });
}

document.getElementById("deviceListInput").addEventListener("input", function () {
   // Call the search function on every input change

    // Restart the interval if input is cleared
    if (this.value.trim() === "") {
        // clearInterval(interval_Id); // Clear any existing interval
        let group_name = group_list.value;

        refresh_data(group_name);

        interval_Id1 = setInterval(refresh_data, 60000); // Restart interval

    }
});

// Add event listener for input events
// try {
//     document.getElementById("deviceListInput").addEventListener("input", deviceListSearch);
// } catch (e) {
//     console.error("Error attaching event listener: ", e);
// }




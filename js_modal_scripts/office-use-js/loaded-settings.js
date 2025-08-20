function openSettingsModal(){
    var openLoadedSettigns=new bootstrap.Modal(document.getElementById("loaded_settings")).show();
}


function updateRowCount() {
const table = document.getElementById('dataTable');
const rowCount = table.tBodies[0].rows.length;
document.getElementById('rowCount').textContent = rowCount;
}
document.addEventListener('DOMContentLoaded', updateRowCount);
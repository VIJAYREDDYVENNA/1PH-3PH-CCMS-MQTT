document.addEventListener('DOMContentLoaded', function() {
  const groupListElement = document.getElementById('group-list');
  const phaseSelection = document.getElementById('phase-selection');
  const deviceIdDropdown = document.getElementById('device_id');
  const multipleList = document.getElementById('multi_selection_device_id');

  if (groupListElement) {
    groupList();
  }


  if(phaseSelection)
  {
    phaseSelectionUpdate();
  }



  if (deviceIdDropdown) {
    /////////////////////////////////////////////////////////////////////////
    const urlParams = new URLSearchParams(window.location.search);

    const deviceId = urlParams.get('id');
    const selectElement = document.getElementById('device_id');

    if (deviceId) {
      selectElement.value = deviceId;
      selectElement.addEventListener('change', handleDeviceIdChange);
      handleDeviceIdChange();
    }
    else
    {
      setDeviceId();
      deviceIdDropdown.addEventListener('change', handleDeviceIdChange);
      const deviceListid = localStorage.getItem("SELECTED_ID");
      if(deviceListid==null||deviceListid=="")
      {
        handleDeviceIdChange();
      }
    }

  }

  if (groupListElement) {
    groupListElement.addEventListener('change', changeGroupList);
  }
  if (phaseSelection) {
    phaseSelection.addEventListener('change', phaseSelectionList);
  }

  if (multipleList) {
    const script = document.createElement('script');
    script.src = '../assets/js/project/multi-selection.js';
    document.head.appendChild(script);
  }

  function setDeviceId() {        
    const selected_list = localStorage.getItem("Devive_ID_Selection");   
    if (selected_list !== null) {
      deviceIdDropdown.selectedIndex = parseInt(selected_list, 10);
    }
  }

  function handleDeviceIdChange() {
    const indexDeviceList = deviceIdDropdown.selectedIndex;
    const deviceListValue = deviceIdDropdown.value;
    localStorage.setItem("Devive_ID_Selection", indexDeviceList);
    localStorage.setItem("SELECTED_ID", deviceListValue);
  }

  function groupList() {        
    const groupListName = localStorage.getItem("GroupName");
    if (groupListName !== null) {
      groupListElement.selectedIndex = parseInt(groupListName, 10);
    }
  }

  function  phaseSelectionUpdate() {        
    const SelectedPhaseID = localStorage.getItem("SelectedPhase");
    
    if (SelectedPhaseID !== null) {

      phaseSelection.selectedIndex = parseInt(SelectedPhaseID, 10);
    }
    else
    {
      const indexSelectedPhase = phaseSelection.selectedIndex;
      localStorage.setItem("SelectedPhase", indexSelectedPhase);



    }

  }

  function phaseSelectionList()
  {
    const indexSelectedPhase = phaseSelection.selectedIndex;
    localStorage.setItem("SelectedPhase", indexSelectedPhase);

    phaseWiseDataList()

  }


  function phaseWiseDataList()
  {
    $("#pre-loader").css('display', 'block');
    const PhaseSelection = phaseSelection.value;
    fetch('../common-files/phase_change_update.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({PHASE: PhaseSelection})
    })
    .then(response => response.json())
    .then(data => {
      if (data !== "FAIL" && Array.isArray(data)) {
        updateDeviceDropdownGroup(data[1]);
        phaseLocalStorageReset();
        groupListElement.dispatchEvent(new Event('change'));

        // if (deviceIdDropdown) {
        //   updateDeviceDropdown(data[0]);
        //   phaseLocalStorageReset();

        // }
      }
      $("#pre-loader").css('display', 'none');
    })
    .catch(error => {
     $("#pre-loader").css('display', 'none');
     alert("Error loading data!");
     console.error('Error:', error);
   });
  }



  function changeGroupList() {
   $("#pre-loader").css('display', 'block');
   const groupListName = groupListElement.value;
   fetch('../common-files/group_change_update.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({GROUP_ID: groupListName})
  })
   .then(response => response.json())
   .then(data => {
    if (data !== "FAIL" && Array.isArray(data)) {

      if (deviceIdDropdown) {
        updateDeviceDropdown(data);
        localStorageReset();
        deviceIdDropdown.dispatchEvent(new Event('change'));
      }
      else
      {

        localStorage.setItem("Devive_ID_Selection", 0);
        localStorage.setItem("SELECTED_ID", data[0].D_ID);
        localStorageReset();
      }
    }
    $("#pre-loader").css('display', 'none');
  })
   .catch(error => {
     $("#pre-loader").css('display', 'none');
     alert("Error loading data!");
     console.error('Error:', error);
   });
 }

 function updateDeviceDropdown(data) {
  deviceIdDropdown.innerHTML = '';

  if (multipleList) {
   multipleList.innerHTML = '';
 }

 data.forEach(id_list => {
  const option = document.createElement('option');
  const option_multi = document.createElement('option');
  option.value = id_list.D_ID;    
  option_multi.value = id_list.D_ID;    
  option.textContent = id_list.D_NAME;
  option_multi.textContent = id_list.D_NAME;
  deviceIdDropdown.appendChild(option);

  if (multipleList) {
    multipleList.appendChild(option_multi);
  }
});

 deviceIdDropdown.selectedIndex = 0;



}


function updateDeviceDropdownGroup(data) {
  groupListElement.innerHTML = '';

  
  const option = document.createElement('option');
  const option_multi = document.createElement('option');
  option.value = "ALL"; 
  option.textContent = "All-Groups/Locations";
  groupListElement.appendChild(option);

  data.forEach(id_list => {
    const option = document.createElement('option');
    const option_multi = document.createElement('option');
    option.value = id_list.GROUP; 
    option.textContent = id_list.GROUP;
    groupListElement.appendChild(option);


  });

  groupListElement.selectedIndex = 0;
}

function localStorageReset() {

  const indexGroupList = groupListElement.selectedIndex;
  const groupListValue = groupListElement.value;
  localStorage.setItem("GroupName", indexGroupList);
  localStorage.setItem("GroupNameValue", groupListValue);
  localStorage.setItem("Devive_ID_Selection", 0);  

  if (deviceIdDropdown)
  {
    deviceIdDropdown.selectedIndex = 0;
  }
}


function phaseLocalStorageReset() {
  /*groupListElement.selectedIndex=0;
  const indexGroupList = groupListElement.selectedIndex;
  const groupListValue = groupListElement.value;*/
  localStorage.setItem("GroupName", 0);
  localStorage.setItem("GroupNameValue", "ALL");
  localStorage.setItem("Devive_ID_Selection", 0);

  if (deviceIdDropdown)
  {
    deviceIdDropdown.selectedIndex = 0;
  }
}
});

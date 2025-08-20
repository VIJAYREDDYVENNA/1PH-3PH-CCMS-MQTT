 async function fetchStates() {
    const response = await fetch('../json-data/get_states.php');
    const data = await response.json();
    const stateSelect = document.getElementById('state');
    stateSelect.innerHTML = '<option value="">Select State</option>'; 
    for (const state in data) {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        stateSelect.appendChild(option);
    }

    const option = document.createElement('option');
    option.value = "add-state";
    option.className = 'text-primary fw-bold';
    option.textContent = "Add State"; 
    stateSelect.appendChild(option);
}

async function updateDistricts() {
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');
    const selectedState = stateSelect.value;

        // Clear existing district options
    districtSelect.innerHTML = '<option value="">Select District</option>';
    //<option value="add-district">Add District</option>

    if (selectedState && selectedState !== 'add-state') {
        const response = await fetch('../json-data/get_states.php');
        const data = await response.json();
        if (data[selectedState]) {
            data[selectedState].forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        }
    }
    const option = document.createElement('option');
    option.value = "add-district";
    option.className = 'text-primary fw-bold';
    option.textContent = "Add District";
    districtSelect.appendChild(option);

}

function handleStateChange() {
    const stateSelect = document.getElementById('state');
    const selectedValue = stateSelect.value;

    if (selectedValue === 'add-state') {
        addState();
    } else {
        updateDistricts();
    }
}

function handleDistrictChange() {
    const districtSelect = document.getElementById('district');
    const selectedValue = districtSelect.value;

    if (selectedValue === 'add-district') {
        addDistrict();
    }
}

async function addState() {
    const newState = prompt("Enter the name of the new state:");
    if (newState) {
        await fetch('../json-data/add_state.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'name': newState
            })
        });
            fetchStates(); // Refresh the states dropdown
        }
    }

    async function addDistrict() {
        const stateSelect = document.getElementById('state');
        const selectedState = stateSelect.value;
        const newDistrict = prompt("Enter the name of the new district:");
        if (selectedState && selectedState !== 'add-state' && newDistrict) {
            await fetch('../json-data/add_district.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'state': selectedState,
                    'name': newDistrict
                })
            });
            updateDistricts(); // Refresh the districts dropdown
        }
    }

    document.addEventListener('DOMContentLoaded', fetchStates);
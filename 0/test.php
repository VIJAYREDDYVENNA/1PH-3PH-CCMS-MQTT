<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Multi-Select Dropdown with Mode Toggle and Select All</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			margin: 0;
			padding: 20px;
			background-color: #f4f4f4;
		}

		.custom-select {
			position: relative;
			display: inline-block;
			width: 250px;
			font-size: 16px;
		}

		.select-selected {
			background-color: #ffffff;
			border: 1px solid #ced4da;
			padding: 10px;
			cursor: pointer;
			border-radius: 4px;
			color: #495057;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
			display: flex;
			align-items: center;
			justify-content: space-between;
			max-height: 50px;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
			position: relative;
		}

		.show-more {
			font-size: 12px;
			color: #007bff;
			cursor: pointer;
			margin-left: 10px;
			display: none; /* Initially hidden */
		}

		.select-items-show .show-more {
			display: block; /* Show when dropdown is expanded */
		}

		.more-text {
			display: none; /* Initially hidden */
			white-space: normal; /* Allow text to wrap */
		}


		.select-selected::after {
			content: '▼';
			font-size: 12px;
			color: #495057;
			margin-left: 10px;
		}

		.select-items {
			display: none;
			position: absolute;
			background-color: #ffffff;
			border: 1px solid #ced4da;
			border-top: none;
			z-index: 99;
			width: 100%;
			max-height: 200px;
			overflow-y: auto;
			border-radius: 4px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}

		.select-items label {
			display: block;
			padding: 10px;
			cursor: pointer;
			color: #495057;
			padding-bottom: 0px;
		}

		
		.dd_items label:hover {
			background-color: #e9ecef;
		}

		.select-items input {
			margin-right: 8px;
		}

		.select-items-show {
			display: block;
		}

		.button {
			margin-top: 10px;
			padding: 10px 20px;
			border: none;
			background-color: #007bff;
			color: #ffffff;
			border-radius: 4px;
			cursor: pointer;
			font-size: 16px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}

		.button:hover {
			background-color: #0056b3;
		}

		.toggle-option{
			font-weight: bold;
			cursor: default;
			margin: 2px 0;
		} 
		.action-option {
			font-weight: bold;
			cursor: default;
			margin: 5px 0;
		}

		.action-option {
			display: flex;
			justify-content: space-between;
		}

		.action-option.hidden {
			display: none;
		}

		select {
			width: 100%;
			padding: 8px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			margin-bottom: 10px;
		}
	</style>
</head>
<body>

	<div class="custom-select">
		<div class="select-selected">
			<span class="text-content">Select Devices</span>
			<span class="show-more">Show More</span>
		</div>
		<div class="select-items">
			<label class="toggle-option">
				<select id="mode-select">
					<option value="single" selected>Single Selection</option>
					<option value="multiple">Multiple Selection</option>
				</select>
			</label>
			<label class="action-option hidden">
				<button id="select-all-btn">Select All</button>
				<button id="deselect-all-btn">Deselect All</button>
			</label>
			<label class="dd_items">
				<label><input type="checkbox" value="CCMS_1"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_2"> HYD</label>
				<label><input type="checkbox" value="CCMS_3"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_4"> HYD</label>
				<label><input type="checkbox" value="CCMS_5"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_6"> HYD</label>
				<label><input type="checkbox" value="CCMS_7"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_8"> HYD</label>
				<label><input type="checkbox" value="CCMS_9"> HYD</label>
				<label><input type="checkbox" value="CCMS_11"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_12"> HYD</label>
				<label><input type="checkbox" value="CCMS_13"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_14"> HYD</label>
				<label><input type="checkbox" value="CCMS_15"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_16"> HYD</label>
				<label><input type="checkbox" value="CCMS_17"> CCMS_ISTL_1</label>
				<label><input type="checkbox" value="CCMS_18" checked> HYD</label>
				<label><input type="checkbox" value="CCMS_19"> HYD</label>
			</div>
		</div>
		<button class="button" id="show-values-btn">Show Selected Values</button>



		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const select = document.querySelector('.custom-select');
				const selectedDiv = select.querySelector('.select-selected');
				const textContent = selectedDiv.querySelector('.text-content');
				const showMore = selectedDiv.querySelector('.show-more');
				const itemsDiv = select.querySelector('.select-items');
				const showValuesBtn = document.getElementById('show-values-btn');
				const modeSelect = document.getElementById('mode-select');
				const selectAllBtn = document.getElementById('select-all-btn');
				const deselectAllBtn = document.getElementById('deselect-all-btn');
				const actionOption = document.querySelector('.action-option');

				let isMultipleSelection = modeSelect.value === 'multiple';

				function updateSelectedText() {
					const checkedCheckboxes = Array.from(select.querySelectorAll('.select-items input[type="checkbox"]:checked'));
					if (isMultipleSelection) {
						const selectedItems = checkedCheckboxes.map(checkbox => checkbox.parentElement.textContent.trim());
						const text = selectedItems.length ? selectedItems.join(', ') : 'Select Devices';
						textContent.textContent = text;

						if (text.length > 50) {
                textContent.textContent = text.substring(0, 50) + '...'; // Truncate text
                showMore.style.display = 'block'; // Show "Show More"
            } else {
                showMore.style.display = 'none'; // Hide "Show More"
            }
        } else {
        	if (checkedCheckboxes.length > 0) {
        		const text = checkedCheckboxes[0].parentElement.textContent.trim();
        		textContent.textContent = text.length > 50 ? text.substring(0, 50) + '...' : text;
        		showMore.style.display = 'none';
        	} else {
        		textContent.textContent = 'Select Devices';
        		showMore.style.display = 'none';
        	}
        }
    }

    function updateCheckboxes() {
    	select.querySelectorAll('.select-items input[type="checkbox"]').forEach(checkbox => {
    		checkbox.removeEventListener('click', handleCheckboxClick);
    		checkbox.addEventListener('click', handleCheckboxClick);
    	});
    }

    function handleCheckboxClick() {
    	if (!isMultipleSelection) {
    		select.querySelectorAll('.select-items input[type="checkbox"]').forEach(cb => {
    			if (cb !== this) cb.checked = false;
    		});

            // Show alert if only one checkbox is selected
    		const checkedCheckboxes = Array.from(select.querySelectorAll('.select-items input[type="checkbox"]:checked'));
    		if (checkedCheckboxes.length === 1) {
    			alert('Single value selected: ' + checkedCheckboxes[0].value);
    		}
    	}
    	updateSelectedText();
    }

    function selectAll() {
    	select.querySelectorAll('.select-items input[type="checkbox"]').forEach(cb => {
    		cb.checked = true;
    	});
    	updateSelectedText();
    }

    function deselectAll() {
    	select.querySelectorAll('.select-items input[type="checkbox"]').forEach(cb => {
    		cb.checked = false;
    	});
    	updateSelectedText();
    }

    function toggleActionOptions() {
    	if (isMultipleSelection) {
    		actionOption.classList.remove('hidden');
    	} else {
    		actionOption.classList.add('hidden');
    	}
    }

    selectedDiv.addEventListener('click', function() {
    	itemsDiv.classList.toggle('select-items-show');
    });

    showMore.addEventListener('click', function() {
    	if (textContent.textContent.includes('...')) {
    		textContent.textContent = textContent.getAttribute('data-full-text');
    		showMore.textContent = 'Show Less';
    	} else {
    		textContent.textContent = textContent.getAttribute('data-truncated-text');
    		showMore.textContent = 'Show More';
    	}
    });

    showValuesBtn.addEventListener('click', function() {
    	const selectedValues = Array.from(select.querySelectorAll('.select-items input[type="checkbox"]:checked'))
    	.map(checkbox => checkbox.value);
    	alert('Selected values: ' + selectedValues.join(', '));
    });

    modeSelect.addEventListener('change', function() {
    	const previouslyChecked = Array.from(select.querySelectorAll('.select-items input[type="checkbox"]:checked'));

    	if (modeSelect.value === 'single') {
    		if (isMultipleSelection) {
                // If switching from multiple to single, deselect all checked items
    			deselectAll();
    		}
            // Clear previously checked items
    		previouslyChecked.forEach(checkbox => {
    			checkbox.checked = false;
    		});
    	}

    	isMultipleSelection = modeSelect.value === 'multiple';
    	updateCheckboxes();
    	toggleActionOptions();
    	updateSelectedText();
    });

    selectAllBtn.addEventListener('click', function(event) {
    	event.stopPropagation();
    	selectAll();
    });

    deselectAllBtn.addEventListener('click', function(event) {
    	event.stopPropagation();
    	deselectAll();
    });

    // Initial setup
    updateSelectedText();
    updateCheckboxes();
    toggleActionOptions();

    document.addEventListener('click', function(event) {
    	if (!select.contains(event.target) && !showValuesBtn.contains(event.target)) {
    		itemsDiv.classList.remove('select-items-show');
    	}
    });
});

</script>
</body>
</html>

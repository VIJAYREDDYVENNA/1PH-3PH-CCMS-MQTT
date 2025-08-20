<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltage Input Validation</title>
    <style>
        .error { color: red; }
        .error-message { margin-left: 10px; }
    </style>
</head>
<body>
    <h1>Voltage Input Validation</h1>
    <form id="voltageForm">
        <div>
            <label for="voltage1">Voltage 1:</label>
            <input type="text" id="voltage1" name="voltage1">
            <span class="error-message" id="error-voltage1"></span>
        </div>
        <div>
            <label for="voltage2">Voltage 2:</label>
            <input type="text" id="voltage2" name="voltage2">
            <span class="error-message" id="error-voltage2"></span>
        </div>
        <div>
            <label for="voltage3">Voltage 3:</label>
            <input type="text" id="voltage3" name="voltage3">
            <span class="error-message" id="error-voltage3"></span>
        </div>
        <div>
            <label for="voltage4">Voltage 4:</label>
            <input type="text" id="voltage4" name="voltage4">
            <span class="error-message" id="error-voltage4"></span>
        </div>
        <div>
            <label for="voltage5">Voltage 5:</label>
            <input type="text" id="voltage5" name="voltage5">
            <span class="error-message" id="error-voltage5"></span>
        </div>
        <div>
            <label for="voltage6">Voltage 6:</label>
            <input type="text" id="voltage6" name="voltage6">
            <span class="error-message" id="error-voltage6"></span>
        </div>
        <button type="button" onclick="validateAndSubmit()">Submit</button>
    </form>

    <script>
        function validateAndSubmit() {
            const voltages = [];
            let hasErrors = false;

            for (let i = 1; i <= 6; i++) {
                const input = document.getElementById(`voltage${i}`);
                const errorSpan = document.getElementById(`error-voltage${i}`);
                const value = input.value.trim();
                
                // Clear previous error messages
                errorSpan.textContent = '';

                // Check if value is a number and between 0 and 500
                const numberValue = parseFloat(value);
                
                if (isNaN(numberValue)) {
                    errorSpan.textContent = `Voltage ${i} must be a number.`;
                    hasErrors = true;
                } else if (numberValue < 0 || numberValue > 500) {
                    errorSpan.textContent = `Voltage ${i} must be between 0 and 500 volts.`;
                    hasErrors = true;
                } else {
                    voltages.push(numberValue);
                }
            }

            if (hasErrors) {
                // Stop the form submission process
                return;
            }

            // Proceed with AJAX request if no errors
            sendToDatabase(voltages);
        }

        function sendToDatabase(voltages) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_voltages.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Handle success response
                        alert('Voltages saved successfully!');
                    } else {
                        // Handle error response
                        alert('Error saving voltages.');
                    }
                }
            };

            // Send AJAX request
            const data = `voltages=${encodeURIComponent(JSON.stringify(voltages))}`;
            xhr.send(data);
        }
    </script>
</body>
</html>

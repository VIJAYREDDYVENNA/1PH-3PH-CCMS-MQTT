function ActivateDevice() {
  const activationCode = document.getElementById('activationCode').value;
  const userName = document.getElementById('userName').value;
  const userMobile = document.getElementById('mobileNumber').value;

  if (!activationCode || !userName || !userMobile) {
    showNotification('warning', 'All fields are required.');
    return;
  }

  fetch('../common-files/mqtt_update_topic.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      activationCode: activationCode,
      userName: userName,
      userMobile: userMobile,
      action: 'getEncrypted'
    })
  })
    .then(response => response.json())
    .then(data => {
      const secretKey = 'mobile_app_activation';
      const decryptedData = xor_decrypt(data.data, secretKey);

      if (!decryptedData) {
        showNotification('error', 'Failed to decrypt activation response.');
        return;
      }

      const options = {
        username: decryptedData.username,
        password: decryptedData.password,
        reconnectPeriod: decryptedData.reconnectPeriod,
        connectTimeout: decryptedData.connectTimeout,
        clean: decryptedData.clean
      };

      const brokerUrl = decryptedData.brokerUrl;
      const topic = decryptedData.mainTopic;

      const client = mqtt.connect(brokerUrl, options);

      client.on('connect', function () {
        console.log('MQTT Connected. Publishing activationCode...');

        client.publish(topic, activationCode, { qos: 2, retain: true }, function (err) {
          if (err) {
            showNotification('error', 'Publish failed: ' + err.message);
          } else {
            // showNotification('success', 'Activation code published via MQTT.');

            InserData(activationCode,userName,userMobile);
           
          }

          client.end();
        });
      });

      client.on('error', function (err) {
        showNotification('error', 'MQTT Connection Error: ' + err.message);
      });
    })
    .catch(error => {
      showNotification('error', 'Error: ' + error.message);
    });
}

function xor_decrypt(encoded, key) {
  try {
    const decoded = atob(encoded);
    let result = '';

    for (let i = 0; i < decoded.length; i++) {
      result += String.fromCharCode(decoded.charCodeAt(i) ^ key.charCodeAt(i % key.length));
    }

    return JSON.parse(result);
  } catch (error) {
    console.error('Decryption failed:', error);
    return null;
  }
}

function showNotification(type, message) {
  const icons = {
    success: 'check-circle-fill',
    error: 'exclamation-triangle-fill',
    warning: 'exclamation-triangle-fill'
  };

  const bgColors = {
    success: 'bg-success',
    error: 'bg-danger',
    warning: 'bg-warning'
  };

  const titles = {
    success: 'Success',
    error: 'Error',
    warning: 'Warning'
  };

  const toast = document.createElement('div');
  toast.className = `toast text-white ${bgColors[type]} border-0 shadow-lg`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');

  toast.innerHTML = `
    <div class="toast-header ${bgColors[type]} text-white border-0">
      <svg class="bi me-2" width="20" height="20" role="img" aria-label="${titles[type]}">
        <use xlink:href="#${icons[type]}"></use>
      </svg>
      <strong class="me-auto">${titles[type]}</strong>
      <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body fs-6">
      ${message}
    </div>
  `;

  document.getElementById('toastContainer').appendChild(toast);

  const bsToast = new bootstrap.Toast(toast, { delay: 8000 });
  bsToast.show();

  toast.addEventListener('hidden.bs.toast', () => {
    toast.remove();
  });
}

function InserData(activationCode,userName,userMobile){
     fetch('../common-files/mqtt_update_topic.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: new URLSearchParams({
                activationCode: activationCode,
                userName: userName,
                userMobile: userMobile,
                action: 'insertData'
              })
            })
              .then(response => response.json())
              .then(result => {
                if (result.status === 'success') {
                  showNotification('success', 'Activation successful. Please check your device. If not activated, try again.');
                } else {
                  showNotification('error', 'Failed to save activation details: ' + result.message);
                }
              })
              .catch(error => {
                showNotification('error', 'Error saving activation details: ' + error.message);
              });
}

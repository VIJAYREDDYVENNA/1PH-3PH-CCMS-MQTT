<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MQTT Connection Example</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mqtt/5.5.1/mqtt.min.js"></script>
</head>
<body>
  <script>
    const brokerUrl = "wss://mqtt-broker.istlabsonline.com/mqtt"; 
    const randomNum = Math.floor(100 + Math.random() * 900);
    const options = {
      clientId: "ISTL_Client_" + randomNum,
      clean: true,
      connectTimeout: 4000,
      reconnectPeriod: 5000,
      username: 'istlMqttHyd',
      password: 'Istl_1234@Hyd'
    };
    const client = mqtt.connect(brokerUrl, options);
        
    client.on("connect", () => {
      console.log("Connected with clientId:", options.clientId);
        
    });

    client.on("error", (err) => {
      console.error("Connection error:", err.message || err);     
       
    });
    
    client.on("close", () => {
      console.warn("Connection closed. Attempting to reconnect...");
    });

    client.on("reconnect", () => {
      console.log("Attempting to reconnect...");
    });
 
    window.mqttClient = client;
  </script>
</body>
</html>

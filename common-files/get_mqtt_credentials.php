<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // The sensitive data - now including broker URL and topics
    $options = [
        'username' => 'istlMqttHyd',
        'password' => 'Istl_1234@Hyd',
        'reconnectPeriod' => 1000, 
        'connectTimeout' => 4000,   
        'clean' => true,
        'brokerUrl' => 'wss://mqtt-broker.istlabsonline.com/mqtt',
        'mainTopic' => 'PUB/SCRSC/VFD_STATUS',
        'requestTopic' => 'test/request_response'
    ];
      
    // Convert options array to JSON
    $json_data = json_encode($options);
    
    // A simple secret key
    $secret_key = 'consoledata';
    
    // Simple XOR encryption
    function xor_encrypt($data, $key) {
        $key_length = strlen($key);
        $output = '';
        
        for($i = 0; $i < strlen($data); $i++) {
            $output .= $data[$i] ^ $key[$i % $key_length];
        }
        
        return base64_encode($output);
    }
    
    $encrypted = xor_encrypt($json_data, $secret_key); 
    
    echo json_encode(['data' => $encrypted]);
}
?>
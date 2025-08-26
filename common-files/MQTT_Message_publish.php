<?php
require('phpMQTT_Library.php');

class SimpleMQTT {
    private static $instance = null;
    private $mqtt = null;
    private $isConnected = false;
    
    // MQTT Configuration
    private $server = '95.111.238.141';
    private $port = 1883;
    private $username = 'istlMqttHyd';
    private $password = 'Istl_1234@Hyd';
    private $client_id;
    
    private function __construct() {
        $this->client_id = uniqid('mqtt_');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        if (!$this->isConnected) {
            try {
                $this->mqtt = new Bluerhinos\phpMQTT($this->server, $this->port, $this->client_id);
                if ($this->mqtt->connect(true, NULL, $this->username, $this->password)) {
                    $this->isConnected = true;
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }
    
    public function publish($topic, $message, $qos = 2, $retain = true) {
        try {
            if (!$this->connect()) {
                return false;
            }
            
            $this->mqtt->publish($topic, $message, $qos, $retain);
            return true;
            
        } catch (Exception $e) {
            $this->isConnected = false;
            return false;
        }
    }
}

function publishMQTTMessage($topic, $message, $qos = 2, $retain = true) {
    $mqtt = SimpleMQTT::getInstance();
    return $mqtt->publish($topic, $message, $qos, $retain);
}
?>
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


function toBase36Upper($num) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($chars);
    $result = '';
    while ($num > 0) {
        $result = $chars[$num % $base] . $result;
        $num = intdiv($num, $base);
    }
    return $result;
}

function generateUniqueCode6() {
    // use milliseconds + random for uniqueness
    $now = microtime(true);
    $milliseconds = (int) round($now * 1000);

    // mix datetime with random
    $num = $milliseconds + random_int(0, 999);

    // convert to Base36 uppercase
    $code = toBase36Upper($num);

    // make sure it's exactly 6 chars (pad or trim)
    return str_pad(substr($code, -6), 6, '0', STR_PAD_LEFT);
}

// echo generateUniqueCode6();


?>
<?php
$file = 'states_districts.json';
$data = json_decode(file_get_contents($file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newState = $_POST['name'];
    if (!isset($data[$newState])) {
        $data[$newState] = [];
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo 'State added successfully!';
    } else {
        echo 'State already exists!';
    }
}
?>

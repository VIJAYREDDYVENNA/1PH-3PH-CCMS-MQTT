<?php
$file = 'states_districts.json';
$data = json_decode(file_get_contents($file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state = $_POST['state'];
    $district = $_POST['name'];
    
    if (isset($data[$state]) && !in_array($district, $data[$state])) {
        $data[$state][] = $district;
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo 'District added successfully!';
    } else {
        echo 'District already exists or state does not exist!';
    }
}
?>

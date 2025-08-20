<?php
$file = 'states_districts.json';
if (file_exists($file)) {
    $json = file_get_contents($file);
    header('Content-Type: application/json');
    echo $json;
} else {
    echo json_encode([]);
}
?>

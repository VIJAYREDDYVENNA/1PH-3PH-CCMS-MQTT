<?php
$devices_list = json_decode($_SESSION["DEVICES_LIST"], true);
foreach ($devices_list as $list) {
	echo '<option value="' . $list["D_ID"] . '">' . $list["D_NAME"] . '</option>';
}
?>

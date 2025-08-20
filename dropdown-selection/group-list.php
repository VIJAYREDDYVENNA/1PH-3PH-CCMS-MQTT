<?php 
$decoded_group_list = json_decode($_SESSION["GROUP_LIST"], true);
foreach ($decoded_group_list as $group) {
	echo '<option value="' . $group["GROUP"] . '">' . $group["GROUP"] . '</option>';
}
?>


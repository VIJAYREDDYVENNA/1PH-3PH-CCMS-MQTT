<?php

$list = "`device_id`, `c_device_name` AS `device_name`";
if ($role == "SUPERADMIN") {
	$list = "`device_id`, `s_device_name` AS `device_name`";
}


?>
<?php

$ip_address = $_SERVER['REMOTE_ADDR'];

if($ip_address=="::1")
{
	define('HOST','localhost');
	define('USERNAME', 'root');
	define('PASSWORD','123456');
	
	define('DB_USER', 'new_ccms_1ph_3ph_userdb');
	define('DB_ALL', 'new_ccms_all');

}
else
{
	define('HOST','103.101.59.93');
	define('USERNAME', 'istlabsonline_db_user');
	define('PASSWORD','istlabsonline_db_pass');
	define('DB_USER', 'ccms_user_details');
	define('DB_ALL', 'ccms_all_devices');
}
$central_db=DB_ALL;
$users_db=DB_USER;

?>
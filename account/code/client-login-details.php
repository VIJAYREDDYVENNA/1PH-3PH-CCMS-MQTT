<?php
$continent = "";
$country ="";
$postal_code =""; 
$subdivision =""; 
$subdivision =""; 
$time_zone =""; 
$org =""; 
$latitude =""; 
$longitude =""; 
$ip_address="empty";

date_default_timezone_set('Asia/Kolkata');
$date=date("Y-m-d H:i:s");


if (!empty($_SERVER['HTTP_CLIENT_IP']))   
{
	$ip_address = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
{
	$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
	$ip_address = $_SERVER['REMOTE_ADDR'];
}

try {
	
$url = "http://ip-api.com/json/$ip_address";
	$response = file_get_contents($url);
	$res = json_decode($response);

	if ($res && $res->status == "success") {		
		$continent = $res->continent ?? '';
		$country = $res->country ?? '';
		$subdivision = $res->regionName ?? '';
		$city = $res->city ?? '';
		$postal_code = $res->zip ?? '';
		$latitude = $res->lat ?? '';
		$longitude = $res->lon ?? '';
		$org = $res->isp ?? '';
	} 

} catch (Exception $e) {
	
}

?>
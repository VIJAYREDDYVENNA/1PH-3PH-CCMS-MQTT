
<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1.'config_db/config.php';
require_once BASE_PATH_1.'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$client = $sessionVars['client'];
//=================================================
$return_response = "";
$total_switch_point=0;
$user_devices="";
//=================================================
$send=array();
$lat=0.0;
$long=0.0;
//$group_id = "ALL";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$group_id = $_POST['GROUP_ID'];

	include_once(BASE_PATH_1."common-files/selecting_group_device.php");

	if($user_devices!="")
	{
		$user_devices= substr($user_devices, 0, -1);
	}

	$conn_db_all = mysqli_connect(HOST,USERNAME,PASSWORD, DB_ALL);
	if (!$conn_db_all) {
		die("Connection failed: " . mysqli_connect_error());
	}
	else
	{
		$date="";
		$signal="";
		$address="";
		$land_mark="";
		$rated_kva="";
		$installation_date="";
		$device_status="";
		$on_off_status="";
		$unit_capacity="";
		$operation_mode="";
		$installation_status="";
		$installed_lights=0;

		$sql = "SELECT  * FROM live_data_updates where device_id IN ($user_devices) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";


		/*$txt =$sql."\n";
		$myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

		$txt =$user_devices."\n";
		$myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);*/

		$status=0;
		if(mysqli_query($conn_db_all, $sql))
		{
			$result = mysqli_query($conn_db_all, $sql);
			if(mysqli_num_rows($result)>0)
			{
				while($r = mysqli_fetch_assoc( $result ))
				{
					$installation_date=$r['installed_date'];
					$frame_date_time=date("H:i:s d-m-Y", strtotime($r['date_time']));				
					$device_id=$r['device_id'];					
					$status=$r['active_device'];
					$unit_capacity=$r['unit_capacity'];
					$operation_mode=$r['operation_mode'];
					$installed_lights=$r['total_lights'];

					
					$v1 = $r['voltage_ph1'];
					$v2 = $r['voltage_ph2'];
					$v3 = $r['voltage_ph3'];
					$c1 = $r['current_ph1'];
					$c2 = $r['current_ph2'];
					$c3 = $r['current_ph3'];
					$kwh = $r['energy_kwh_total'];
					$kvah = $r['energy_kvah_total'];
					$on_off_status=$r['on_off_status'];
					$phase=$r['phase'];
					$google_location="";


					$lat=0.0;
					$long=0.0;
					if($r['location']!='0,0'&& strpos($r['location'], "0000000,000000") === false)
					{
						
						$coordinates=$r['location'];
						$co_array=explode(',', $coordinates);
						if($co_array[0]!=""&&$co_array[1]!=""){
							try {
								$lat=convert_DMS_DD($co_array[0]);
								$long=convert_DMS_DD($co_array[1]);
								$coordinates= $lat.",".$long;
							} catch (Exception $e) {
							}
						}
						$google_location="https://www.google.co.in/maps?q=".$coordinates;
					}
					

					/*if($r['installed_status']==1)
					{
						if($r['active_device']==1)
						{						
							$device_status="<span class='text-white fw-semibold bg-success py-1 px-2 rounded'> Active</span>";
						}
						else if($r['poor_network']==1)
						{
							$device_status="<span class='fw-semibold bg-warning py-1 px-2 rounded'> Poor Newtwork</span>";
						}
						else if($r['power_failure']==1)
						{
							$device_status="Power Fail";
						}
						else if($r['faulty']==1)
						{
							$device_status="Faulty";
						}

						$installation_status="<span class='text-success-emphasis fw-semibold'> Installed</span>";
					}
					else
					{
						$device_status="<span class='text-danger fw-semibold'> Not Installed</span>";
						$installation_status= "<span class='text-danger fw-semibold'> Not Installed</span>";

					}*/



					$on_off_status = $r['on_off_status'];
					if($on_off_status==1||$on_off_status==3||$on_off_status==4)
					{		

						$on_off_status="ON";
						$status=1;

					}
					else if($on_off_status==5)
					{
						$on_off_status="MANUAL ON";
						$status=1;

					}
					else
					{
						$on_off_status="OFF";
						$status=0;
					}
					if($r['power_failure']==1)
					{
						$status=3;
					}
					else if($r['poor_network']==1)
					{
						$status=2;
					}
					else if($r['faulty']==1)
					{
						$status=4;
					}

					$name=$device_id;
					$device_ids = array_column($device_list, 'D_ID');
					$index = array_search($device_id, $device_ids);

					
					if ($index !== false) {
						$name = $device_list[$index]['D_ID'];
					}
					$window="";
					if($phase=="3PH")
					{

						$window='<h5 class="text-primary mb-0">CCMS Info</h5> <hr class="m-0 pt-2 mt-2 text-black">'.
						'<div class="font-small text-black"><label class="fw-bold p-0">Device ID: </label><label> '.$name.' </label></div>'.

						'<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black "><label class="fw-bold">Last Updated at: </label><label>'.$frame_date_time.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">On/Off Status: </label><label>'.$on_off_status.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">Voltage(V):</label><label><b> R =</b>'.$v1.'<b> , Y =</b>'.$v2.'<b> , B =</b>'.$v3.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black"/>' .

						'<div class="font-small text-black"><label class="fw-bold">Current(A): </label><label><b> R =</b>'.$c1.'<b> , Y =</b>'.$c2.'<b> , B =</b>'.$c3.'</label></div>' .

						'<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">Energy(Units): </label><label><b> kWh =</b>'.$kwh.' </label></div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">'.

						'<div class="font-small text-black"><label class="fw-bold">Energy(Units): </label><label><b> kVAh =</b>'.$kvah.' </label></div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">'.

						'<div class="font-small text-black"><label class="fw-bold">Location: </label><a href='.$google_location.' target="_blank"> Google Map</a> </div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">' ;
					}else if($phase=="1PH")
					{
						$window='<h5 class="text-primary mb-0">CCMS Info</h5> <hr class="m-0 pt-2 mt-2 text-black">'.
						'<div class="font-small text-black"><label class="fw-bold p-0">Device ID: </label><label> '.$name.' </label></div>'.

						'<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black "><label class="fw-bold">Last Updated at: </label><label>'.$frame_date_time.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">On/Off Status: </label><label>'.$on_off_status.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">Voltage(V):</label><label>'.$v1.'</label></div>

						<hr class="m-0 pt-2 mt-2 text-black"/>' .

						'<div class="font-small text-black"><label class="fw-bold">Current(A): </label><label>'.$c1.'</label></div>' .

						'<hr class="m-0 pt-2 mt-2 text-black">' .

						'<div class="font-small text-black"><label class="fw-bold">Energy(Units): </label><label><b> kWh =</b>'.$kwh.' </label></div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">'.

						'<div class="font-small text-black"><label class="fw-bold">Energy(Units): </label><label><b> kVAh =</b>'.$kvah.' </label></div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">'.

						'<div class="font-small text-black"><label class="fw-bold">Location: </label><a href='.$google_location.' target="_blank"> Google Map</a> </div>'.
						'<hr class="m-0 pt-2 mt-2 text-black">' ;

					}




					$send[]=array("va"=>$window, 'l1'=>$lat, "l2"=>$long, "icon"=>$status, "id"=>$name);
				}
			}
		}
		mysqli_close($conn_db_all);
	}
	echo json_encode(array($send, strtoupper($client)));
}
else
{
	$return_response="Data not Available";
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////



function convert_DMS_DD($coordinate)
{
	$array_split=explode('.', $coordinate);
	$deg=(int)($array_split[0]/100);
	$time=((float)$coordinate-$deg*100)/60;
	return $decimal=round($deg+$time, 7);	
}

?>
<?php
$normal='class=""';
$red='class="text-danger fw-bold"'; 
$orange='class="text-warning fw-bold"'; 
$green='class="text-success fw-bold"'; 
$class_r=$normal;
$class_y=$normal;
$class_b=$normal;
$class_ir=$normal;
$class_iy=$normal;
$class_ib=$normal;

$class_pf=$normal;
$class_temp_1=$normal;
$class_temp_2=$normal;
$class_temp_3=$normal;
$class_temp_4=$normal;
$class_temp_5=$normal;
$class_load=$normal;
$class_load_r=$normal;
$class_load_y=$normal;
$class_load_b=$normal;
$class_on_off_status=$normal;
$temp_fail=1;

$v_min_r=180;
$v_min_y=180;
$v_min_b=180;
$v_max_r=250;
$v_max_y=250;
$v_max_b=250;
$c_max_r=20;
$c_max_y=20;
$c_max_b=20;
$temp=45;
$pf1=0.85;
$pf2=-0.85;
$load=80;

//$sql_set_parma="SELECT  `l_r`,`l_y`,`l_b`,`u_r`,`u_y`,`u_b`,`i_r`,`i_y`,`i_b`,`pf` FROM `$central_db`.`thresholds` WHERE device_id='$device_id'";
$sql_set_parma="SELECT '180' AS l_r, '180' AS l_y, '180' AS l_b, '265' AS u_r, '265' AS u_y, '265' AS u_b, '20' AS i_r, '20' AS i_y, '20' AS i_b, '1' AS pf FROM dual WHERE NOT EXISTS ( SELECT 1 FROM `$central_db`.`thresholds` WHERE device_id = '$device_id' ) UNION ALL SELECT l_r, l_y, l_b, u_r, u_y, u_b, i_r, i_y, i_b, pf FROM `$central_db`.`thresholds` WHERE device_id = '$device_id'";
if(mysqli_query($conn, $sql_set_parma))
{
	$result_set_parma = mysqli_query($conn, $sql_set_parma);
	if(mysqli_num_rows($result_set_parma)>0)
	{
		$r_set_parma = mysqli_fetch_assoc( $result_set_parma ) ; 
		$v_min_r=$r_set_parma['l_r'];
		$v_min_y=$r_set_parma['l_y'];
		$v_min_b=$r_set_parma['l_b'];
		$v_max_r=$r_set_parma['u_r'];
		$v_max_y=$r_set_parma['u_y'];
		$v_max_b=$r_set_parma['u_b'];
		$c_max_r=$r_set_parma['i_r'];
		$c_max_y=$r_set_parma['i_y'];
		$c_max_b=$r_set_parma['i_b'];		
	}
}


$v_max_lr=$v_max_r - $v_max_r*(0.04);
$v_max_ly=$v_max_y - $v_max_y*(0.04);
$v_max_lb=$v_max_b - $v_max_b*(0.04);
$pf2 = round((1 - $pf1 + 1)-2, 3);
?>
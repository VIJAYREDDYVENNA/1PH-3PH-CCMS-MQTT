
function show_location(location)
{
	if(location!="0,0" && location!="000000000,0000000000")
	{
		const myLocation_center = location.split(",");
		var loc_lat=myLocation_center[0].trim();
		var loc_long=myLocation_center[1].trim();
		loc_lat= ddm_to_dd( loc_lat);
		loc_long= ddm_to_dd( loc_long);

		var location ="https://www.google.co.in/maps?q="+loc_lat+","+loc_long;	
		window.open(location, "_blank");
	}
}



function ddm_to_dd(ddmm) 
{
	var ddm=parseFloat(ddmm).toFixed(6);

	var degrees = Math.floor(ddm / 100.0);
	var minutes = ddm - degrees * 100.0;
	var decimal_degrees = degrees + minutes / 60.0;
	decimal_degrees=parseFloat(decimal_degrees).toFixed(6)
	return decimal_degrees;
}
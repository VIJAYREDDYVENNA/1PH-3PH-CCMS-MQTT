let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;
}
//var selected_alert  = document.getElementById('selected_phase_alert').value;
var paramter= document.getElementById('graph-paramater').value;
days_func(device_id, "LATEST", paramter);

let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
	device_id = document.getElementById('device_id').value;
	var paramter= document.getElementById('graph-paramater').value;
	days_func(device_id, "LATEST", paramter);
	refresh_data();
});

setTimeout(refresh_data, 50);
setInterval(refresh_data, 20000);
function refresh_data() {
	if (typeof update_frame_time === "function") {
		device_id = document.getElementById('device_id').value;
		update_frame_time(device_id);
	} 
}


var datapoints = [];
var amchart = AmCharts.makeChart("chartdiv", {
	"type": "serial",
	"theme": "light",
	"marginTop": 0,
	"marginRight": 80,
	"dataProvider": datapoints,
	"valueAxes": [{
		"axisAlpha": 0,
		"position": "left",
		"title": "Voltage(V)",

		"gridColor": "#fff",
		"color": "#9e9e9e",
		"titleColor": "#fff"
	}, {
		"id": "v1",
		"axisColor": "#FF6600",
		"axisThickness": 2,
		"axisAlpha": 1,
		"position": "left"
	}],
	"graphs": [{
		"id": "g1",
		"balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[valueR]]</span></b>",
		"bullet": "round",
		"bulletSize": 2,
		"lineColor": "#d53f3f",
		"lineThickness": 2,
		"type": "smoothedLine",
		"valueField": "valueR"
	},
	{
		"id": "g2",
		"balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[valueY]]</span></b>",
		"bullet": "round",
		"bulletSize": 2,
		"lineColor": "#cbb015",
		"lineThickness": 2,
		"type": "smoothedLine",
		"valueField": "valueY"
	},{
		"id": "g3",
		"balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[valueB]]</span></b>",
		"bullet": "round",
		"bulletSize": 2,
		"lineColor": "#147cbb",
		"lineThickness": 2,
		"type": "smoothedLine",
		"valueField": "valueB"
	}],
	"chartScrollbar": {

		"gridAlpha": 0,
		"color": "#fff",
		"scrollbarHeight": 30,
		"backgroundAlpha": 0,
		"selectedBackgroundAlpha": 0.1,
		"selectedBackgroundColor": "#fff",
		"graphFillAlpha": 0,
		"autoGridCount": true,
		"selectedGraphFillAlpha": 0,
		"graphLineAlpha": 0.2,
		"graphLineColor": "#c2c2c2",
		"selectedGraphLineColor": "#fff",
		"selectedGraphLineAlpha": 1
	},
	"chartCursor": {
		"categoryBalloonDateFormat": "YYYY",
		"cursorAlpha": 0,
		"valueLineEnabled": true,
		"valueLineBalloonEnabled": true,
		"valueLineAlpha": 0.5,
		"fullWidth": true
	},
	"categoryField": "year",
	"categoryAxis": {
		"gridColor": "#ddd",
		"color": "#9e9e9e",
		"minorGridAlpha": 0.1,
		"minorGridEnabled": false,
		"titleColor": "#fff"
	},
	"export": {
		"enabled": true
	},
	"xAxes": [{
		"type": "CategoryAxis",
	}]
});

function update_graph()
{
	var selected_dp = document.getElementById('graph-selection').value;
	var selected_date = document.getElementById('graph_date').value;
	var paramter= document.getElementById('graph-paramater').value;



	if(selected_dp!="LATEST")
	{
		if(selected_date==null|selected_date=="")
		{
			alert("Please Select Date");
			document.getElementById('graph_date').focus();
			document.getElementById('graph_date').style.border = "1px solid #d9534f"; 
			return false;
		}
		else
		{
			document.getElementById('graph_date').style.border = "1px solid #dee2e6"; 
		}
	}

	if(selected_dp=="LATEST")
	{
		days_func(device_id, "LATEST", paramter);
	}
	else if(selected_dp=="DAY")
	{
		day_data(device_id, "DAY", paramter);
	}
	else if(selected_dp=="DAYS")
	{
		getdays(device_id, "DAYS", paramter);
	}
	else if(selected_dp=="MONTHS")
	{
		getmonths(device_id, "MONTHS", paramter);
	}
	else if(selected_dp=="YEARS")
	{
		getyears(device_id, "YEAR", paramter);
	}
}



function days_func(device_id, latest, paramter) {

	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block');
		$(function () {
			$.ajax({
				type: "POST",
				url: '../graphs/code/graph.php',
				traditional : true,  
				data:{D_ID:device_id, TYPE:'LATEST', PARAMTER:paramter},
				dataType: "json", 
				success: function(data) {
					$("#pre-loader").css('display', 'none');
					var json = data[0];
					datapoints = [];
					for (var i = 0; i < json.length; i++) {
						yValue1 = json[i].v_1;
						yValue2 = json[i].v_2;
						yValue3 = json[i].v_3;
						if(data[1].PHASE=="3PH")
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1),  valueY: Number(yValue2), valueB: Number(yValue3) });
						}
						else
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1)});
						}
					}
					$('#day_month').html("Day : ");
					$('#day_month_value').html(latest);
					amchart.dataProvider = datapoints;
					amchart.categoryAxis.labelRotation = 45;
					amchart.categoryAxis.title = "TIME";
					amchart.validateData();

				}
			});
		});
	}
}
function getyears(device_id, years, paramter) {
	
	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block');
		$(function () {
			$.ajax({
				type: "POST",
				url: '../graphs/code/graph.php',
				traditional : true,  
				data:{D_ID:device_id, TYPE:years, PARAMTER:paramter },
				dataType: "json", 
				success: function(data) {
					$("#pre-loader").css('display', 'none');
					var json = data[0];
					datapoints = [];
					for (var i = 0; i < json.length; i++)
					{
						yValue1 = json[i].v_1;
						yValue2 = json[i].v_2;
						yValue3 = json[i].v_3;
						if(data[1].PHASE=="3PH")
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1),  valueY: Number(yValue2), valueB: Number(yValue3) });
						}
						else
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1)});
						}
					}
					$('#day_month').html("Years ");
					$('#day_month_value').html("");
					amchart.dataProvider = datapoints;
					amchart.categoryAxis.labelRotation = 0;
					amchart.categoryAxis.title = "All Years";
					amchart.validateData();

				}
			});
		});
	}
}
/*////////////////  Getting Months from selected Year ///////////////*/
function getmonths(device_id, month, paramter) {
	
	var selected_date = document.getElementById('graph_date').value;

	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block');
		$(function () {
			$.ajax({
				type: "POST",
				url: '../graphs/code/graph.php',
				traditional : true,  
				data:{D_ID:device_id, DATE:selected_date,  TYPE:month , PARAMTER:paramter},
				dataType: "json", 
				success: function(data) {
					$("#pre-loader").css('display', 'none');
					var json = data[0];
					datapoints = [];
					for (var i = 0; i < json.length; i++)
					{
						yValue1 = json[i].v_1;
						yValue2 = json[i].v_2;
						yValue3 = json[i].v_3;
						if(data[1].PHASE=="3PH")
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1),  valueY: Number(yValue2), valueB: Number(yValue3) });
						}
						else
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1)});
						}
					}

					var dateObject = new Date(selected_date);
					var month = dateObject.getMonth() + 1;
					var year = dateObject.getFullYear();
					month = month < 10 ? '0' + month : month;

					$('#day_month').html("Year : ");
					$('#day_month_value').html(year);
					amchart.dataProvider = datapoints;
					amchart.categoryAxis.labelRotation = 0;
					amchart.categoryAxis.title = "Months";
					amchart.validateData();


				}
			});
		});
	}
}
/*////////////////  Getting Days from selected Month ///////////////*/
const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
function getdays(device_id, days, paramter) {
	var selected_date = document.getElementById('graph_date').value;
	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block');
		$(function () 
		{
			$.ajax({
				type: "POST",
				url: '../graphs/code/graph.php',
				traditional : true,  
				data:{D_ID:device_id, DATE:selected_date,  TYPE:days , PARAMTER:paramter},
				dataType: "json", 
				success: function(data) {
					$("#pre-loader").css('display', 'none');
					var dateObject = new Date(selected_date);
					var month = dateObject.getMonth() + 1;
					//month = month < 10 ? '0' + month : month;
					fun(data, monthNames[Number(month)-1]);
				},
				error: function () {
					$("#pre-loader").css('display', 'none');
					alert("Error loading data!");
				}
			});
		});
	}
}
function fun(data, period) {
	var json = data[0];
	datapoints = [];
	for (var i = 0; i < json.length; i++)
	{
		yValue1 = json[i].v_1;
		yValue2 = json[i].v_2;
		yValue3 = json[i].v_3;
		if(data[1].PHASE=="3PH")
		{
			datapoints.push({ year: json[i].date, valueR: Number(yValue1),  valueY: Number(yValue2), valueB: Number(yValue3) });
		}
		else
		{
			datapoints.push({ year: json[i].date, valueR: Number(yValue1)});
		}
	}
	$('#day_month').html("Month : ");
	$('#day_month_value').html(period);
	amchart.dataProvider = datapoints;
	amchart.categoryAxis.labelRotation = 0;
	amchart.categoryAxis.title = "DAYS";
	amchart.validateData();

}

function day_data(device_id, days, paramter) {
	var selected_date = document.getElementById('graph_date').value;
	if(device_id!=""&&device_id!=null)
	{
		$("#pre-loader").css('display', 'block');
		$(function () {
			$.ajax({
				type: "POST",
				url: '../graphs/code/graph.php',
				traditional : true,  
				data:{D_ID:device_id, DATE:selected_date, TYPE:days, PARAMTER:paramter},
				dataType: "json", 
				success: function(data) {
					$("#pre-loader").css('display', 'none');
					var json = data[0];
					datapoints = [];
					for (var i = 0; i < json.length; i++)
					{
						yValue1 = json[i].v_1;
						yValue2 = json[i].v_2;
						yValue3 = json[i].v_3;
						if(data[1].PHASE=="3PH")
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1),  valueY: Number(yValue2), valueB: Number(yValue3) });
						}
						else
						{
							datapoints.push({ year: json[i].date, valueR: Number(yValue1)});
						}
					}
					$('#day_month').html("Day : ");
					if (days !== "LATEST") {
						$('#day_month_value').html(selected_date);
					}
					else {
						$('#day_month_value').html(day);
					}
					amchart.dataProvider = datapoints;
					amchart.categoryAxis.labelRotation = 45;
					amchart.categoryAxis.title = "TIME";
					amchart.validateData();

				}
			});
		});
	}
}


	
<div class="d-flex flex-column flex-shrink-0 pt-0 p-3 bg-body sidebar-menu mt-xl-3" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="">
	<div class="offcanvas-header">       
		<button type="button" class="btn-close p-0" id="sidebar-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<ul class="nav nav-pills flex-column mb-auto" >
		<li>
			<a href="index.php" class="nav-link link-body-emphasis" aria-current="page">
				<i class="bi bi-grid"></i>
				Dashboard
			</a>
		</li>
		<li>
			<a href="device-list.php" class="nav-link link-body-emphasis">
				<i class="bi bi-list-ol"></i>
				Devices List
			</a>
		</li>
		<li>
			<a href="on-off-control.php" class="nav-link link-body-emphasis">
				<i class="bi bi-toggles"></i>
				On/Off Control
			</a>
		</li>
		
		<li>
			<a href="gis-map.php" class="nav-link link-body-emphasis">
				<i class="bi bi-geo-alt-fill"></i>
				GIS Map
			</a>
		</li>
		<li>
			<a href="data-report.php" class="nav-link link-body-emphasis">
				<i class="bi bi-table"></i>
				Data Report
			</a>
		</li>
		<li>
			<a href="#" class="nav-link btn-toggle collapsed"  data-bs-toggle="collapse" data-bs-target="#settings-collapse" aria-expanded="false">
				<i class="bi bi-gear"></i>
				Settings
			</a> 
			<div class="collapse " id="settings-collapse">
				<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small px-3">
					
					<li><a href="settings.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Threshold Settings</a></li>
					<li><a href="add-group-area.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Add Group or Area</a></li>
					<li><a href="location-details.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Location Update</a></li>
					<li><a href="notificationsettings.php" class=" nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Notification Settings</a></li>
					<li><a href="settings-iot.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>IoT Settings</a></li>
					<li><a href="pending-actions.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Pending Actions</a></li>

				</ul>
			</div>
		</li>
		<li>
			<a href="#" class="nav-link btn-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#alertsCollapse" aria-expanded="false">
				<i class="bi bi-bell-fill"></i>
				Alerts
			</a>
			<div class="collapse" id="alertsCollapse">
				<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small px-3">
					<li><a href="alerts-phases.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Phase alerts</a></li>
					<li><a href="alerts.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Alerts</a></li>	
					<li><a href="notifications.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Notification</a></li>
				</ul>
			</div>
		</li>
		<li>
			<a href="v-i-graph.php" class="nav-link link-body-emphasis">
				<i class="bi bi-bar-chart-fill"></i>
				Graphs
			</a>
		</li>
		<li>
			<a href="" class="nav-link btn-toggle collapsed"  data-bs-toggle="collapse" data-bs-target="#devicestatus" aria-expanded="false">
				<i class="bi bi-device-ssd"></i>
				Device Status
			</a>    
			<div class="collapse " id="devicestatus">
				<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small px-3">
					<li><a href="uptime-downtime.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Uptime-Downtime</a></li>
					<li><a href="glowing-hours.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded"><i class="bi bi-arrow-return-right"></i>Lights Glowing </a></li>
				</ul>
			</div>
		</li>
		<li>
			<a href="user-activity.php" class="nav-link link-body-emphasis">
				<i class="bi bi-lightning-charge-fill"></i>
				User Activity
			</a>
		</li>
		<li>
			<a href="download-data.php" class="nav-link link-body-emphasis ">
				<i class="bi bi-download"></i>
				Downloads
			</a> 
			
		</li>
		<li>
			<a href="complaints.php" class="nav-link link-body-emphasis ">
				<i class="bi bi-pencil-square"></i>
				Complaints
			</a> 
			
		</li>
		<li>
			<a href="#" class="nav-link btn-toggle collapsed"  data-bs-toggle="collapse" data-bs-target="#office-collapse" aria-expanded="false">
				<i class="bi bi-gear"></i>
				Office Use
			</a> 
			<div class="collapse " id="office-collapse">
				<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small px-3">					
					<li><a href="status-updates.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Status Updates</a></li>					
					<li><a href="module-diagnosis.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Module Diagnosis</a></li>
					<li><a href="software-update.php" class="nav-link link-body-emphasis d-inline-flex text-decoration-none rounded active_dp"><i class="bi bi-arrow-return-right"></i>Software Updates</a></li>
				</ul>
			</div>
		</li>

	</ul>
</div>
<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
	<title>Data report</title> 
	<?php
	include(BASE_PATH."assets/html/start-page.php");
	?>
	<div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
		<div class="container-fluid">
			<div class="row d-flex align-items-center">
				<div class="col-12 p-0">
					<p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Data report</span></p>
				</div>
			</div>
			<?php
			include(BASE_PATH."dropdown-selection/group-device-list.php");
			?>
			<div class="row">
				
				<div class="col-12 p-0">

					<div class="container-fluid text-center p-0 mt-3">

						<div class="row d-flex align-items-center">
							<div class="col-auto me-auto "><label><input type="checkbox" id="view_all_group_device"> View All</label> </div>
							<div class="col-auto">
								<div class="input-group">
									<input type="date" class="form-control" aria-label="date" id="search_date" value="yyyy-mm-dd" aria-describedby="button-addon2">
									<button class="btn btn-primary" type="button" onclick="search_records()">Search</button>
								</div>
							</div>
						</div>
					</div>


					<div class="table-responsive rounded mt-2 border">
						<table class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100">
							<thead class="sticky-header text-center" id="frame_data_table_header">
								<!-- <tr class="header-row-1">
									<th class="table-header-row-1"></th>
									<th class="table-header-row-1 col-size-1" >Updated at</th>
									<th class="table-header-row-1">ON/OFF Status</th>
									<th class="table-header-row-1" colspan="3">Phase Voltages (Volts)</th>
									<th class="table-header-row-1" colspan="3">Phase Currents (Amps)</th>
									<th class="table-header-row-1" colspan="4">KW</th>
									<th class="table-header-row-1" colspan="4">KVA</th>

									<th class="table-header-row-1" colspan="2">Energy (Units)</th>
									<th class="table-header-row-1" colspan="3">Power Factor</th>
									<th class="table-header-row-1" colspan="3">Frequency (Hz)</th>
									<th class="table-header-row-1">Signal Level</th>
									<th class="table-header-row-1">Location</th>
								</tr>
								<tr class="header-row-2">

									<th class="table-header-row-2">Device Id</th>
									<th class="table-header-row-2 col-size-1"></th>
									<th class="table-header-row-2"></th>
									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>

									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>

									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>
									<th class="table-header-row-2">Total</th>

									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>
									<th class="table-header-row-2">Total</th>

									<th class="table-header-row-2">kWh</th>
									<th class="table-header-row-2">kVAh</th>
									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>
									<th class="table-header-row-2">R</th>
									<th class="table-header-row-2">Y</th>
									<th class="table-header-row-2">B</th>
									<th class="table-header-row-2"></th>
									<th class="table-header-row-2"></th>

								</tr> -->
							</thead>
							<tbody id="frame_data_table">	
								<tr><td colspan="75" class="text-danger">Record Not Found </td> </tr>

							</tbody>
						</table>
					</div>
					<div class="col-12 d-flex justify-content-end">
						<button class="btn btn-secondary btn-sm mt-2" id="btn_add_more" onclick="add_more_records()">+ More Records</button>
					</div>

				</div>
			</div>
		</div>
	</div>
</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/data-report.js"></script>
<?php
include(BASE_PATH."assets/html/body-end.php");
include(BASE_PATH."assets/html/html-end.php");
?>
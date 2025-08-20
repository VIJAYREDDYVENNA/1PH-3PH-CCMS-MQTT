<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
	<title>Pending Activity</title>
	<?php 
	include(BASE_PATH."assets/html/start-page.php"); 
	?>
	<div class="d-flex flex-column flex-shrink-0 p-3 main-content">
		<div class="container-fluid">
			<div class="row d-flex align-items-center">
				<div class="col-12 p-0">
					<p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Pending Activity</span></p>
				</div>
			</div>
			<?php include(BASE_PATH."dropdown-selection/group-device-list.php"); ?>
			<div class="row">
				<div class="col-12 p-0 ">

					<div class="container-fluid mt-3 p-0">
						<div class="card ">
							<div class="card-header bg-primary bg-opacity-25 fw-bold">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<span class="me-2">Pending Actions</span>
										<a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info"
										data-bs-content="Pending Actions for Admin to Update"> <i class="bi bi-info-circle"></i> </a>
									</div>
									<div class="col-auto d-flex align-items-center flex-wrap">
										<div class="mb-2 ps-2 custom-size">
											<button type="button" class="btn btn-primary" onclick="btn_refresh_data()">Refresh</button>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive rounded mt-2 border">
									<table id="pending-action-table" class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100">

									</table>
								</div>
							</div>
						</div>
						
					</div>
					<!-- ==================================================== -->
					

				</div>

			</div>
		</div>
	</div>
</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.min.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/pending-actions.js"></script>
<script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>




<?php include(BASE_PATH."assets/html/body-end.php"); ?>
<?php include(BASE_PATH."assets/html/html-end.php"); ?>


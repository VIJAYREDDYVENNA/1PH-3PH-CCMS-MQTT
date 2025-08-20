
<div class="modal fade" id="group-create-component" tabindex="-1" aria-labelledby="group-create-componentLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-success" >Add Group/Area</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<div class="col-12 p-0 pe-sm-2">
					<div class="card mt-3 ">
						<div class="card-header bg-primary bg-opacity-25 fw-bold">
							<span class="me-2">Add Area</span>
							<a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="update gps location"> <i class="bi bi-info-circle"></i></a>
						</div>
						<div class="card-body">
							<form id="areaForm">
								<div class="row">
									<div class="col-md-6 mb-2 mt-1">
										<label for="state" class="form-label">State:</label>
										<select id="state" class="form-select" onchange="handleStateChange()">
											<option value="">Select State</option>
											<option value="add-state" class="text-primary fw-bold">Add State</option>
										</select>
										<input type="text" class="form-control mt-2 d-none" id="other-state" placeholder="Enter State">
									</div>
									<div class="col-md-6 mb-2 mt-1">
										<label for="district" class="form-label">District:</label>
										<select id="district" class="form-select" onchange="handleDistrictChange()">
											<option value="">Select District</option>
											<option value="add-district" class="text-primary fw-bold">Add District</option>
										</select>
										<input type="text" class="form-control mt-2 d-none" id="other-district" placeholder="Enter District">
									</div>
									<div class="col-md-6 mb-2 mt-1">
										<label for="town" class="form-label">Town or City:</label>
										<input type="text" class="form-control" id="town" placeholder="Enter Town or City">
									</div>
									<div class="col-md-6 mb-2 mt-1">
										<label for="area" class="form-label">Area or Group:</label>
										<input type="text" class="form-control" id="group" placeholder="Enter Area or Group">
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between align-items-center">
									<div class="w-100 text-center">
										<button type="button" class="btn btn-primary mb-2" onclick="updateArea()">Add</button>
									</div>
								</div>
							</form>

						</div>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

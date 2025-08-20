<div class="modal fade" id="complaints_close_Modal" tabindex="-1" role="dialog" aria-labelledby="complaints_close_Modal" aria-hidden="true">
	<div class="modal-dialog " role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" >Comaplaint Close/Status Update</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<div class="row justify-content-center">
					<div class="col-12">
						<label><span>Complaint No: <b><span id="complaint_id_close" class="complaint_id"></span></b> </span></label>
					</div>

					<div class="col-12 mt-2 d-flex align-items-center">
						<input type="checkbox" id="accept_close"/>
						<span class="small ms-2"> Do you want to close the complaint?</span>

					</div>

					<div class="col-12 mt-2">
						<label>Status:</label>
					</div>
					<div class="col-12 d-flex align-items-center justify-content-center">
						<textarea  class="form-control"  maxlength="250" rows="4" cols="60"  id="complaint_update_status"></textarea> 
					</div>
					<div class="col-12 text-left mt-2">
						<small> This accepts up to 250 characters.</small>
					</div>

				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="closing_complaint">Submit</button>
			</div>
		</div>
	</div>
</div>
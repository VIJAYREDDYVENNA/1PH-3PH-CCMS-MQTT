<div class="modal fade" id="tracking_complaints_Modal" tabindex="-1" role="dialog" aria-labelledby="tracking_complaints_Modal" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Comaplaint Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row p-0 m-0" >
          <div class="col-12">
            <label><span>Complaint No: <b><span id="complaint_id_track" class="complaint_id"></span></b> </span></label>
          </div>
          <div class="col-12">

            <div class="table-responsive-1 rounded mt-2 border ">
              <table class="table table-striped table-type-1 w-100 text-center">
                <thead>
                  <tr>
                    <th class="bg-primary-subtle">Status</th>
                    <th class="bg-primary-subtle">Updated By</th>
                    <th class="bg-primary-subtle">Date-Time</th>
                  </tr>
                </thead>
                <tbody id="complaint_status_update">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick="show_more_compalints_history()">+ Show More</button>
        <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#complaints_close_Modal">Update Report</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
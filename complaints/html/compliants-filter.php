
<div class="modal fade" id="complaints_filter_Modal" tabindex="-1" role="dialog" aria-labelledby="complaints_filter_Modal" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Filter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="row justify-content-center " >
          <div class="col-6 text-right d-flex align-items-center">
            <select class="form-select mx-2" id="selection">
              <option value="1">Selected Group/Area</option>
              <option value="2">Selected Device ID</option>

            </select>
          </div>
          <div class="col-6 text-right d-flex align-items-center">
            <select class="form-select mx-2"  id="complaint_status">
              <option value="1">ALL</option>
              <option value="2">Pending</option>
              <option value="3">In-Progress</option>
              <option value="4">Resolved</option>
            </select>
          </div>
          
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="update_complaints">Submit</button>
        <!--  <button type="button" class="btn btn-primary" id="" >Update</button> -->
        <!--   <button type="button" class="btn btn-secondary" id="btn_scd_update" >up</button> -->
      </div>
    </div>
  </div>
</div>
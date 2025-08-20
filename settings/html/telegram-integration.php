<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Telegram Group</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="NewChat-tab" data-bs-toggle="tab" data-bs-target="#NewChat" type="button" role="tab" aria-controls="NewChat" aria-selected="true">New Chat ID</button>
            <button class="nav-link" id="add-device-to-group-tab" data-bs-toggle="tab" data-bs-target="#add-device-to-group" type="button" role="tab" aria-controls="add-device-to-group" aria-selected="false">Add Devices</button>
            <button class="nav-link" id="remove_devices-tab" data-bs-toggle="tab" data-bs-target="#remove_devices" type="button" role="tab" aria-controls="remove_devices" aria-selected="false">Remove Devices</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="NewChat" role="tabpanel" aria-labelledby="NewChat-tab" tabindex="0">
            <div class=" m-2 p-2">
              <div class="form-group">
                <label for="exampleInputEmail1">Chat ID</label>
                <input type="text" class="form-control" id="chat_id" aria-describedby="ChartIDHelp" placeholder="Chat ID: -1001709XXXXX">

                <p>If don't know Chat ID, Please check <a href="../docs/ccms_telegram_group.pdf" target="_blank">here</a>.</p>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Group Name</label>
                <input type="text" class="form-control" id="telegram_group_name" placeholder="Group Name">
              </div>
              <div class="col-12 d-flex justify-content-center mt-2">
                <button type="button" class="btn btn-primary me-1" id="test_chat_id">Check Test Msg </button>
                <button type="button" class="btn btn-primary" id="save_chat_id">Submit</button>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="add-device-to-group" role="tabpanel" aria-labelledby="add-device-to-group-tab" tabindex="0">
            <div class="row" >
              <div class="col-md-12">
                <div class="row justify-content-center " >
                  <div class="col-12">
                    <div class="col-sm-12">
                      <div class="custom-control custom-checkbox pl-3">
                        <input type="checkbox" id="select_all_for_telegram" style="width: auto; margin-top:10px" />
                        <label class="small" > Select All</label>
                      </div>
                    </div>
                    <div class="col-sm-12 text-right d-flex align-items-center ">
                      <div class="col-12">
                        <select id="add_devices_to_telegram_group" class="multi_selection_device_id col-12 telegram_group_add"  multiple size="30" style="max-height: 250px;">
                          <?php
                          include("../dropdown-selection/device_id_list.php");
                          ?>
                        </select>
                      </div>
                    </div>


                    <div class="col-sm-12 text-right d-flex align-items-center ">
                      <div class="col-12">
                        <select id="update_telegram_groups" class="form-select pointer">
                          <?php
                          include("../settings/code/user-telegram-groups.php");
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-12 justify-content-end d-flex mt-2 me-3 pb-2">
                      <button type="button" class="btn btn-primary me-1" id="update_device_to_group">Update</button>
                      <button type="button" class="btn btn-danger me-1" onclick="clear_selection_add_devices()">Clear</button>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="tab-pane fade" id="remove_devices" role="tabpanel" aria-labelledby="remove_devices-tab" tabindex="0">
            <div class="row" >
              <div class="col-md-12">
                <div class="row justify-content-center " >
                  <div class="col-12">
                    <div class="col-sm-12 text-right d-flex align-items-center mt-2">
                      <select id="updated_telegram_groups" class="form-select pointer">
                        <?php
                        include("../settings/code/user-telegram-groups.php");
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-12 text-right d-flex align-items-center mt-2">
                      <select id="group_devices" class="multi_selection_device_id col-12" multiple size="30" style="height: 250px;">
                      </select>
                    </div>


                    <div class="col-12 justify-content-end d-flex me-3 pb-2 mt-2">
                      <button type="button" class="btn btn-primary me-1" id="remove_device_from_group">Remove</button>
                      <button type="button" class="btn btn-danger me-1" onclick="clear_selection()">Clear</button>

                    </div>
                  </div>
                </div>
              </div>

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
<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars =SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <title>Complaints</title>
    <?php
    include(BASE_PATH."assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Complaints</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH."dropdown-selection/group-device-list.php");
            ?>

            <div class="row justify-content-end align-items-center mt-3 p-0">
                <div class="col-auto p-0 mb-2">
                    <div class="input-group">
                      <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#complaints_filter_Modal">Filter</button>
                      <button type="button" class="btn btn-primary ml-2" data-bs-toggle="modal" data-bs-target="#raise_complaints_Modal" >Raise Complaint</button>


                  </div>
              </div>
          </div>
          

          <div class="row">
            <div class="col-12 p-0">
                <div class="table-responsive rounded border">
                    <table class="table table-striped table-type-1 w-100 text-center deviceListSearch">
                        <thead>
                            <tr>
                                <th class="table-header-row-1">Complaint No</th>
                                <th class="table-header-row-1">Date</th>
                                <th class="table-header-row-1">Device ID</th>
                                <th colspan="2" class="table-header-row-1">Complaint</th>

                                <th class="table-header-row-1">Status</th>
                                <th class="table-header-row-1"></th>
                            </tr>
                        </thead>
                        <tbody id="complaints_list_table">
                            <tr>
                                <td class="text-danger" colspan="12">Complaints not found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <button class="btn btn-secondary" onclick="fetch_more_records()">+ Show More</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php
include(BASE_PATH."complaints/html/compliants-filter.php");
include(BASE_PATH."complaints/html/raise-complaint.php");
include(BASE_PATH."complaints/html/complaints-tracking.php");
include(BASE_PATH."complaints/html/update_status.php");
include(BASE_PATH."complaints/html/complaint_close.php");
?>
</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/complaints.js"></script>
<?php
include(BASE_PATH."assets/html/body-end.php");
include(BASE_PATH."assets/html/html-end.php");
?>

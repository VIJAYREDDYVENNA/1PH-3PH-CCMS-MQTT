<?php
require_once 'config-path.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
$permission_check = 0;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <title>Add New Area or Group</title>
    <?php
    include (BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Add Group/Area</span></p>
                </div>
            </div>
            <?php
            include (BASE_PATH . "dropdown-selection/group-device-list.php");
            //include (BASE_PATH."dropdown-selection/device-list.php");
            ?>
            <div class="row ">
                <div class="col-xl-6 p-0 ">
                    <div class="card mt-3 ">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <span class="me-2">Add Devices</span>
                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-title="Info" data-bs-content="Assign Devices to the Group / Area they are Allocated">
                                <i class="bi bi-info-circle"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-8">
                                    <div class="mb-2 ms-2">
                                        <label for="frame_update_time" class="form-label">Select Group/Area</label>

                                        <select class="form-select" id="group_name" onchange="handleGroupChange()">
                                            <option value="">Select Group/Area</option>
                                            <?php
                                            $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
                                            if ($conn) {
                                                $sql_group_list = "SELECT `s_id`,`state`,`district`,`city_or_town`,`device_group_or_area` FROM `device_list_by_group` WHERE `login_id`=? GROUP by device_group_or_area;";
                                                $stmt = mysqli_prepare($conn, $sql_group_list);

                                                if ($stmt) {
                                                    mysqli_stmt_bind_param($stmt, "s", $user_login_id);
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);

                                                    if (mysqli_num_rows($result) > 0) {
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $s_id = $row['s_id'];
                                                            $state = $row['state'];
                                                            $district = $row['district'];
                                                            $city_or_town = $row['city_or_town'];
                                                            $device_group_or_area = $row['device_group_or_area'];

                                                            echo "<option value='$s_id'>$state-$district-$city_or_town-$device_group_or_area </option>";
                                                        }
                                                    }
                                                    mysqli_stmt_close($stmt); 
                                                }
                                                mysqli_close($conn); 
                                            }
                                            ?>
                                            <option value="_add_new_group">Add New Group/Area</option>
                                        </select>
                                    </div>

                                    <div class="container mt-2">
                                        <?php
                                        include(BASE_PATH."dropdown-selection/multiple-devices.php")
                                        ?>
                                        <div class="row mt-2">
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary mb-2" onclick="updateDevice()">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($role=="SUPERADMIN"){?>

                <div class="col-xl-6 p-0 ps-xl-3">
                    <div class="card mt-3 ">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <span class="me-2">Group Selection</span>
                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-title="Info" data-bs-content="Update the Group/Area View for Device Display">
                                <i class="bi bi-info-circle"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2 d-flex justify-content-center">
                                <div class="col-sm-8 ">
                                    <div class="mb-2 ms-2">
                                        <label for="frame_update_time" class="form-label">Select Group/Area</label>

                                        <select class="form-select" id="group_for_view" >
                                            <?php
                                            if($role=="SUPERADMIN")
                                            {
                                                echo '<option value="state">State Level Devices</option>';
                                                echo '<option value="district">District Level Devices</option>';
                                                echo '<option value="city_or_town">City/Zone Level Devices</option>';
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else if($role=="ADMIN")
                                            {
                                                echo '<option value="state">State Level Devices</option>';
                                                echo '<option value="district">District Level Devices</option>';
                                                echo '<option value="city_or_town">City/Zone Level Devices</option>';
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else if($role=="DISTRICT")
                                            {
                                                echo '<option value="district">District Level Devices</option>';
                                                echo '<option value="city_or_town">City/Zone Level Devices</option>';
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else if($role=="ZONE")
                                            {
                                                echo '<option value="city_or_town">City/Zone Level Devices</option>';
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else if($role=="AREA")
                                            {
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else if($role=="TECHNICIAN")
                                            {
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            else
                                            {
                                                echo '<option value="device_group_or_area">Group/Area Level Devices</option>';
                                            }
                                            ?>


                                        </select>

                                    </div>

                                    <div class="container mt-2">
                                        <div class="row mt-2">
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary mb-2" onclick="updateGroupDevice()">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
                </div>
                </div>
</main>

<?php
include(BASE_PATH."/devices/html/group-creation.php");
?>


<script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>

<script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/project/group_creation.js"></script>
<script src="<?php echo BASE_PATH; ?>json-data/json-data.js"></script>


<?php
include (BASE_PATH . "assets/html/body-end.php");
include (BASE_PATH . "assets/html/html-end.php");
?>
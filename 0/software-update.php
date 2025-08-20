<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();

$send = ["status" => "", "message" => ""];

date_default_timezone_set('Asia/Kolkata');
$offset_address = 0;
$size = 128 * 1024;
$byte_arr = array_fill(0, $size, 0xFF);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["inputfile"]) && $_FILES["inputfile"]["size"] > 0) {
        $FileData = file_get_contents($_FILES["inputfile"]["tmp_name"]);

        $data = $FileData;
        $deviceid = $_POST['device_id'] ?? '';

        if (!empty($data)) {
            if (!empty($deviceid)) {
                $db = strtolower($deviceid);
                include('../config_db/config.php');
                try {
                    $conn = mysqli_connect(HOST, USERNAME, PASSWORD);
                    if (!$conn) {
                        $send = ["status" => "error", "message" => "Database connection failed: " . mysqli_connect_error()];
                    } else {
                        $file_data = trim($data);
                        $data_array = explode("\r\n", $file_data);

                        for ($m = 1; $m < count($data_array) - 1; $m++) {

                            $file_line = $data_array[$m];
                            $s2_removed_chars = substr($file_line, 0, - (strlen($file_line) - 2));

                            $s2_removed = substr($file_line, 2);
                            $dt = substr($s2_removed, 0, - (strlen($s2_removed) - 2));

                            $a = array_map('hexdec', str_split($dt, 2));
                            $no_byte = $a[0];
                            $first_2_char_removed = substr($s2_removed, 2);

                            $removed_chars_str = 0;
                            $remove_byte = 0;
                            if ($s2_removed_chars == "S2") {
                                $removed_chars_str = 6;
                                $remove_byte = 4;
                            } else {
                                $removed_chars_str = 4;
                                $remove_byte = 3;
                            }

                            $_6_char_to_3_byte = substr($first_2_char_removed, 0, - (strlen($first_2_char_removed) - $removed_chars_str));

                            $b = array_map('hexdec', str_split($_6_char_to_3_byte, $removed_chars_str));
                            $mot_addres = $b[0];

                            $address_removed = substr($first_2_char_removed, $removed_chars_str);
                            $last_2_chars_removed = substr($address_removed, 0, -2);


                            $end_loop = strlen($last_2_chars_removed) / 2;
                            $final_string_convert = "";
                            $final_string_convert = $last_2_chars_removed;



                            $hex_array = [];

                            for ($i = 0; $i < $end_loop; $i++) {

                                if (strlen($final_string_convert) > 2) {

                                    $to_convert_hex = substr($final_string_convert, 0, - (strlen($final_string_convert) - 2));
                                } else {
                                    $to_convert_hex = $final_string_convert;
                                }


                                $b = array_map('hexdec', str_split($to_convert_hex, 2));
                                $hex_array[$i] = $b[0];

                                $final_string_convert = substr($final_string_convert, 2);
                            }

                            $index = $mot_addres - $offset_address;

                            for ($lp = 0; $lp < $no_byte - $remove_byte; $lp++) {
                                $byte_arr[$index] = $hex_array[$lp];
                                $index++;
                            }
                        }

                        $hex = "";
                        $sum = 0;
                        foreach ($byte_arr as $byte) {
                            $sum += $byte;
                            $string = str_pad(dechex($byte), 2, "0", STR_PAD_LEFT);
                            $hex .= strtoupper($string);
                        }

                        $results = ~$sum;
                        $x = strtoupper(dechex($results & 0xFF));
                        $x = str_pad($x, 2, "0", STR_PAD_LEFT);

                        $end_str = end($data_array);
                        $final_hex_file = $data_array[0] . "\r\n" . $hex . "\r\n" . $end_str . "\r\nS7030000" . $x . "\r\n";

                        $sql = "INSERT INTO `$db`.`software_update`(`software`,`date_time`) VALUES ('$final_hex_file', current_timestamp())";
                        if (mysqli_query($conn, $sql)) {
                            if (mysqli_query($conn, "INSERT INTO `$db`.`device_settings` (`setting_type`, `setting_flag`) VALUES('SOFTWARE', '1') ON DUPLICATE KEY UPDATE setting_flag='1'")) {
                                if (mysqli_query($conn, "INSERT INTO `$db`.`device_settings` (`setting_type`, `setting_flag`) VALUES('READ_SETTINGS', '1') ON DUPLICATE KEY UPDATE setting_flag='1'")) {
                                    $send = ["status" => "success", "message" => "File uploaded successfully"];
                                } else {
                                    $send = ["status" => "error", "message" => "Failed to update READ_SETTINGS"];
                                }
                            } else {
                                $send = ["status" => "error", "message" => "Failed to update SOFTWARE settings"];
                            }
                        } else {
                            $send = ["status" => "error", "message" => "File data insertion failed"];
                        }
                        mysqli_close($conn);
                    }
                } catch (Exception $e) {
                    $send = ["status" => "error", "message" => "Database error: " . $e->getMessage()];
                }
            } else {
                $send = ["status" => "error", "message" => "Device ID not selected"];
            }
        } else {
            $send = ["status" => "error", "message" => "Uploaded file content is empty"];
        }
    } else {
        $send = ["status" => "error", "message" => "No file selected or file is empty"];
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Software-Update</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Software-Update</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 p-0  ">
                </div>
                <div class="col-sm-6 p-0 ">

                    <div class="card mt-3">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">Upload Software</span>
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Upload new software to the device">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('SOFTWARE')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a>
                        </div>

                        <div class="card-body row">
                            <form class="col-md-12" id="ccms-data" method="post" enctype="multipart/form-data">
                                <div class="pb-2">
                                    <label for="select-group" class="form-label">Group:</label>
                                    <?php include(BASE_PATH . "dropdown-selection/group_selection.php"); ?>

                                    <label for="select-device" class="form-label mt-2">Device:</label>
                                    <?php include(BASE_PATH . "dropdown-selection/device_selection.php"); ?>

                                    <label for="inputfile" class="form-label mt-2">Select File:</label>
                                    <input type="file" name="inputfile" id="inputfile" class="form-select">
                                </div>

                                <!-- Error/Success message display -->
                                <div class="mt-2">
                                    <?php
                                    if (!empty($send) && $send['status'] === 'error'):
                                        echo "<div class='alert alert-danger'>" . htmlspecialchars($send['message']) . "</div>";
                                    elseif (!empty($send) && $send['status'] === 'success'):
                                        echo "<div class='alert alert-success'>" . htmlspecialchars($send['message']) . "</div>";
                                    endif;
                                    ?>
                                </div>

                                <div class="d-flex justify-content-center align-items-center mt-2">

                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <button type="button" class="btn btn-secondary ms-2" onclick="updateStatus()" data-bs-toggle="modal" data-bs-target="#sofware-update-status">Check Status</button>

                                </div>
                            </form>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="w-100 text-center">

                                <div class="mt-1 text-start">
                                    <p class="text-danger">* Update the OTA to device. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    </main>
    <?php
    include(BASE_PATH . "settings/html/update-status-modal.php");
    include(BASE_PATH . "settings/html/software-update-status.php");
    ?>
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/software-update.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
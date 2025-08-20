<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
	<title>Loaded Settings</title> 
	<?php
	include(BASE_PATH."assets/html/start-page.php");
	?>
	<div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
		<div class="container-fluid">
                    <div class="row d-flex align-items-center">
                        <div class="col-12 p-0">
                            <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Loaded Settings</span></p>
                        </div>
                    </div>
                    <?php
                    include(BASE_PATH."dropdown-selection/group-device-list.php");
                    ?>
                    <div class="row">

                        <div class="container mt-2 p-0">
                            <div class="row justify-content-end align-items-center mt-2 ">
                                <div class="col-auto ms-2">
                                    <button type="button" class="btn btn-primary w-md-auto" onclick="openSettingsModal()">Settings</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 p-0">
                            <div class="table-responsive rounded mt-2 border">
                                <table class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100" id="dataTable">
                                    <thead class="sticky-header text-center">
                                        <tr>
                                            <th class="bg-primary text-white">Device ID</th>
                                            <th class="bg-primary text-white col-size-1" >Mode</th>
                                            <th class="bg-primary text-white">Value 1</th>
                                            <th class="bg-primary text-white">Value 2</th>
                                            <th class="bg-primary text-white">Value 3</th>
                                            <th class="bg-primary text-white">Value 4</th>
                                            <th class="bg-primary text-white">Value 5</th>
                                            <th class="bg-primary text-white">Value 6</th>
                                            <th class="bg-primary text-white">Value 7</th>
                                            <th class="bg-primary text-white">Value 8</th>
                                            <th class="bg-primary text-white">Value 9</th>
                                            <th class="bg-primary text-white">Value 10</th>
                                            <th class="bg-primary text-white">Value 11</th>
                                            <th class="bg-primary text-white">Value 12</th>
                                            <th class="bg-primary text-white">Value 13</th>
                                            <th class="bg-primary text-white">Value 14</th>
                                            <th class="bg-primary text-white">Value 15</th>
                                            <th class="bg-primary text-white">Value 16</th>
                                            <th class="bg-primary text-white">Value 17</th>
                                            <th class="bg-primary text-white">Value 18</th>
                                            <th class="bg-primary text-white">Value 19</th>
                                            <th class="bg-primary text-white">Value 20</th>
                                            <th class="bg-primary text-white">Value 21</th>
                                            <th class="bg-primary text-white">Value 22</th>
                                            <th class="bg-primary text-white">Value 23</th>
                                            <th class="bg-primary text-white">Value 24</th>
                                            <th class="bg-primary text-white">Value 25</th>
                                            <th class="bg-primary text-white">Value 26</th>
                                            <th class="bg-primary text-white">Value 27</th>
                                            <th class="bg-primary text-white">Value 28</th>
                                            <th class="bg-primary text-white">Value 29</th>
                                            <th class="bg-primary text-white">Value 30</th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody class="text-center">	
                                        <tr>
                                            <td>CCMS_8</td>
                                            <td>DEFAULT</td>
                                            <td>60</td>
                                            <td>500</td>
                                            <td>159</td>
                                            <td>3311</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>241</td>
                                            <td>3310</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>77</td>
                                            <td>3321</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>115</td>
                                            <td>44</td>
                                            <td>65</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>ISTL_CCMS_8</td>
                                            <td>ISTLCCMS8</td>
                                            <td>CCMS=CCMS_8</td>
                                            <td>XXXXXXXX</td>
                                            <td>11:20:22</td>
                                            <td>09/07/2024</td>
                                        </tr>

                                        <tr>
                                            <td>CCMS_8</td>
                                            <td>DEFAULT</td>
                                            <td>60</td>
                                            <td>500</td>
                                            <td>159</td>
                                            <td>3311</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>241</td>
                                            <td>3310</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>77</td>
                                            <td>3321</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>115</td>
                                            <td>44</td>
                                            <td>65</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>ISTL_CCMS_8</td>
                                            <td>ISTLCCMS8</td>
                                            <td>CCMS=CCMS_8</td>
                                            <td>XXXXXXXX</td>
                                            <td>11:20:22</td>
                                            <td>09/07/2024</td>
                                        </tr>

                                        <tr>
                                            <td>CCMS_8</td>
                                            <td>DEFAULT</td>
                                            <td>60</td>
                                            <td>500</td>
                                            <td>159</td>
                                            <td>3311</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>241</td>
                                            <td>3310</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>77</td>
                                            <td>3321</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>115</td>
                                            <td>44</td>
                                            <td>65</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>ISTL_CCMS_8</td>
                                            <td>ISTLCCMS8</td>
                                            <td>CCMS=CCMS_8</td>
                                            <td>XXXXXXXX</td>
                                            <td>11:20:22</td>
                                            <td>09/07/2024</td>
                                        </tr>

                                        <tr>
                                            <td>CCMS_8</td>
                                            <td>DEFAULT</td>
                                            <td>60</td>
                                            <td>500</td>
                                            <td>159</td>
                                            <td>3311</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>241</td>
                                            <td>3310</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>77</td>
                                            <td>3321</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>115</td>
                                            <td>44</td>
                                            <td>65</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>ISTL_CCMS_8</td>
                                            <td>ISTLCCMS8</td>
                                            <td>CCMS=CCMS_8</td>
                                            <td>XXXXXXXX</td>
                                            <td>11:20:22</td>
                                            <td>09/07/2024</td>
                                        </tr>

                                        <tr>
                                            <td>CCMS_8</td>
                                            <td>DEFAULT</td>
                                            <td>60</td>
                                            <td>500</td>
                                            <td>159</td>
                                            <td>3311</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>241</td>
                                            <td>3310</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>123</td>
                                            <td>52</td>
                                            <td>60</td>
                                            <td>77</td>
                                            <td>3321</td>
                                            <td>1000</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>115</td>
                                            <td>44</td>
                                            <td>65</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>27000</td>
                                            <td>17000</td>
                                            <td>3500</td>
                                            <td>ISTL_CCMS_8</td>
                                            <td>ISTLCCMS8</td>
                                            <td>CCMS=CCMS_8</td>
                                            <td>XXXXXXXX</td>
                                            <td>11:20:22</td>
                                            <td>09/07/2024</td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
   <?php
   include("../office-use/modals/loadedsettings-settingmodal.php")
   ?>
            

</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>js_modal_scripts/office-use-js/loaded-settings.js"></script>
<?php
include(BASE_PATH."assets/html/body-end.php");
include(BASE_PATH."assets/html/html-end.php");
?>
<script>
   
</script>
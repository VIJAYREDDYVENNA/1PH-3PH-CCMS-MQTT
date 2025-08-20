<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$login_error = "";

include("../base-path/config-path.php");
require_once '../session/session-manager.php';

SessionManager::startSession();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$user_login_id = strtolower($_POST['userid']);
	$password = $_POST['password'];
	SessionManager::login("khammam", $user_login_id,  $password);
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>


	<title>Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="generator" content="Hugo 0.122.0">
	<link href="<?php echo BASE_PATH ?>assets/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo BASE_PATH ?>assets/css/sidebars.css" rel="stylesheet">
	<link href="<?php echo BASE_PATH ?>assets/css/istl-styles.css" rel="stylesheet">
	<link href="<?php echo BASE_PATH ?>assets/css/login-styles.css" rel="stylesheet">
	<link href="../assets/css/login-client.css" rel="stylesheet">
	<script src="<?php echo BASE_PATH ?>assets/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo BASE_PATH ?>assets/js/sidebars.js"></script>
	<script src="<?php echo BASE_PATH ?>assets/js/color-modes-login.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script type="text/javascript">
		localStorage.removeItem("Devive_ID_Selection");
		localStorage.removeItem("SELECTED_ID");
		localStorage.removeItem("GroupName");
		localStorage.removeItem("GroupNameValue");
	</script>

	<?php
	include(BASE_PATH . "assets/html/body-start.php");
	include(BASE_PATH . "assets/icons-svg/icons.php");
	include(BASE_PATH . "assets/html/theme-selection.php");
	?>
	<div class="background all ">
		<div class="login-backdrop">
			<div class="row d-flex  mt-2">
				<div class="col-md-2 col-xl-3"></div>
				<div class="col-12 col-md-8 col-xl-6 justify-content-center">
					<div class="title-container fs-sm-18">
						<h1>Khammam Municipal Corporation <br>Street Lighting-Centralized Control Monitoring System</h1>
					</div>
				</div>
				<!-- <div class="title-container">
						<h1 class="responsive-title text-center">
							Nandyala Municipality <br>Street Lighting-Centralized Control Monitoring System
						</h1>
					</div> -->
				<div class="col-md-2 col-xl-3"></div>
			</div>
			<?php
			include("login-card.php");
			include(BASE_PATH . "login/registration-toast.php");
			?>
		</div>
	</div>
</body>
<script src="<?php echo BASE_PATH; ?>assets/js/project/preloader.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/project/password-show-hide.js"></script>

</html>
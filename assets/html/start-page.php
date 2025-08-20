<?php

$MenuVariables = $_SESSION['menu_permission_variables'] ?? ''; // Handle if session variable is not set
$menu_list = array_map('trim', explode(',', $MenuVariables)); // Explode and trim to avoid extra spaces

function hasPermission($key, $menu_list) {
	return in_array($key, $menu_list);
}

include(BASE_PATH."assets/html/html-link.php");
include(BASE_PATH."assets/html/body-start.php");
include(BASE_PATH."assets/icons-svg/icons.php");
include(BASE_PATH."assets/html/theme-selection.php");
include(BASE_PATH."assets/html/main-start.php");
include(BASE_PATH."assets/html/navbar.php");
include(BASE_PATH."assets/html/sidebar.php");
?>

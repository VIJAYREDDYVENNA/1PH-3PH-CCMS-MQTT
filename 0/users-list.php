<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Add New User</title>
    <style>
        /* CSS for fixed headers and properly positioned dropdowns */

        /* Make header sticky and fixed */
        .table-sticky-header thead {
            position: sticky;
            top: 0;
            z-index: 10;
            /* background-color: #f8f9fa; */
            /* Match your table header background color */
        }

        /* Fix for table header alignment */
        .table-sticky-header th {
            min-width: max-content;
            /* Ensure headers don't shrink */
            position: relative;
        }

        /* Ensure the table header and data cells are properly aligned */
        .table-sticky-header th,
        .table-sticky-header td {
            white-space: nowrap;
            /* Prevent text wrapping */
            padding: 0.5rem;
            /* Consistent padding */
        }

  

        /* Style for dropdown menus */
        .dropdown-menu-user-list {
            position: absolute;
            top: 100%;
            right: 0;
            left: auto;
            z-index: 1000;
            float: left;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
        }

        /* Dropdown positioning */
        .btn-group.dropend {
            position: relative;
        }

        /* Ensure dropdown menu stays inside table */
        .table-responsive {
            overflow-x: auto;
            position: relative;
        }

        /* Set a shade for action column when scrolling */
      

        /* Make dropdown open to the left rather than right */
        .dropend .dropdown-menu {
            top: 0;
            right: auto;
            left: auto;
            margin-top: 0.125rem;
            margin-left: 0.125rem;
        }
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Add New User</span></p>
                </div>
            </div>
            <div class="row">
                <div class="container mt-2 p-0">
                    <div class="row justify-content-end align-items-center mt-2 ">
                        <div class="col-auto mb-2 p-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                                <button class="btn btn-primary" type="button" onclick="search_users()">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div class="col-auto mb-2 ms-2">
                            <button type="button" class="btn btn-primary w-md-auto" onclick="addUser()">Add User</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0">
                    <div class="table-responsive rounded mt-2 border">
                        <table class="table table-striped styled-table table-sticky-header table-type-1 w-100 text-center resulttable" id="user_list_table">


                        </table>
                    </div>

                    <div class="pagination-wrapper mt-2">
                        <div class="row">
                            <div class="col">
                                <div class="row g-2 align-items-center d-flex">
                                    <div class="col-auto">
                                        <label for="items-per-page" class="form-label">Items per page:</label>
                                    </div>
                                    <div class="col-auto">
                                        <select id="items-per-page" class="form-select">
                                            <option value="10">10</option>
                                            <option value="20" selected>20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="100">200</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="col">
                                <div class="pagination-container">
                                    <nav>
                                        <ul class="pagination justify-content-end " id="pagination">
                                        </ul>
                                    </nav>
                                </div>
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
    include(BASE_PATH . "account/html/create-new-user.php");
    include(BASE_PATH . "account/html/user-managing-devices.php");
    include(BASE_PATH . "account/html/update-button.php");
    include(BASE_PATH . "account/html/add-to-group.php");
    include(BASE_PATH . "account/html/edit-user-details.php");
    include(BASE_PATH . "account/html/permissions.php");
    include(BASE_PATH . "account/html/menu-permissions.php");
    include(BASE_PATH . "account/html/account-action.php");
    ?>

    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <!-- <script src="<?php echo BASE_PATH; ?>js_modal_scripts/searchbar.js"></script> -->
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>

    <script src="<?php echo BASE_PATH; ?>assets/js/project/user-list1.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");

    ?>
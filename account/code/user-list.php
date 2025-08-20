<?php

require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
$user_type = $sessionVars['user_type'];

$permission_check = 0;

$response = ["data" => "", "totalPages" => ""];
$device_ids = "";
$parameter_value = "";
$parameter = "";


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT); // Ensure it is sanitized as an integer
    $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT); // Ensure it is sanitized as an integer
    $search_user = filter_input(INPUT_GET, 'search_user', FILTER_SANITIZE_STRING); // Ensure it is sanitized as an integer

    /*$page= $_GET['page'];
$limit= $_GET['limit'];*/

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $page = sanitize_input($page, $conn);
    $limit = sanitize_input($limit, $conn);
    $search_user = sanitize_input($search_user, $conn);

    $page = $page ? intval($page) : 1;
    $limit = $limit ? intval($limit) : 20;

    $offset = ($page - 1) * $limit;

    if ($role == "SUPERADMIN" && $user_type == "MANAGER") {
        $totalQuery = "";
        $sql = "";

        if ($search_user == "") {
            //account_delete='1'and
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE  id!='$user_login_id'";
            $sql = "SELECT * FROM login_details WHERE id!='$user_login_id' LIMIT ? OFFSET ?";
        } else {
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE  (`user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%') AND id!='$user_login_id'";

            $sql = "SELECT * FROM login_details WHERE  (`user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%') AND id!='$user_login_id' LIMIT ? OFFSET ?";
        }


        $totalResult = mysqli_query($conn, $totalQuery);

        if (!$totalResult) {
            die("Query failed: " . mysqli_error($conn));
        }

        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ii",  $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $data = "<thead>
    <tr>
    <th class='table-header-row-1'>Name</th>
    <th class='table-header-row-1'>User_Id</th>
    <th class='table-header-row-1'>User_Role</th>
    <th class='table-header-row-1'>Mobile</th>
    <th class='table-header-row-1'>Email</th>
    <th class='table-header-row-1'>Status</th>
    <th class='table-header-row-1'>Login Page</th>
    <th class='table-header-row-1'>Version</th>
    <th class='table-header-row-1 action-column' scope='col'>Action</th>
    </tr>
    </thead> <tbody >";
        /*while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }*/

        while ($row = mysqli_fetch_assoc($result)) {
            $text_class = "";
            if ($row['account_delete'] == 0) {
                $row['status'] = "DELETED";
                $text_class = "class='text-danger'";
            }

            $data = $data . "<tr>
        <td>{$row['name']}</td>
        <td>{$row['user_id']}</td>
        <td>{$row['role']}</td>
        <td>{$row['mobile_no']}</td>
        <td>{$row['email_id']}</td>
        <td $text_class>{$row['status']}</td>
        <td>{$row['client']}</td>
        <td>{$row['client_login']}</td>
        <td>
        <div class='btn-group dropend p-0'>
        <button class='btn p-0' type='button' data-bs-toggle='dropdown' style='border:none'>
        <i class='bi bi-three-dots-vertical'></i>
        </button>
        <ul class='dropdown-menu dropdown-menu-user-list p-0 border-0' style='width:200px'>
        <div class='list-group'>
        <button type='button' onclick='editMainTableDetails(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)' class='list-group-item list-group-item-action text-primary'>
        <i class='bi bi-pen-fill'></i><strong> Edit</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-danger' onclick='deleteRow(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)'>
        <i class='bi bi-trash-fill'></i><strong> Delete</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-success-emphasis' onclick='permissionModal(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-shield-lock-fill pe-1'></i><strong> Permissions</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action' onclick='managing_devices(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-cpu pe-1'></i><strong> Managing Devices</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-info' onclick='device_group(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-collection pe-1'></i><strong>Group/Area View</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-warning' onclick='menu_permission(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-list pe-1'></i><strong>Menu Permissions</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-primary' onclick='account_action(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-person-lines-fill pe-1'></i><strong>Account Action</strong>
        </button>
        </div>
        </ul>
        </div>
        </td>
        </tr>";
        }

        $data = $data . "</tbody>";
        $response = ['data' => $data, 'totalPages' => $totalPages];

        header('Content-Type: application/json');
        mysqli_stmt_close($stmt);
    } else if ($role == "SUPERADMIN") {
        $totalQuery = "";
        $sql = "";

        if ($search_user == "") {
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE account_delete='1' AND id!='$user_login_id' AND (created_by='$user_login_id' OR role!='SUPERADMIN')";
            $sql = "SELECT * FROM login_details WHERE account_delete='1' AND id!='$user_login_id' AND (created_by='$user_login_id' OR role!='SUPERADMIN') LIMIT ? OFFSET ?";
        } else {
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE account_delete='1' and (`user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%') AND id!='$user_login_id' AND (created_by='$user_login_id' OR role!='SUPERADMIN')";

            $sql = "SELECT * FROM login_details WHERE account_delete='1'and (`user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%') AND id!='$user_login_id' AND (created_by='$user_login_id' OR role!='SUPERADMIN') LIMIT ? OFFSET ?";
        }


        $totalResult = mysqli_query($conn, $totalQuery);

        if (!$totalResult) {
            die("Query failed: " . mysqli_error($conn));
        }

        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ii",  $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        /* $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }*/
        $data = "<thead>
    <tr>
    <th class='table-header-row-1'>Name</th>
    <th class='table-header-row-1'>User_Id</th>
    <th class='table-header-row-1'>User_Role</th>
    <th class='table-header-row-1'>Mobile</th>
    <th class='table-header-row-1'>Email</th>

    <th class='table-header-row-1'>Status</th>
    <th class='table-header-row-1 action-column' scope='col'>Action</th>
    </tr>
    </thead> <tbody >";
        while ($row = mysqli_fetch_assoc($result)) {
            $data = $data . "<tr>
          <td>{$row['name']}</td>
    <td>{$row['user_id']}</td>
    <td>{$row['role']}</td>
    <td>{$row['mobile_no']}</td>
    <td>{$row['email_id']}</td>
    <td>{$row['status']}</td>
    <td>
    <div class='btn-group dropend p-0 z-3 popup-btn-group'>
    <button class='btn p-0' type='button' data-bs-toggle='dropdown' style='border:none'>
    <i class='bi bi-three-dots-vertical'></i>
    </button>
    <ul class='dropdown-menu p-0 border-0' style='width:200px'>
    <div class='list-group'>

        <button type='button' onclick='editMainTableDetails(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)' class='list-group-item list-group-item-action text-primary'>
        <i class='bi bi-pen-fill'></i><strong> Edit</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-danger' onclick='deleteRow(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)'>
        <i class='bi bi-trash-fill'></i><strong> Delete</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-success-emphasis' onclick='permissionModal(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-shield-lock-fill pe-1'></i><strong> Permissions</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action' onclick='managing_devices(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-cpu pe-1'></i><strong> Managing Devices</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-info' onclick='device_group(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-person-lines-fill'></i><strong>Group/Area View</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-warning' onclick='menu_permission(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-list pe-1'></i><strong>Menu Permissions</strong>
        </button>
        </div>
        </ul>
        </div>
        </td>
        </tr>";
        }
        $data = $data . "</tbody>";
        $response = ['data' => $data, 'totalPages' => $totalPages];

        header('Content-Type: application/json');
        mysqli_stmt_close($stmt);
    } else {
        $totalQuery = "";
        $sql = "";

        if ($search_user == "") {
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE account_delete='1' AND created_by='$user_login_id'";
            $sql = "SELECT * FROM login_details WHERE account_delete='1' AND created_by='$user_login_id' LIMIT ? OFFSET ?";
        } else {
            $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE account_delete='1' AND created_by='$user_login_id' AND `user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%'";
            $sql = "SELECT * FROM login_details WHERE account_delete='1' AND created_by='$user_login_id' AND `user_id` LIKE '%$search_user%' or `mobile_no` LIKE '%$search_user%' or `email_id` LIKE '%$search_user%' or `name` LIKE '%$search_user%' LIMIT ? OFFSET ?";
        }

        // $totalQuery = "SELECT COUNT(*) AS total FROM login_details WHERE account_delete='1' AND created_by='$user_login_id'";
        $totalResult = mysqli_query($conn, $totalQuery);

        if (!$totalResult) {
            die("Query failed: " . mysqli_error($conn));
        }

        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);


        // $sql = "SELECT * FROM login_details WHERE account_delete='1' AND created_by='$user_login_id' LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ii",  $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        /* $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }*/
        $data = "<thead>
    <tr>
    <th class='table-header-row-1'>Name</th>
    <th class='table-header-row-1'>User_Id</th>
    <th class='table-header-row-1'>User_Role</th>
    <th class='table-header-row-1'>Mobile</th>
    <th class='table-header-row-1'>Email</th>

    <th class='table-header-row-1'>Status</th>
    <th class='table-header-row-1 action-column' scope='col'>Action</th>
    </tr>
    </thead> <tbody >";
        while ($row = mysqli_fetch_assoc($result)) {
            $data = $data . "
        <tr>
           <td>{$row['name']}</td>
    <td>{$row['user_id']}</td>
    <td>{$row['role']}</td>
    <td>{$row['mobile_no']}</td>
    <td>{$row['email_id']}</td>
    <td>{$row['status']}</td>
    <td>
    <div class='btn-group dropend p-0'>
    <button class='btn p-0' type='button' data-bs-toggle='dropdown' style='border:none'>
    <i class='bi bi-three-dots-vertical'></i>
    </button>
    <ul class='dropdown-menu dropdown-menu-user-list p-0 border-0' style='width:200px'>
    <div class='list-group'>
        <button type='button' onclick='editMainTableDetails(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)' class='list-group-item list-group-item-action text-primary'>
        <i class='bi bi-pen-fill'></i><strong> Edit</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-danger' onclick='deleteRow(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\", this)'>
        <i class='bi bi-trash-fill'></i><strong> Delete</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-success-emphasis' onclick='permissionModal(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-shield-lock-fill pe-1'></i><strong> Permissions</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action' onclick='managing_devices(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-cpu pe-1'></i><strong> Managing Devices</strong>
        </button>
        <button type='button' class='list-group-item list-group-item-action text-info' onclick='device_group(\"{$row['id']}\", \"{$row['mobile_no']}\", \"{$row['name']}\")'>
        <i class='bi bi-person-lines-fill'></i><strong>Group/Area View</strong>
        </button>
        </div>
        </ul>
        </div>
        </td>
        </tr>";
        }
        $data = $data . "</tbody>";

        $response = ['data' => $data, 'totalPages' => $totalPages];

        header('Content-Type: application/json');
        mysqli_stmt_close($stmt);
    }
    echo json_encode($response);
    mysqli_close($conn);
}


function sanitize_input($data, $conn)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

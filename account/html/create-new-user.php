
<div class="modal fade" id="adduser" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 highlight-first-letter" id="addUserModalLabel">Add New User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserform">
                    <div class="mb-3">
                        <label class="form-label" for="edituserName">Name</label>
                        <input type="text" class="form-control" id="newprofileName" placeholder="Enter Name...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edituserid">User_ID</label>
                        <input type="text" class="form-control" id="newuserid" placeholder="Enter User_ID...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editUserRole">User_Role</label>
                        <select class="form-select" id="newUserRole">
                            <?php
                            if($role=="SUPERADMIN")
                            {
                                ?>
                                <option value="SUPERADMIN">Super-Admin ISTL User Only</option>
                                <?php
                            }
                            ?>
                            <option value="ADMIN">Admin</option>
                            <option value="DISTRICT">District Level</option>
                            <option value="CITY">City Level</option>
                            <option value="ZONE">Zone Level</option>
                            <option value="AREA">Area/Group Level</option>
                            <option value="TECHNICIAN">Technician</option>


                        </select>
                    </div>

                    <?php
                    if($role=="SUPERADMIN")
                    {
                        ?>
                        <div class="mb-3">
                            <label class="form-label" for="client_dashboard">Client Login Page</label>
                            <select class="form-select" id="client_dashboard">
                                <?php
                                try {
                                    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

                                    if (!$conn) {
                                        $error = "Connection failed: " . mysqli_connect_error();
                                    } else {
                                        $sql = "SELECT `client_dashboard`,`client_identity_name` FROM `client_dashboard`";
                                        $result = mysqli_query($conn, $sql);

                                        if ($result) {
                                            if (mysqli_num_rows($result) > 0) {
                                                while ($r = mysqli_fetch_assoc($result)) {
                                                    echo "<option value='" . htmlspecialchars($r['client_dashboard'], ENT_QUOTES) . "'>" . htmlspecialchars($r['client_identity_name'], ENT_QUOTES) . "</option>";
                                                }
                                            }
                                            mysqli_free_result($result);
                                        } else {

                                        }

                                        mysqli_close($conn);
                                    }


                                } catch (Exception $e) {

                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="mb-3">
                        <label class="form-label" for="edituserMobile">Mobile</label>
                        <input type="text" class="form-control" id="newuserMobile" placeholder="Enter Mobile..." maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edituserEmail">Email</label>
                        <input type="text" class="form-control" id="newuserEmail" placeholder="Enter Email...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" value="" id="password" placeholder="Enter Password...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirmpassword">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmpassword" value="" placeholder="Enter Confirm Password...">
                    </div>
                    <div id="passwordStrength" class="font-small text-danger-emphasis mt-2"style="display: none;">Password must include: at least 8 characters, an uppercase letter, a lowercase letter, a number, a special character</div>
                    <div id="passwordMismatch" class="font-small text-danger mt-2" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="adding_new_user()">Add New User</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Alret Message -->
<div class="custom-alert " id="customAlert">
    <div class="custom-alert-content">
        <p id="customAlertMessage" class="alert alert-warning"></p>
        <button class="btn btn-primary alertbutton" onclick="hideCustomAlert()">OK</button>
    </div>
</div>
<div class="modal fade" id="view_managing_device_list" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">#<span id="userid_devices"></span>-User Managing Devices</h1>              
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="view-user-devices-list-tab" data-bs-toggle="tab" data-bs-target="#user-devices-list" type="button" role="tab" aria-controls="user-devices-list" aria-selected="true" onclick="addUserDevice()">Managing Devices </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admin-devices-list-tab" data-bs-toggle="tab" data-bs-target="#admin-devices-list" type="button" role="tab" aria-controls="admin-devices-list" aria-selected="false" onclick="addnewDevice()">Add Devices</button>
                    </li>

                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="user-devices-list" role="tabpanel" aria-labelledby="view-user-devices-list-tab" tabindex="0">
                        <div class="container mt-2 p-0 ">
                            <div class="row justify-content-end align-items-end mt-2 ">
                                <div class="col-auto  mb-2 pe-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search..." id="devicehandlesearch">
                                        <button class="btn btn-primary" type="button" onclick="devicehandleSearch()">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <?php
                                if($role=="SUPERADMIN")
                                {
                                    ?>

                                    <div class="col-auto  mb-2 ps-1">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-primary " onclick="syncToMyAccount()">Devices Sync</button>
                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>

                                <div class="col-auto  mb-2 ps-1">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-danger " onclick="DeleteSelectedDevices()">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive-1 rounded mt-2 border">
                            <table class="table table-striped styled-table table-type-1  text-center devicehandlesearch">
                                <thead>
                                    <tr> 
                                        <th class="table-header-row-1 select-option" >
                                            <div class="form-check m-0">
                                                <input class="form-check-input pointer" type="checkbox" value="" id="selectAll">
                                                <label class="form-check-label " for="selectAll">Select</label>
                                            </div>
                                        </th>
                                        <th class="table-header-row-1 " >Device ID</th>
                                        <th class="table-header-row-1">Device Name</th>
                                        <th class="table-header-row-1">Group/Area</th>
                                    </tr>
                                </thead>
                                <tbody id="managing_devices_table">

                                </tbody>
                            </table>
                        </div>
                        <p>Selected Devices: <span id="selected_count" class="fw-semibold">0</span></p>
                        <div class="pagination-wrapper mt-2">
                            <div class="row">
                                <div class="col">
                                    <div class="row g-2 align-items-center d-flex">
                                        <div class="col-auto">
                                            <label for="items-per-page-user-devices" class="form-label">Items per page:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select id="items-per-page-user-devices" class="form-select">
                                                <option value="10">10</option>
                                                <option value="20" selected>20</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="pagination-container ">
                                        <nav>
                                            <ul class="pagination justify-content-end" id="pagination-user-devices">
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="admin-devices-list" role="tabpanel" aria-labelledby="admin-devices-list-tab" tabindex="0"> 
                        <div class="container mt-2 p-0 ">
                            <div class="row justify-content-end align-items-end mt-2 ">
                                <div class="col-auto  mb-2 pe-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search..." id="adminhandlesearch">
                                        <button class="btn btn-primary" type="button" onclick="adminhandlesearch()"><i class="bi bi-search"></i> Search</button>
                                    </div>
                                </div>
                                <div class="col-auto  mb-2 ps-1">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-primary " onclick="addSelectedDevices()">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive-1 rounded mt-2 border">
                            <table class="table table-striped styled-table table-type-1  text-center devicehandlesearch">
                                <thead>
                                    <tr> 
                                        <th class="table-header-row-1 select-option" >
                                            <div class="form-check m-0">
                                                <input class="form-check-input pointer" type="checkbox" value="" id="selectAllAdd">
                                                <label class="form-check-label " for="selectAllAdd">Select</label>
                                            </div>
                                        </th>
                                        <th class="table-header-row-1">Device ID</th>
                                        <th class="table-header-row-1">Device Name</th>
                                        <th class="table-header-row-1">Group/Area</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-managing_devices_table">

                                </tbody>
                            </table>
                        </div>
                        <p>Selected Devices: <span id="selected_count_add_device" class="fw-semibold">0</span></p>
                        <div class="pagination-wrapper mt-2">
                            <div class="row">
                                <div class="col">
                                    <div class="row g-2 align-items-center d-flex">
                                        <div class="col-auto">
                                            <label for="items-per-page-admin-devices" class="form-label">Items per page:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select id="items-per-page-admin-devices" class="form-select">
                                                <option value="10">10</option>
                                                <option value="20" selected>20</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="pagination-container ">
                                        <nav>
                                            <ul class="pagination justify-content-end" id="pagination-admin-devices">
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer ">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


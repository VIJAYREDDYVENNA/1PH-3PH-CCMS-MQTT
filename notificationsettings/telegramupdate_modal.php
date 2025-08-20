<div class="modal fade" id="telegram_modal" tabindex="-1" aria-labelledby="telegram_modallabel" aria-hidden="true">
    <div class="modal-dialog modal-">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="telegram_modallabel">Update Telegram Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ms-3">
                <div class="card h-100 shadow">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-chatId-tab" data-bs-toggle="tab" data-bs-target="#newChatId" type="button" role="tab" aria-controls="nav-chatId" aria-selected="true">New Chat ID</button>
                            <button class="nav-link" id="nav-adddevices-tab" data-bs-toggle="tab" data-bs-target="#addDevice" type="button" role="tab" aria-controls="nav-adddevices" aria-selected="false">Add Devices</button>
                            <button class="nav-link" id="nav-removedevices-tab" data-bs-toggle="tab" data-bs-target="#removeDevice" type="button" role="tab" aria-controls="nav-removedevices" aria-selected="false">Remove Devices</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="newChatId" role="tabpanel" aria-labelledby="nav-chatId-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body">
                                    <form class="col-10" id="Address">
                                        <div class="mb-2 mt-1 ms-3">
                                            <label for="Chat Id" class="form-label">Chat Id</label>
                                            <input type="text" class="form-control" id="Chat Id" placeholder="Enter Chat Id">
                                            <label for="Group name" class="form-label">Group name</label>
                                            <input type="text" class="form-control" id="Group name" placeholder="Enter Group Name">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="addDevice" role="tabpanel" aria-labelledby="nav-adddevices-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body row">
                                    <form class="col-md-12" id="frame_update_time">
                                        <div class="mb-2 ms-2">
                                            <label for="frame_update_time" class="form-label">Telegram Group</label>
                                            <select class="form-select" id="time">
                                                <option value="Select Telegram Group">Select Telegram Group</option>
                                                <option value="CCMS Bhopal">CCMS Bhopal</option>
                                            </select>
                                        </div>
                                    </form>
                                    <div>
                                        <input class="form-check-input" type="checkbox" value="" id="selectAllAddDevice" onclick="selectAllDevices('add')">
                                        <label class="form-check-label" for="selectAllAddDevice">
                                            Select ALL
                                        </label>
                                    </div>
                                    <div class="card">
                                        <div class="table-responsive rounded m-2 border" style="max-height: 200px; overflow-y: auto;">
                                            <table class="table table-type-1 text-center" id="totalDeviceTableAdd">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-logo-color text-white" style="width: 20%;" scope="col">Select</th>
                                                        <th class="bg-logo-color text-white" style="width: 80%;" scope="col">Device ID</th>
                                                        <th class="bg-logo-color text-white" scope="col">Device Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 1"></td>
                                                        <td>Device 1</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 2"></td>
                                                        <td>Device 2</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 3"></td>
                                                        <td>Device 3</td>
                                                        <td>CCMS 2</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 1"></td>
                                                        <td>Device 1</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 2"></td>
                                                        <td>Device 2</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 3"></td>
                                                        <td>Device 3</td>
                                                        <td>CCMS 2</td>
                                                    </tr>
                                                    <!-- Add more rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center">Selected Devices: <span id="selectedCountAddDevice">0</span></div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div>
                                        <button type="button" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="removeDevice" role="tabpanel" aria-labelledby="nav-removedevices-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body row">
                                    <form class="col-md-12" id="frame_update_time">
                                        <div class="mb-2 ms-2">
                                            <label for="frame_update_time" class="form-label">Telegram Group</label>
                                            <select class="form-select" id="time">
                                                <option value="Select Telegram Group">Select Telegram Group</option>
                                                <option value="CCMS Bhopal">CCMS Bhopal</option>
                                            </select>
                                        </div>
                                    </form>
                                    <div>
                                        <input class="form-check-input" type="checkbox" value="" id="selectAllRemoveDevice" onclick="selectAllDevices('remove')">
                                        <label class="form-check-label" for="selectAllRemoveDevice">
                                            Select ALL
                                        </label>
                                    </div>
                                    <div class="card">
                                        <div class="table-responsive rounded m-2 border" style="max-height: 200px; overflow-y: auto;">
                                            <table class="table table-type-1 text-center" id="totalDeviceTableRemove">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-logo-color text-white" style="width: 20%;" scope="col">Select</th>
                                                        <th class="bg-logo-color text-white" style="width: 80%;" scope="col">Device ID</th>
                                                        <th class="bg-logo-color text-white" scope="col">Device Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 1"></td>
                                                        <td>Device 1</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 2"></td>
                                                        <td>Device 2</td>
                                                        <td>CCMS 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 3"></td>
                                                        <td>Device 3</td>
                                                        <td>CCMS 2</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="selectedDevice" value="Device 3"></td>
                                                        <td>Device 3</td>
                                                        <td>CCMS 2</td>
                                                    </tr>
                                                    <!-- Add more rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center">Selected Devices: <span id="selectedCountRemoveDevice">0</span></div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <div>
                                            <button type="button" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
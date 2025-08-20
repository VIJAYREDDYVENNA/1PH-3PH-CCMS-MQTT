<div class="modal fade" id="down-time-view" tabindex="-1" aria-labelledby="downTimeModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="downTimeModal">IoT Down Time</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container mt-3 p-0">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-auto d-flex align-items-center flex-wrap">
                                <div class="form-group d-flex align-items-center mb-2  ps-3 ">
                                    <label for="actionDate" class="mr-2 mb-0">Set Date:</label>
                                    <input type="date" class="form-control" id="actionDate" style="width: auto;">
                                </div>
                                <div class="mb-2 ps-3 custom-size">
                                    <button type="button" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                    <h5>Device ID: <span id="deviceIdDisplay"></span></h5>
                    </div>
                    <div class="table-responsive rounded mt-2 border">
                    <table class="table table-striped styled-table table-type-1 text-center devicehandlesearch">
                        <thead>
                            <tr>
                                <th class="bg-logo-color text-white">Device ID</th>
                                <th class="bg-logo-color text-white">Date&Time</th>
                                <th class="bg-logo-color text-white">Downtime(Minutes)</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data rows will be inserted here -->
                            <!-- Placeholder row for no data -->
                             
                            
                            <tr id="noDataRow" style="display: none;">
                                <td colspan="3" class="text-center text-danger">No Data Available</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

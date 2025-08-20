<div class="modal fade" id="openNonActiveDevicesModal" tabindex="-1" aria-labelledby="openNonActiveDevicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="openNonActiveDevicesModalLabel">InActive Devices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-12 p-0" id="openNonActiveDevicesModal">
                    <div class="col-12 rounded mt-3 p-0">
                        <div class="row">
                            <div class="col-12 col-md-4 pointer">
                                <div class="card text-center shadow" onclick="openPoorNetwork()"id="poor_nw_device_list">
                                    <div class="card-body m-0 p-0">
                                        <p class="card-text fw-semibold m-0 py-1 text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill h4"></i> Poor N/W</p>
                                        <!-- <hr class="mt-0"> -->
                                        <h3 class="card-title py-2 text-warning-emphasis text-opacity-25" id="poornetwork">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                                <div class="card text-center shadow" onclick="openPowerFail()"id="power_failure_device_list">
                                    <div class="card-body m-0 p-0">
                                        <p class="card-text fw-semibold m-0 py-1 text-secondary-emphasis"><i class="bi bi-power h4"></i> Input Power Fail</p>
                                        <!-- <hr class="mt-0"> -->
                                        <h3 class="card-title py-2 text-secondary-emphasis" id="input_power_fail">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                                <div class="card text-center shadow" onclick="openFaulty()" id="faulty_device_list">
                                    <div class="card-body m-0 p-0">
                                        <p class="card-text fw-semibold m-0 py-1 text-danger-emphasis"><i class="bi bi-bug-fill h4"></i> Faulty</p>
                                        <!-- <hr class="mt-0"> -->
                                        <h3 class="card-title py-2 text-danger-emphasis" id="faulty">0</h3>
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
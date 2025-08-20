<div class="modal fade" id="openview1ph" style="background: rgb(0, 0,0, 0.8 )">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightsModalLabel">Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-12 rounded p-0">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end align-items-center mb-2">
                            <p class="m-0"><span class="text-body-tertiary">Updated On : </span><span id="1ph_record_date_time"></span></p>
                        </div>
                        
                        <div class="col-xl-3 col-6">
                            <div class="card text-center shadow">
                                <div class="card-body m-0 p-0">
                                    <p class="card-text fw-semibold m-0 py-1"><i class="bi bi-lightbulb-fill h4"></i> Total Lights</p>
                                    <h3 class="card-title py-2" id="1ph_total_light">--</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="card text-center shadow">
                                <div class="card-body m-0 p-0">
                                    <p class="card-text fw-semibold m-0 py-1 "><i class="bi bi-power h4"></i>ON(%)</p>
                                    <h3 class="card-title py-2" id="1ph_on_percentage">--</h3>
                                </div>  
                            </div>
                        </div>
                        <div class="col-xl-3 col-6 mt-3 mt-xl-0" >
                            <div class="card text-center shadow">
                                <div class="card-body m-0 p-0">
                                    <p class="card-text fw-semibold m-0 py-1 "><i class="bi bi-power h4"></i>OFF(%)</p>
                                    <h3 class="card-title py-2 "id="1ph_off_percentage">--</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-6 mt-3 mt-xl-0">
                            <div class="card text-center shadow" >
                                <div class="card-body m-0 p-0">
                                    <p class="card-text fw-semibold m-0 py-1 "><i class="bi bi-lightning-fill h4"></i> On/Off Status</p>
                                    <h3 class="card-title py-2" id="1ph_on_off_status">--</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row"> 
                    <div class="col-md-6 rounded  p-2">
                        <div class="mt-4">
                            <h5>Voltage(V)</h5>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <div class="card shadow mt-2 py-2" style="background: rgba(189, 189, 189, 0.45)">
                                    <div class="card-body m-0 p-0">
                                        <div class="row">
                                            <div class="col-md-4 justify-content-center d-flex align-items-center">
                                                <div class="phase-text justify-content-center d-flex align-items-center">Ph</div>
                                            </div>
                                            <div class="col-md-8">
                                                <h3 class="card-title py-2 text-center m-0" id="1ph_v_r">--</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- <div class="col-xl-4 col-lg-4  col-md-4 col-sm-4 col-4">
                            <div class="card shadow mt-2 py-2" style="background:rgba(238, 218, 43, 0.15)">
                                <div class="card-body m-0 p-0 text-warning">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Y</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_v_y">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4  col-lg-4 col-md-4 col-sm-4 col-4" >
                            <div class="card shadow mt-2 py-2" style="background:rgba(43, 127, 238, 0.15)">
                                <div class="card-body m-0 p-0 text-primary">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">B</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0 " id="1ph_v_b">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>


                <div class="col-md-6 rounded  p-2">
                 <div class="mt-4">
                    <h5>Current(Amps)</h5>
                </div>
                <div class="row">
                    <div class="col-xl-12  col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card shadow mt-2 py-2" style="background: rgba(189, 189, 189, 0.45)">
                            <div class="card-body m-0 p-0">
                                <div class="row">
                                    <div class="col-md-4 justify-content-center d-flex align-items-center">
                                        <div class="phase-text justify-content-center d-flex align-items-center">Ph</div>
                                    </div>
                                    <div class="col-md-8 justify-content-center d-flex align-items-center">
                                        <h3 class="card-title py-2 text-center m-0" id="1ph_i_r">--</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- <div class="col-xl-4  col-lg-4 col-md-4 col-sm-4 col-4">
                            <div class="card shadow mt-2 py-2" style="background:rgba(238, 218, 43, 0.15)">
                                <div class="card-body m-0 p-0 text-warning">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Y</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_i_y">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4  col-lg-4 col-md-4 col-sm-4 col-4" >
                            <div class="card shadow mt-2 py-2" style="background:rgba(43, 127, 238, 0.15)">
                                <div class="card-body m-0 p-0 text-primary">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">B</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_i_b">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>

                
                <div class="col-md-6 rounded  p-2">
                    <div class="mt-4">
                        <h5>Power(kW)</h5>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 ">
                            <div class="card shadow mt-2 py-2" style="background: rgba(189, 189, 189, 0.45)">
                                <div class="card-body m-0 p-0">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Ph</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_watt_r">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-xl-4 col-lg-4">
                            <div class="card shadow mt-2 py-2" style="background:rgba(238, 218, 43, 0.15)">
                                <div class="card-body m-0 p-0 text-warning">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Y</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_watt_y">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4" >
                            <div class="card shadow mt-2 py-2" style="background:rgba(43, 127, 238, 0.15)">
                                <div class="card-body m-0 p-0 text-primary">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">B</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_watt_b">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div> 
                <div class="col-md-6 rounded  p-2">
                    <div class="mt-4">
                        <h5>Power(kVA)</h5>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 ">
                            <div class="card shadow mt-2 py-2" style="background: rgba(189, 189, 189, 0.45)">
                                <div class="card-body m-0 p-0">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Ph</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_kva_r">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-xl-4 col-lg-4">
                            <div class="card shadow mt-2 py-2" style="background:rgba(238, 218, 43, 0.15)">
                                <div class="card-body m-0 p-0 text-warning">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">Y</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_watt_y">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4" >
                            <div class="card shadow mt-2 py-2" style="background:rgba(43, 127, 238, 0.15)">
                                <div class="card-body m-0 p-0 text-primary">
                                    <div class="row">
                                        <div class="col-md-4 justify-content-center d-flex align-items-center">
                                            <div class="phase-text justify-content-center d-flex align-items-center">B</div>
                                        </div>
                                        <div class="col-md-8 justify-content-center d-flex align-items-center">
                                            <h3 class="card-title py-2 text-center m-0" id="1ph_watt_b">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div> 
            </div>


            <div class="mt-4">
                <h5>Total Energy(units)</h5>
            </div>
            <div class="col-12 rounded mt-3 p-0">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 mb-2">
                        <div class="card text-center shadow bg-secondary ">
                            <div class="card-body m-0 p-0 text-white">
                                <p class="card-text fw-semibold m-0 py-1 ">kWh</p>
                                <h3 class="card-title py-2" id="1ph_kwh">--</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card text-center shadow bg-secondary ">
                            <div class="card-body m-0 p-0 text-white">
                                <p class="card-text fw-semibold m-0 py-1 ">kVAh</p>
                                <h3 class="card-title py-2 "  id="1ph_kvah">--</h3>
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

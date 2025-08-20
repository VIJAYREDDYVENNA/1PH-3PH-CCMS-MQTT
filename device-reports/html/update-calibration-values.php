<div class="modal fade" id="calibration_model" tabindex="-1" aria-labelledby="calibration_modelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="calibration_modelLabel">Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row border-bottom mb-2"> 
                        <div class="col-12">
                            <h6 >Voltages(V)</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id="v_r">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id="v_y">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id="v_b">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2">    
                        <div class="col-12">
                            <h6 >Current(A)</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=i_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=i_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=i_b>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >Gain</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=gain_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=gain_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=gain_b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >Angle-1</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_1_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_1_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_1_b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >Angle-2</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_2_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_2_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=angle_2_b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >AWG</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=awg_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=awg_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=awg_b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >AVAG</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avag_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avag_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avag_b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-2"> 
                        <div class="col-12">
                            <h6 >AVARG</h6>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>R : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avarg_r>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>Y : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avarg_y>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-4 mb-2">
                            <div class="row p-0 ">
                                <div class="col-4 pr-0 justify-content-center align-self-center text-center">

                                    <label>B : </label>
                                </div>
                                <div class="col-8 p-0 m-0 ">
                                    <input type="number" class="form-control validate_input" min=0 id=avarg_b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 d-flex justify-content-end">
                    <span id="row_count" class="mx-2"></span>
                    <button type="button" class="btn btn-secondary mx-2" id=btn_prev onclick="readPreviousValues()" >Old</button>
                    <button type="button" class="btn btn-secondary mx-2" id=btn_next onclick="readNextValues()">Latest</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id=read_settings onclick="read_iot_settings()">Read IOT Settings</button>
                <button type="button" class="btn btn-primary" id=save_settings>Save Changes</button>
            </div>
        </div>
    </div>
</div>

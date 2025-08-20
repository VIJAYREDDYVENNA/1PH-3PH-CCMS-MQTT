
<div class="modal fade " id="addLightModal" tabindex="1" aria-labelledby="addLightModalLabel" aria-hidden="true" style="background: rgb(0, 0,0, 0.8 )">
    <div class="modal-dialog ">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="addLightModalLabel">Add Light</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="addLightsForm">
                    <div class="mb-3">
                        <label for="brandName" class="form-label">Brand Name</label>
                        <select class="form-select" id="brandName">
                            <option value="PHILIPS">Philips</option>
                            <option value="HAVELLS">Havells</option>
                            <option value="WIPRO">Wipro</option>
                            <option value="BAJAJ">Bajaj</option>
                            <option value="SYSKA">Syska</option>
                            <option value="CHARLSTON">Charlston</option>
                            <option value="OREVA">Oreva</option>
                            <option value="MOSER BAER">Moser Baer</option>
                            <option value="CROMPTON">Crompton</option>
                            <option value="SURYA">Surya</option>
                            <option value="OSRAM">Osram</option>
                            <option value="EVEREADY">Eveready</option>
                            <option value="GE LIGHTING">GE Lighting</option>
                            <option value="NTL LEMNIS">NTL Lemnis</option>
                            <option value="REIZ ELECTROCONTROLS">Reiz Electrocontrols</option>
                            <option value="MIC ELECTRONICS">MIC Electronics</option>
                            <option value="INNOVLITE INDIA">Innovlite India</option>
                            <option value="SANARTI GROUP">Sanarti Group</option>
                            <option value="GOLDWYN LTD">Goldwyn Ltd</option>
                            <option value="LAAJ LIGHTING">Laaj Lighting</option>
                            <option value="ECO LITE TECHNOLOGIES">Eco Lite Technologies</option>
                            <option value="3S INTERNATIONAL">3S International</option>
                            <option value="AMPTEK LED LIGHTING">Amptek Led Lighting</option>
                            <option value="PROMPTEC">Promptec</option>
                            <option value="BINAY OPTO ELECTRONICS">Binay Opto Electronics</option>
                            <option value="INSTAPOWER">INSTAPOWER</option>
                            <option value="SUJANA">Sujana</option>
                            <option value="D-LITE">D-Lite</option>
                            <option value="HALONIX">Halonix</option>
                            <option value="EON">Eon</option>
                            <option value="KAWACHI">Kawachi</option>
                            <option value="COMPACT">Compact</option>
                            <option value="LOW-CARBON LIGHTING">Low-Carbon Lighting</option>
                            <option value="MEGAWAY LED">Megaway Led</option>
                            <option value="RASHMI">Rashmi</option>
                            <option value="ACS">ACS</option>
                            <option value="GCM GOLD">Gcm Gold</option>
                            <option value="SUPERDEALS">Superdeals</option>
                            <option value="PHAROX">Pharox</option>
                            <option value="GOLD LED">Gold LED</option>
                            <option value="CARBON REDUCTION TECHNOLOGY">Carbon Reduction Technology</option>
                            <option value="OTHER">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="wattage" class="form-label">Wattage</label>
                        <input type="number" class="form-control" id="wattage" required>
                    </div>
                    <div class="mb-3">
                        <label for="lights" class="form-label">Number of Lights</label>
                        <input type="number" class="form-control" id="lights" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveLightButton" onclick="addLight()">Save Light</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
</main>
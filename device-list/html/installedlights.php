<!-- Add Light Modal -->
<div class="modal fade" id="lightsModal" tabindex="-1" aria-labelledby="lightsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightsModalLabel">Installed Lights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table for displaying installed lights -->
                <div class="table-responsive-1 rounded mt-2 border">
                    <table class="table table-striped styled-table w-100 text-center">
                        <thead>
                            <tr>
                                <th class="bg-primary-subtle">Brand Name</th>
                                <th class="bg-primary-subtle">Watts</th>
                                <th class="bg-primary-subtle">Total Lights</th>
                                <th class="bg-primary-subtle">Total Wattage</th>
                                <th class="bg-primary-subtle">Action</th>
                            </tr>
                        </thead>
                        <tbody id="lightsTableBody">
                            <!-- Lights data will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addLightButton" onclick="showAddLightsForm()">Add Light</button>
            </div>
        </div>
    </div>
</div>

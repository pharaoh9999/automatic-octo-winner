<div class="modal fade text-bg-dark" id="vehicleSearchModal" tabindex="-1" aria-labelledby="vehicleSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success" id="vehicleSearchModalLabel">Search Vehicles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="vehicleSearchForm">
                    <!-- Number Plate Field -->
                    <div class="mb-3">
                        <label for="numberPlate" class="form-label">Number Plate</label>
                        <input type="text" class="form-control" id="numberPlate" name="numberPlate" placeholder="Enter Number Plate">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Output Section -->
                <div id="vehicleSearchOutput" class="mt-4" style="display: none;">
                    <h5>Search Results</h5>
                    <div class="card">
                        <div class="card-body" id="vehicleDetails"></div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include './includes/scripts.php' ?>
<script>
    document.getElementById('vehicleSearchForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submission
        const formData = new FormData(this);
        showLoader(); // Show loader
        // Clear previous results
        document.getElementById('vehicleSearchOutput').style.display = 'none';
        document.getElementById('vehicleDetails').innerHTML = '';

        // Perform API call
        fetch('vehicle_search_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const vehicle = data.data[0]; // Assuming single asset returned

                    // Populate vehicle details
                    const detailsHtml = `
                    <div id="vehicleSearchOutput" class="mt-4">
    <h5 class="text-primary">Search Results</h5>
    <ul class="list-group">
        <li class="list-group-item"><strong>ID Number:</strong> ${vehicle.ID_Number}</li>
        <li class="list-group-item"><strong>Owner Name:</strong> ${vehicle.Owner_Name}</li>
        <li class="list-group-item"><strong>Passport Number:</strong> ${vehicle.passport_no || 'N/A'}</li>
        <li class="list-group-item"><strong>PIN:</strong> ${vehicle.Pin}</li>
        <li class="list-group-item"><strong>Mobile Number:</strong> ${vehicle.mobile_number}</li>
        <li class="list-group-item"><strong>Vehicle Number:</strong> ${vehicle.vehicle_no}</li>
        <li class="list-group-item"><strong>Vehicle Model:</strong> ${vehicle.vehicle_model}</li>
        <li class="list-group-item"><strong>Use:</strong> ${vehicle.Use}</li>
        <li class="list-group-item"><strong>NTSA ID:</strong> ${vehicle.ntsa_id}</li>
        <li class="list-group-item"><strong>Capacity:</strong> ${vehicle.capacity}</li>
    </ul>

    <h5 class="mt-4 text-primary">Mechanical Data</h5>
    <ul class="list-group">
        <li class="list-group-item"><strong>Chassis Number:</strong> ${vehicle.mechanical_data.ChassisNo}</li>
        <li class="list-group-item"><strong>Year of Manufacture:</strong> ${vehicle.mechanical_data.yearOfManufacture}</li>
        <li class="list-group-item"><strong>Car Make:</strong> ${vehicle.mechanical_data.carMake}</li>
        <li class="list-group-item"><strong>Car Model:</strong> ${vehicle.mechanical_data.carModel}</li>
        <li class="list-group-item"><strong>Registration Number:</strong> ${vehicle.mechanical_data.regNo}</li>
        <li class="list-group-item"><strong>Body Type:</strong> ${vehicle.mechanical_data.bodyType}</li>
        <li class="list-group-item"><strong>Logbook Number:</strong> ${vehicle.mechanical_data.logbookNumber}</li>
        <li class="list-group-item"><strong>Registration Date:</strong> ${vehicle.mechanical_data.registrationDate}</li>
        <li class="list-group-item"><strong>Engine Capacity:</strong> ${vehicle.mechanical_data.engineCapacity}</li>
        <li class="list-group-item"><strong>Passenger Capacity:</strong> ${vehicle.mechanical_data.passengerCapacity}</li>
        <li class="list-group-item"><strong>Body Color:</strong> ${vehicle.mechanical_data.bodyColor}</li>
        <li class="list-group-item"><strong>Engine Number:</strong> ${vehicle.mechanical_data.engineNumber}</li>
    </ul>
</div>

                `;

                    document.getElementById('vehicleDetails').innerHTML = detailsHtml;
                    document.getElementById('vehicleSearchOutput').style.display = 'block';
                } else {
                    alert(data.message || 'No results found.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                //console.log(response);
                alert('An error occurred while performing the search.');
            }).finally(() => {
                hideLoader(); // Always hide the loader
            });
    });
</script>
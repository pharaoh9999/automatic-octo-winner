<div class="modal fade" id="citizenSearchModal" tabindex="-1" aria-labelledby="citizenSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success" id="citizenSearchModalLabel">Search Citizens</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="citizenSearchForm">
                    <!-- Search Type Selection -->
                    <div class="mb-3">
                        <label for="searchType1" class="form-label">Search By</label>
                        <select class="form-select" id="searchType1" name="searchType1" required>
                            <option value="kraPin" selected>KRA PIN</option>
                            <option value="idNumber">ID Number</option>
                        </select>
                    </div>

                    <!-- Dynamic Input Field -->
                    <div class="mb-3">
                        <label for="dynamicField1" class="form-label" id="dynamicField1Label">Enter KRA PIN</label>
                        <input type="text" class="form-control" id="dynamicField1" name="kraPin" placeholder="Enter KRA PIN" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Output Section -->
                <div id="citizenSearchOutput" class="mt-4" style="display: none;">
                    <h5 class="text-primary">Search Results</h5>
                    <ul class="list-group" id="citizenDetails"></ul>
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
    // Handle dynamic field updates
    document.getElementById('searchType1').addEventListener('change', function() {
        const field = document.getElementById('dynamicField1');
        const label = document.getElementById('dynamicField1Label');

        if (this.value === 'kraPin') {
            label.textContent = 'Enter KRA PIN';
            field.placeholder = 'Enter KRA PIN';
            field.name = 'kraPin'; // Update field name
        } else if (this.value === 'idNumber') {
            label.textContent = 'Enter ID Number';
            field.placeholder = 'Enter ID Number';
            field.name = 'idNumber'; // Update field name
        }
    });

    // Handle form submission
    document.getElementById('citizenSearchForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
        showLoader(); // Show loader
        // Clear previous results
        document.getElementById('citizenSearchOutput').style.display = 'none';
        document.getElementById('citizenDetails').innerHTML = '';

        // Perform API call
        fetch('citizen_search_handler.php', {
                method: 'POST',
                body: formData,
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.status) {
                    const citizen = data.data.RESPONSE?.PINDATA || data.data;
                    const encodedString = data.encoded;

                    // Populate citizen details dynamically
                    let detailsHtml = `
                        <li class="list-group-item"><strong>Full Name:</strong> ${citizen.FirstName || citizen.first_name || 'N/A'} ${citizen.MiddleName || citizen.middle_name || ''} ${citizen.LastName || citizen.sur_name || ''}</li>
                        <li class="list-group-item"><strong>KRA PIN:</strong> ${citizen.KRAPIN || citizen.pin_no || 'N/A'}</li>
                        <li class="list-group-item"><strong>ID Number:</strong> ${citizen.IdentificationNumber || citizen.nid_no || 'N/A'}</li>
                        <li class="list-group-item"><strong>Date of Birth:</strong> ${citizen.DateOfBirth || citizen.actual_birth_date || 'N/A'}</li>
                        <li class="list-group-item"><strong>Gender:</strong> ${citizen.Gender || citizen.gender || 'N/A'}</li>
                        <li class="list-group-item"><strong>Status:</strong> ${citizen.StatusOfPIN || citizen.active_flag1 || 'N/A'}</li>
                        <li class="list-group-item"><strong>Email:</strong> ${citizen.EmailAddress || citizen.email || 'N/A'}</li>
                        <li class="list-group-item"><strong>Mobile Number:</strong> ${citizen.MobileNumber || citizen.mobile_number || 'N/A'}</li>
                        <li class="list-group-item"><a href="./process_qr.php?kestrelToken=${encodedString || 'N/A'}" class="btn btn-warning">Kestrel Advance Query</a></li>
                    `;

                    // Add Physical Address
                    if (citizen.PrincipalPhysicalAddress || citizen) {
                        const address = citizen.PrincipalPhysicalAddress || citizen;
                        detailsHtml += `
                            <li class="list-group-item"><strong>Physical Address:</strong> ${address.Building || address.building || 'N/A'}, ${address.StreetRoad || address.street_road || 'N/A'}, ${address.CityTown || address.city_town || 'N/A'}</li>
                        `;
                    }

                    // Add Postal Address
                    if (citizen.PrincipalPostalAddress || citizen) {
                        const postal = citizen.PrincipalPostalAddress || citizen;
                        detailsHtml += `
                            <li class="list-group-item"><strong>Postal Address:</strong> P.O. Box ${postal.POBox || postal.po_box || 'N/A'} - ${postal.PostalCode || postal.postal_code || 'N/A'}</li>
                        `;
                    }

                    document.getElementById('citizenDetails').innerHTML = detailsHtml;
                    document.getElementById('citizenSearchOutput').style.display = 'block'; // Show results
                } else {
                    alert(data.message || 'No results found.');
                }
            })
            .catch((error) => {
                console.error('Fetch Error:', error.message);
                alert(`An error occurred: ${error.message}`);
            }).finally(() => {
                hideLoader(); // Always hide the loader
            });
    });
</script>
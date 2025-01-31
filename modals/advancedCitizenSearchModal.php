<div class="modal fade" id="advancedCitizenSearchModal" tabindex="-1" aria-labelledby="advancedCitizenSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success" id="advancedCitizenSearchModalLabel">Search Citizens by Parameters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="advancedCitizenSearchForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="firstnameId" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstnameId" name="firstnameId" placeholder="Enter First Name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middlenameId" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middlenameId" name="middlenameId" placeholder="Enter Middle Name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="lastnameId" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastnameId" name="lastnameId" placeholder="Enter Last Name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dateOfBirthLow" class="form-label">Date of Birth (Start)</label>
                            <input type="date" class="form-control" id="dateOfBirthLow" name="dateOfBirthLow">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dateOfBirthHigh" class="form-label">Date of Birth (End)</label>
                            <input type="date" class="form-control" id="dateOfBirthHigh" name="dateOfBirthHigh">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select class="form-select" id="sex" name="sex">
                                <option value="">Select Sex</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="orderBy" class="form-label">Order By</label>
                            <select class="form-select" id="orderBy" name="orderBy">
                                <option value="random">Random</option>
                                <option value="firstname">First Name</option>
                                <option value="middlename">Middle Name</option>
                                <option value="lastname">Last Name</option>
                                <option value="dob">Date of Birth</option>
                                <option value="idnumber">ID Number</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="orderType" class="form-label">Order Type</label>
                            <select class="form-select" id="orderType" name="orderType">
                                <option value="asc">Ascending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="limit" class="form-label">Limit</label>
                            <input type="number" class="form-control" id="limit" name="limit" placeholder="Enter Limit">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Results Section -->
                <div class="mt-4 overflow-auto" id="citizenSearchResults" style="display: none;">
                    <h5 class="text-primary">Search Results</h5>
                    <table class="table table-striped table-hover table-dark">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>National ID</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Date of Birth</th>
                                <th>Sex</th>
                                <th>Physical Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="citizenResultsTableBody"></tbody>
                    </table>
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
    document.getElementById('advancedCitizenSearchForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
        showLoader(); // Show loader
        // Clear previous results
        document.getElementById('citizenSearchResults').style.display = 'none';
        document.getElementById('citizenResultsTableBody').innerHTML = '';

        // Perform API call
        fetch('citizen_param_search_handler.php', {
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
                    const citizens = data.data;
                    const tableBody = document.getElementById('citizenResultsTableBody');

                    citizens.forEach((citizen, index) => {
                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${citizen.identity_id || 'N/A'}</td>
                                <td>${citizen.first_name || 'N/A'}</td>
                                <td>${citizen.middle_name || 'N/A'}</td>
                                <td>${citizen.last_name || 'N/A'}</td>
                                <td>${citizen.date_birth || 'N/A'}</td>
                                <td>${citizen.sex || 'N/A'}</td>
                                <td>${citizen.physical_address || 'N/A'}</td>
                                <td>
                                    <a href="./process_qr.php?kestrelToken=${citizen.encodedData || 'N/A'}" class="btn btn-primary btn-sm">PKest</a>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    document.getElementById('citizenSearchResults').style.display = 'block';
                } else {
                    alert(data.message || 'No results found.');
                }
            })
            .catch((error) => {
                // Fetch raw text for debugging
                fetch('citizen_param_search_handler.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then((response) => response.text()) // Get the raw response text
                    .then((text) => {
                        console.error('Full Response:', text); // Log full HTML response
                        alert('An error occurred. Check the console for details.');
                    });
            }).finally(() => {
                hideLoader(); // Always hide the loader
            });
    });
</script>
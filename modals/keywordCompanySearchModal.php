<div class="modal fade" id="keywordCompanySearchModal" aria-labelledby="keywordCompanySearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success" id="keywordCompanySearchModalLabel">Search Companies by Keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="keywordSearchForm">
                    <div class="mb-3">
                        <label for="businessName" class="form-label">Enter Keyword</label>
                        <input type="text" class="form-control" id="businessName" name="businessName" placeholder="Enter business name keyword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Results Section -->
                <div class="mt-4 overflow-auto" id="keywordSearchResults" style="display: none;">
                    <h5 class="text-primary">Search Results</h5>
                    <table class="table table-striped table-hover table-dark">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Business Name</th>
                                <th>PIN Number</th>
                                <th>Registration Number</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTableBody"></tbody>
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
    document.getElementById('keywordSearchForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
        showLoader(); // Show loader
        // Clear previous results
        document.getElementById('keywordSearchResults').style.display = 'none';
        document.getElementById('resultsTableBody').innerHTML = '';

        // Perform API call
        fetch('keyword_search_handler.php', {
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
                    const companies = data.data;
                    const tableBody = document.getElementById('resultsTableBody');

                    companies.forEach((company, index) => {
                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${company.business_name || 'N/A'}</td>
                                <td>${company.pin_no || 'N/A'}</td>
                                <td>${company.buss_cert_reg_num || 'N/A'}</td>
                                <td>${company.mobile_number || 'N/A'}</td>
                                <td>${company.email_id || 'N/A'}</td>
                                <td>${company.city_name || 'N/A'}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#companySearchModal">Search</button>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    document.getElementById('keywordSearchResults').style.display = 'block';
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
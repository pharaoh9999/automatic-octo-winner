<div class="modal fade" id="companySearchModal" aria-labelledby="companySearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success" id="companySearchModalLabel">Search Companies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="companySearchForm">
                    <!-- Field Selection -->
                    <div class="mb-3">
                        <label for="searchType" class="form-label">Search By</label>
                        <select class="form-select" id="searchType" name="searchType" required>
                            <option value="kraPin" selected>KRA PIN</option>
                            <option value="brsNumber">BRS Number</option>
                        </select>
                    </div>

                    <!-- Dynamic Input Field -->
                    <div class="mb-3">
                        <label for="dynamicField" class="form-label" id="dynamicFieldLabel">Enter KRA PIN</label>
                        <input type="text" class="form-control" id="dynamicField" name="dynamicField" placeholder="Enter KRA PIN" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Output Section -->
                <div id="companySearchOutput" class="mt-4" style="display: none;">
                    <h5 class="text-primary">Search Results</h5>
                    <ul class="list-group" id="companyDetails"></ul>
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
    document.getElementById('companySearchForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submission
        const formData = new FormData(this);
        showLoader(); // Show loader
        // Clear previous results
        document.getElementById('companySearchOutput').style.display = 'none';
        document.getElementById('companyDetails').innerHTML = '';

        // Perform API call
        fetch('company_search_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const company = data.data;

                    // Dynamic handling for both APIs
                    let detailsHtml = '';
                    if (company.RESPONSE) {
                        const pinData = company.RESPONSE.PINDATA;

                        // Add basic fields dynamically
                        detailsHtml = `
        <li class="list-group-item"><strong>KRA PIN:</strong> ${pinData.KRAPIN || 'N/A'}</li>
        <li class="list-group-item"><strong>Company Name:</strong> ${pinData.FirstName || 'N/A'}</li>
        <li class="list-group-item"><strong>Business Type:</strong> ${pinData.BusinessType || 'N/A'}</li>
        <li class="list-group-item"><strong>Business Subtype:</strong> ${pinData.BusinessSubType || 'N/A'}</li>
        <li class="list-group-item"><strong>Status of PIN:</strong> ${pinData.StatusOfPIN || 'N/A'}</li>
        <li class="list-group-item"><strong>PIN Issuance Date:</strong> ${pinData.PINIssuanceDate || 'N/A'}</li>
        <li class="list-group-item"><strong>Residential Status:</strong> ${pinData.ResidentialStatus || 'N/A'}</li>
        <li class="list-group-item"><strong>Identification Number:</strong> ${pinData.IdentificationNumber || 'N/A'}</li>
        <li class="list-group-item"><strong>Registration Date:</strong> ${pinData.BusinessRegistrationDate || 'N/A'}</li>
        <li class="list-group-item"><strong>Email Address:</strong> ${pinData.EmailAddress || 'N/A'}</li>
        <li class="list-group-item"><strong>Mobile Number:</strong> ${pinData.MobileNumber || 'N/A'}</li>
    `;

                        // Add Principal Physical Address (if available)
                        if (pinData.PrincipalPhysicalAddress) {
                            const address = pinData.PrincipalPhysicalAddress;
                            detailsHtml += `
            <li class="list-group-item"><strong>Physical Address:</strong> ${address.Building || 'N/A'}, ${address.StreetRoad || 'N/A'}, ${address.CityTown || 'N/A'}</li>
            <li class="list-group-item"><strong>County:</strong> ${address.County || 'N/A'}</li>
            <li class="list-group-item"><strong>District:</strong> ${address.District || 'N/A'}</li>
        `;
                        }

                        // Add Principal Postal Address (if available)
                        if (pinData.PrincipalPostalAddress) {
                            const postal = pinData.PrincipalPostalAddress;
                            detailsHtml += `
            <li class="list-group-item"><strong>Postal Code:</strong> ${postal.PostalCode || 'N/A'}</li>
            <li class="list-group-item"><strong>P.O. Box:</strong> ${postal.POBox || 'N/A'}</li>
        `;
                        }

                        // Add EPZ Effective Date and Legal Representative PIN
                        detailsHtml += `
        <li class="list-group-item"><strong>EPZ Effective Date:</strong> ${pinData.EPZEffectiveDate || 'N/A'}</li>
        <li class="list-group-item"><strong>Legal Representative PIN:</strong> ${pinData.LegalRepresentativePIN || 'N/A'}</li>
    `;

                        // Add Shareholders (if available)
                        if (pinData.ShareholderDetails?.Shareholder) {
                            detailsHtml += '<h6 class="mt-3">Shareholders:</h6>';
                            pinData.ShareholderDetails.Shareholder.forEach((shareholder, index) => {
                                detailsHtml += `
                <li class="list-group-item">
                    #${index + 1} ${shareholder.TypeOfShareholder || 'Unknown'}: ${shareholder.ShareholderPin || 'N/A'}
                </li>
            `;
                            });
                        }
                    } else if (company.records) {
                        const record = company.records[0];

                        // Add basic fields dynamically
                        detailsHtml = `
        <li class="list-group-item"><strong>Business Name:</strong> ${record.business_name || 'N/A'}</li>
        <li class="list-group-item"><strong>Registration Number:</strong> ${record.registration_number || 'N/A'}</li>
        <li class="list-group-item"><strong>Email:</strong> ${record.email || 'N/A'}</li>
        <li class="list-group-item"><strong>Phone:</strong> ${record.phone_number || 'N/A'}</li>
        <li class="list-group-item"><strong>Physical Address:</strong> ${record.physical_address || 'N/A'}</li>
        <li class="list-group-item"><strong>Postal Address:</strong> ${record.postal_address || 'N/A'}</li>
        <li class="list-group-item"><strong>Registration Date:</strong> ${record.registration_date || 'N/A'}</li>
        <li class="list-group-item"><strong>Status:</strong> ${record.status || 'N/A'}</li>
    `;

                        // Add Share Capital (if available)
                        if (record.share_capital?.length) {
                            detailsHtml += '<h6 class="mt-3">Share Capital:</h6>';
                            record.share_capital.forEach((capital, index) => {
                                detailsHtml += `
                <li class="list-group-item">#${index + 1} Shares: ${capital.number_of_shares || 'N/A'}, Value: ${capital.nominal_value || 'N/A'}, Type: ${capital.name || 'N/A'}</li>
            `;
                            });
                        }

                        // Add Partners (if available)
                        if (record.partners?.length) {
                            detailsHtml += '<h6 class="mt-3">Partners:</h6>';
                            record.partners.forEach((partner, index) => {
                                detailsHtml += `
                <li class="list-group-item">
                    #${index + 1} ${partner.type || 'Unknown'}: ${partner.name || 'N/A'} (${partner.id_type || 'N/A'} - ${partner.id_number || 'N/A'})
                    <ul>
                        ${partner.shares?.map((share, i) => `
                            <li><strong>Share #${i + 1}:</strong> ${share.number_of_shares || 'N/A'} shares (${share.name || 'N/A'})</li>
                        `).join('') || '<li>No shares listed</li>'}
                    </ul>
                </li>
            `;
                            });
                        }
                    } else if (company.business) {
                        const record = company.business;

                        // Add basic fields dynamically
                        detailsHtml = `
        <li class="list-group-item"><strong>Business Name:</strong> ${record.business_name || 'N/A'}</li>
        <li class="list-group-item"><strong>Registration Number:</strong> ${record.registration_number || 'N/A'}</li>
        <li class="list-group-item"><strong>Email:</strong> ${record.email || 'N/A'}</li>
        <li class="list-group-item"><strong>Phone:</strong> ${record.phone_number || 'N/A'}</li>
        <li class="list-group-item"><strong>Physical Address:</strong> ${record.physical_address || 'N/A'}</li>
        <li class="list-group-item"><strong>Postal Address:</strong> ${record.postal_address || 'N/A'}</li>
        <li class="list-group-item"><strong>Registration Date:</strong> ${record.registration_date || 'N/A'}</li>
        <li class="list-group-item"><strong>Status:</strong> ${record.status || 'N/A'}</li>
    `;

                        // Add Share Capital (if available)
                        if (record.share_capital?.length) {
                            detailsHtml += '<h6 class="mt-3">Share Capital:</h6>';
                            record.share_capital.forEach((capital, index) => {
                                detailsHtml += `
                <li class="list-group-item">#${index + 1} Shares: ${capital.number_of_shares || 'N/A'}, Value: ${capital.nominal_value || 'N/A'}, Type: ${capital.name || 'N/A'}</li>
            `;
                            });
                        }

                        // Add Partners (if available)
                        if (record.partners?.length) {
                            detailsHtml += '<h6 class="mt-3">Partners:</h6>';
                            record.partners.forEach((partner, index) => {
                                detailsHtml += `
                <li class="list-group-item">
                    #${index + 1} ${partner.type || 'Unknown'}: ${partner.name || 'N/A'} (${partner.id_type || 'N/A'} - ${partner.id_number || 'N/A'})
                    <ul>
                        ${partner.shares?.map((share, i) => `
                            <li><strong>Share #${i + 1}:</strong> ${share.number_of_shares || 'N/A'} shares (${share.name || 'N/A'})</li>
                        `).join('') || '<li>No shares listed</li>'}
                    </ul>
                </li>
            `;
                            });
                        }
                    }


                    document.getElementById('companyDetails').innerHTML = detailsHtml;
                    document.getElementById('companySearchOutput').style.display = 'block'; // Show results
                } else {
                    alert(data.message || 'No results found.');
                }
            })
            .catch(error => {
                // Fetch raw text for debugging
                fetch('company_search_handler.php', {
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

<script>
    // Handle dynamic field updates
    document.getElementById('searchType').addEventListener('change', function() {
        const field = document.getElementById('dynamicField');
        const label = document.getElementById('dynamicFieldLabel');

        if (this.value === 'kraPin') {
            label.textContent = 'Enter KRA PIN';
            field.placeholder = 'Enter KRA PIN';
            field.name = 'kraPin'; // Update field name
        } else if (this.value === 'brsNumber') {
            label.textContent = 'Enter BRS Number';
            field.placeholder = 'Enter BRS Number';
            field.name = 'brsNumber'; // Update field name
        }
    });
</script>
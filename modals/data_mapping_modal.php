<div class="modal fade" id="mapModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white position-relative">
                <h5 class="modal-title"><i class="bi bi-file-lock"></i> Kestrel Data Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- File Upload Section -->
            <div class="modal-body" id="uploadSection">
                <div class="dropzone bg-light rounded-3 p-4 text-center border-dashed">
                    <form id="kestrelUploadForm" enctype="multipart/form-data">
                        <input type="file" name="kestrel_file" class="form-control"
                            accept=".kestrel,.pkestrel" required>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-upload"></i> Upload Kestrel File
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mapping Section (hidden initially) -->
            <!-- Update mapping section -->
            <div class="modal-body" id="mappingSection" style="display:none;">
                <div id="mappingContent">
                    <form id="saveMappingsForm" method="post">
                        <input type="hidden" name="template_id" id="templateId">
                        <div id="mappingContainer" class="mb-3"></div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> Save Mappings
                            </button>
                        </div>
                    </form>
                </div>
                <div id="emptyState" class="text-center py-4" style="display:none;">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h5 class="mt-3">No Mappings Found</h5>
                    <p class="text-muted">The uploaded file doesn't contain compatible data structure</p>
                    <button class="btn btn-outline-primary" onclick="resetUpload()">
                        <i class="bi bi-arrow-clockwise"></i> Try Another File
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let currentTemplateId = null;

        // Get template ID when modal opens
        $('#mapModal').on('show.bs.modal', function(e) {
            currentTemplateId = $(e.relatedTarget).data('template-id');
            $('#templateId').val(currentTemplateId);
            resetModalState();
        });

        function resetModalState() {
            $('#uploadSection').show();
            $('#mappingSection').hide();
            $('#mappingContainer').empty();
            $('#kestrelUploadForm')[0].reset();
        }

        // Handle file upload
        $('#kestrelUploadForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button');
            $btn.prop('disabled', true).html('<i class="bi bi-upload"></i> Uploading...');

            const formData = new FormData(this);
            formData.append('template_id', currentTemplateId);

            $.ajax({
                url: './upload_kestrel.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.status === 'success') {
                        $('#uploadSection').hide();
                        $('#mappingSection').show();
                        loadJsonStructure();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: (xhr) => {
                    showAlert('error', 'Upload failed: ' + xhr.responseText);
                },
                complete: () => {
                    $btn.prop('disabled', false)
                        .html('<i class="bi bi-upload"></i> Upload Kestrel File');
                }
            });
        });

        // Handle save mappings
        $('#saveMappingsForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button');
            $btn.prop('disabled', true).html('<i class="bi bi-save"></i> Saving...');

            $.ajax({
                url: './save_mappings.php',
                type: 'POST',
                data: $(this).serialize(),
                success: (response) => {
                    if (response.status === 'success') {
                        $('#mapModal').modal('hide');
                        showAlert('success', 'Mappings saved successfully');
                        refreshTemplateList();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: (xhr) => {
                    showAlert('error', 'Save failed: ' + xhr.responseText);
                },
                complete: () => {
                    $btn.prop('disabled', false)
                        .html('<i class="bi bi-save"></i> Save Mappings');
                }
            });
        });

        function loadJsonStructure() {
            $.ajax({
                url: './get_mapping_options.php',
                data: {
                    template_id: currentTemplateId
                },
                success: (data) => {
                    if (data.status === 'success') {
                        if (data.placeholders?.length > 0 && data.jsonPaths?.length > 0) {
                            renderMappingInterface(data);
                        } else {
                            $('#mappingSection').hide();
                            showAlert('warning', 'Cannot map - no placeholders or JSON paths found', true);
                        }
                    } else {
                        showAlert('error', data.message, true);
                    }
                },
                error: (xhr) => {
                    showAlert('error', 'Connection error: ' + xhr.statusText, true);
                }
            });
        }

        function renderMappingInterface(data) {
            let html = '';

            // Create a dropdown for each placeholder
            data.placeholders.forEach(placeholder => {
                html += `
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">${placeholder}</label>
            </div>
            <div class="col-md-8">
                <select name="mappings[${placeholder}]" class="form-select" required>
                    <option value="">Select JSON Path</option>
                    ${data.jsonPaths.map(path => `
                        <option value="${path}">${path}</option>
                    `).join('')}
                </select>
            </div>
        </div>`;
            });

            $('#mappingContainer').html(html);
            $('#mappingSection').show(); // Explicitly show after rendering
        }

        function showAlert(type, message, hideSection = false) {
            if (hideSection) {
                $('#mappingSection').hide();
            }

            const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

            $('#mapModal .modal-body:first').prepend(alert);
        }

        function refreshTemplateList() {
            // Implement your template list refresh logic here
            // Example: $('#templateList').load(location.href + ' #templateList');
        }
    });
</script>

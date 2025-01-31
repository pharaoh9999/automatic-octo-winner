<div class="modal fade" id="kestrelMappingModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-bg-dark border border-success-subtle">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title h2 pb-2 text-success border-bottom border-success"><i class="bi bi-file-lock"></i> Kestrel Data Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Data Preview -->
                    <div class="col-md-6 border-end">
                        <h6>Decrypted Data Structure</h6>
                        <pre id="kestrelDataPreview" class="bg-light p-3" style="max-height: 500px; overflow: auto;"></pre>
                    </div>
                    
                    <!-- Mapping Interface -->
                    <div class="col-md-6">
                        <div id="kestrelFieldMapper">
                            <!-- Dynamic fields will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load decrypted data preview
$(document).ready(function() {
    $('#kestrelMappingModal').on('show.bs.modal', function() {
        $.post('./preview_kestrel.php', function(response) {
            $('#kestrelDataPreview').text(JSON.stringify(response.data, null, 2));
            buildMappingInterface(response.sample);
        }, 'json');
    });
});

// Auto-generate mapping fields
function buildMappingInterface(sampleData) {
    let html = '';
    const fields = flattenObject(sampleData);
    
    fields.forEach(field => {
        html += `
        <div class="mb-3">
            <label class="form-label">${field.path}</label>
            <input type="text" 
                   name="mappings[${field.path}]" 
                   class="form-control" 
                   placeholder="Template placeholder name"
                   value="{{${field.path.replace(/\./g, '_')}}}">
            <div class="form-text">Sample value: ${field.value}</div>
        </div>`;
    });
    
    $('#kestrelFieldMapper').html(html);
}

// Helper to flatten nested JSON
function flattenObject(obj, prefix = '') {
    // ... (implementation from previous step)
}
</script>
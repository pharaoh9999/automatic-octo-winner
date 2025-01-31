<div class="modal fade" id="uploadModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Upload Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="./upload_template.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="file" name="template" class="form-control" accept=".docx" required>
                        <div class="form-text">Only .docx files under 5MB allowed</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload"></i> Process Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
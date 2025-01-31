<?php
require_once 'includes/config.php';
require_once 'includes/function.php';

try {
    $stmt = $conn->prepare("SELECT * FROM document_templates");
    if (!$stmt->execute()) throw new Exception('Failed to load templates');
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $historyStmt = $conn->prepare("SELECT * FROM document_history WHERE user_id = ? ORDER BY generated_at DESC LIMIT 5");
    if (!$historyStmt->execute([$_SESSION['user_id']])) throw new Exception('Failed to load history');
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error = "Database connection error";
} catch (Exception $e) {
    error_log("Document Error: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/head.php'; ?>
</head>

<body>

    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <h1 class="text-center">Doc Page</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Upload Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="h2 pb-2 text-success border-bottom border-success"><i class="bi bi-upload me-2"></i>Upload New Template</h3>
            </div>
            <div class="card-body">
                <div class="dropzone bg-light rounded-3 p-4 text-center border-dashed"
                    id="dropzone"
                    data-bs-toggle="modal"
                    data-bs-target="#uploadModal">
                    <p class="text-muted">Drag & drop DOCX files here<br>or click to browse</p>
                </div>
            </div>
        </div>

        <!-- Template List -->
        <div class="card mt-4 mt-4">
            <div class="card-header">
                <h3 class=" h2 pb-2 text-success border-bottom border-success"><i class="bi bi-files me-2"></i>Your Templates</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-dark">
                        <thead>
                            <tr>
                                <th>Template Name</th>
                                <th>Placeholders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM document_templates");
                            $stmt->execute();
                            while ($template = $stmt->fetch(PDO::FETCH_ASSOC)) :
                            ?>
                                <tr>
                                    <td><?= basename($template['file_path']) ?></td>
                                    <td>
                                        <?php
                                        $placeholders = json_decode($template['placeholders']);
                                        echo implode(', ', $placeholders);
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#mapModal"
                                            data-template-id="<?= $template['id'] ?>">
                                            <i class="bi bi-puzzle"></i> Map Data
                                        </button>
                                        <button class="btn btn-sm btn-outline-success generate-btn"
                                            data-template-id="<?= $template['id'] ?>">
                                            <i class="bi bi-magic"></i> Generate
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-template-id="<?= $template['id'] ?>">
                                            <i class="bi bi-magic"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Generation History -->
        <div class="card mt-4 mt-4">
            <div class="card-header">
                <h3 class="h2 pb-2 text-success border-bottom border-success"><i class="bi bi-clock-history me-2"></i>Recent Generations</h3>
            </div>
            <div class="card-body">
                <div id="historyList">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM document_history WHERE user_id = ? ORDER BY generated_at DESC");
                    $stmt->execute([$_SESSION['user_id']]);
                    while ($history = $stmt->fetch(PDO::FETCH_ASSOC)) :
                    ?>
                        
                        <ul class="list-group list-group-horizontal p-2 mb-2 w-100">
                            <li class="list-group-item"><?= basename($history['file_path']) ?></li>
                            <li class="list-group-item"><?= $history['generated_at'] ?></li>
                            <li class="list-group-item"><a href="<?= $history['file_path'] ?>" class="btn btn-warning" download>Download</a></li>
                        </ul>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <!-- Modals -->
    <div id="alertContainer" class="position-absolute top-100 start-50 translate-middle-x w-75 mt-2"></div>
    <?php include 'modals/template_upload_modal.php'; ?>
    <?php include 'modals/data_mapping_modal.php'; ?>

    <!-- Scripts -->
    <script>
        // $('.generate-btn').click(function() {
        //     const $btn = $(this);
        //     const templateId = $btn.data('template-id');

        //     $btn.prop('disabled', true).html('<i class="bi bi-hourglass"></i> Generating...');

        //     $.ajax({
        //         url: './generate_document.php',
        //         method: 'POST',
        //         data: {
        //             template_id: templateId
        //         },
        //         success: function(response, status, xhr) {
        //             showErrorAlert(response); // Print raw success response exactly as received
        //         },
        //         error: function(xhr) {
        //             showErrorAlert(xhr.responseText || `Error ${xhr.status}: ${xhr.statusText}`); // Print raw error response exactly as received
        //         },
        //         complete: function() {
        //             $btn.prop('disabled', false).html('<i class="bi bi-magic"></i> Generate');
        //         }
        //     });
        // });

        $('.generate-btn').click(function() {
            const $btn = $(this);
            const templateId = $btn.data('template-id');

            $btn.prop('disabled', true).html('<i class="bi bi-hourglass"></i> Generating...');

            $.ajax({
                url: './generate_document.php',
                method: 'POST',
                data: {
                    template_id: templateId
                },
                dataType: 'json', // Force JSON parsing first
                success: function(response, status, xhr) {
                    // Handle DOCX response if content-type matches
                    if (xhr.getResponseHeader('Content-Type')?.includes('application/vnd.openxmlformats')) {
                        const blob = new Blob([xhr.response], {
                            type: xhr.getResponseHeader('Content-Type')
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '');
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    } else {
                        // Handle JSON response
                        if (response.success) {
                            showSuccessAlert('Document generated successfully');
                        } else {
                            showErrorAlert(response.error || 'Unknown error occurred');
                        }
                    }
                },
                error: function(xhr) {
                    let errorMsg = `Error ${xhr.status}: `;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg += response.error || xhr.statusText;
                    } catch {
                        errorMsg += xhr.statusText;
                    }
                    showErrorAlert(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false)
                        .html('<i class="bi bi-magic"></i> Generate');
                }
            });
        });

        function showErrorAlert(message) {
            const alertHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <pre>${message}</pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            $('#alertContainer').html(alertHTML);
        }

        function showSuccessAlert(message) {
            const alertHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <pre>${message}</pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            $('#alertContainer').html(alertHTML);
        }
    </script>

    <script>
        /**
         * Trigger template deletion via AJAX when user clicks the .delete-btn
         */
        $('.delete-btn').on('click', function() {
            const $btn = $(this);
            const templateId = $btn.data('template-id');

            // Confirmation dialog
            if (!confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
                return;
            }

            // Disable button and show 'Deleting...' state
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Deleting...');

            // AJAX request to delete_template.php
            $.ajax({
                url: 'delete_template.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the template row from the table (quick UI feedback)
                        $btn.closest('tr').remove();

                        // Optionally show a success alert
                        showDeleteAlert('Template deleted successfully.');
                    } else {
                        // If an error occurred (e.g., file not found, DB error)
                        showDeleteAlert(response.error || 'An error occurred while deleting the template.', true);
                    }
                },
                error: function(xhr) {
                    showDeleteAlert(xhr.responseText || `Error ${xhr.status}: ${xhr.statusText}`, true);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-magic"></i> Delete');
                }
            });
        });

        /**
         * Display alerts in top-right alert container
         * @param {string} message - The message to display
         * @param {boolean} [isError=false] - Whether the alert is an error
         */
        function showDeleteAlert(message, isError = false) {
            const alertType = isError ? 'danger' : 'success';
            const alertHTML = `
        <div class="alert alert-${alertType} alert-dismissible fade show shadow-lg mb-2" role="alert">
            <pre class="mb-0" style="white-space: pre-wrap;">${message}</pre>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
            $('#alertContainer').append(alertHTML);
        }
    </script>



</body>

</html>
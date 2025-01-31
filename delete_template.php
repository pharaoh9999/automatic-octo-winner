<?php
require_once 'includes/config.php';
require_once 'includes/function.php';

header('Content-Type: application/json');

// Ensure the user is logged in and has the role/permission to delete templates
if (!isset($_SESSION['user_id']) || !checkUserRole('document_generation')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized: Missing permissions']);
    exit;
}

// Validate template_id
$template_id = filter_input(INPUT_POST, 'template_id', FILTER_VALIDATE_INT);
if (!$template_id || $template_id < 1) {
    echo json_encode(['error' => 'Invalid template ID']);
    exit;
}

try {
    // Check if template exists
    $stmt = $conn->prepare("SELECT file_path FROM document_templates WHERE id = ?");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$template) {
        echo json_encode(['error' => 'Template record not found.']);
        exit;
    }

    // Delete the file from the server (if it exists)
    $filePath = $template['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Remove from document_templates
    $deleteStmt = $conn->prepare("DELETE FROM document_templates WHERE id = ?");
    $deleteStmt->execute([$template_id]);

    // Also remove any related mappings, if applicable
    $mapStmt = $conn->prepare("DELETE FROM template_data_map WHERE template_id = ?");
    $mapStmt->execute([$template_id]);

    // Optionally remove from document_history if you never want to show history after a template is gone
    // $historyStmt = $conn->prepare("DELETE FROM document_history WHERE template_id = ?");
    // $historyStmt->execute([$template_id]);

    echo json_encode(['success' => true]);
    exit;
} catch (PDOException $e) {
    error_log("Delete Template Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred while deleting the template.']);
    exit;
} catch (Exception $ex) {
    error_log("Delete Template Error: " . $ex->getMessage());
    echo json_encode(['error' => $ex->getMessage()]);
    exit;
}

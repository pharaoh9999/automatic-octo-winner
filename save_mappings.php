<?php
//session_start();
require_once  './includes/config.php';
require_once  './includes/function.php';

header('Content-Type: application/json');

try {
    // Validate session and permissions
    if (!isset($_SESSION['user_id']) || !checkUserRole('manage_mappings')) {
        throw new Exception('Unauthorized access');
    }

    // Validate input
    if (!isset($_POST['template_id']) || !isset($_POST['mappings'])) {
        throw new Exception('Missing required parameters');
    }

    $templateId = (int)$_POST['template_id'];
    $mappings = $_POST['mappings'];

    // Start transaction
    $conn->beginTransaction();

    try {
        // Delete old mappings
        $stmt = $conn->prepare("DELETE FROM template_data_map WHERE template_id = ?");
        $stmt->execute([$templateId]);

        // Insert new mappings
        $stmt = $conn->prepare("INSERT INTO template_data_map 
                              (template_id, placeholder, data_source) 
                              VALUES (?, ?, ?)");

        foreach ($mappings as $placeholder => $dataSource) {
            if (!empty($dataSource)) {
                $stmt->execute([$templateId, $placeholder, $dataSource]);
            }
        }

        $conn->commit();
        activityLog($_SESSION['user_id'],'Mappings Saving', json_encode($_POST));
        echo json_encode([
            'status' => 'success',
            'message' => 'Mappings saved successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
<?php
require_once '../includes/config.php';
require_once '../includes/function.php';

if (!isset($_SESSION['active_kestrel_file'])) {
    die(json_encode(['error' => 'No active Kestrel file']));
}

try {
    // Decrypt and decode Kestrel file
    $rawData = base64_decode(readCustomFile($_SESSION['active_kestrel_file']));
    $jsonData = json_decode($rawData, true);
    
    // Get placeholders from template
    $stmt = $conn->prepare("SELECT placeholders FROM document_templates WHERE id = ?");
    $stmt->execute([$_GET['template_id']]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'placeholders' => json_decode($template['placeholders'], true),
        'jsonPaths' => getJsonPaths($jsonData)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
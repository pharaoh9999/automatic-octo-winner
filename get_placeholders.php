<?php
require_once './includes/config.php';
require_once './includes/function.php';

// Authorization check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('Unauthorized');
}

$templateId = $_GET['template_id'];
$stmt = $conn->prepare("SELECT placeholders FROM document_templates WHERE id = ?");
$stmt->execute([$templateId]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'placeholders' => json_decode($template['placeholders'])
]);
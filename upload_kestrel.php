<?php
//session_start();
require_once'./includes/config.php';
require_once'./includes/function.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_FILES['kestrel_file']) || !isset($_POST['template_id'])) {
        throw new Exception('Missing required parameters');
    }

    $file = $_FILES['kestrel_file'];
    $templateId = (int)$_POST['template_id'];

    // Validate file
    $allowedExtensions = ['kestrel', 'pkestrel'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file type. Allowed: .kestrel, .pkestrel');
    }

    // Store file
    $targetDir ='./uploads/kestrel/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    
    $filename = 'kestrel_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
    $targetPath = $targetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    activityLog($_SESSION['user_id'],'Kestrel File Upload', $filename);

    // Store in session
    $_SESSION['active_kestrel_file'] = $targetPath;
    $_SESSION['current_template_id'] = $templateId;

    echo json_encode([
        'status' => 'success',
        'message' => 'File uploaded successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
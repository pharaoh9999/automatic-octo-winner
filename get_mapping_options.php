<?php
//session_start();
require_once  './includes/config.php';
require_once  './includes/function.php';

header('Content-Type: application/json');

try {
    // 1. Validate session
    if (!isset($_SESSION['active_kestrel_file'])) {
        throw new Exception('Session expired or invalid file reference');
    }

    // 2. Load Kestrel data
    $filePath = $_SESSION['active_kestrel_file'];
    $encryptedData = file_get_contents($filePath);
    $decryptedData = readCustomFile($filePath);
    $base64Data = base64_decode($decryptedData);
    
    if (!$base64Data) {
        throw new Exception('Base64 decoding failed');
    }

    $jsonData = json_decode($base64Data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON parse error: ' . json_last_error_msg());
    }

    // 3. Get template data
    $stmt = $conn->prepare("SELECT placeholders FROM document_templates WHERE id = ?");
    $stmt->execute([$_GET['template_id']]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$template) {
        throw new Exception('Template not found in database');
    }

    $placeholders = json_decode($template['placeholders'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid placeholders format in template');
    }

    // 4. Generate JSON paths
    $jsonPaths = [];
    
    function flattenJson($data, $prefix = '') {
        $paths = [];
        foreach ($data as $key => $value) {
            $currentPath = $prefix ? "$prefix.$key" : $key;
            
            if (is_array($value) || is_object($value)) {
                // Handle arrays with index placeholders
                if (is_array($value) && array_keys($value) !== range(0, count($value) - 1)) {
                    $paths[] = $currentPath . '.*'; // Array wildcard
                }
                $paths = array_merge($paths, flattenJson((array)$value, $currentPath));
            } else {
                $paths[] = $currentPath;
            }
        }
        return $paths;
    }

    $jsonPaths = flattenJson($jsonData);
    $jsonPaths = array_unique($jsonPaths);

    if (empty($jsonPaths)) {
        error_log("JSON Structure: " . print_r($jsonData, true)); // Debug
        throw new Exception('No valid JSON paths found in Kestrel file');
    }

    echo json_encode([
        'status' => 'success',
        'placeholders' => $placeholders,
        'jsonPaths' => array_unique($jsonPaths)
    ]);

} catch (Exception $e) {
    error_log("Mapping Options Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Helper function to find JSON paths
function getJsonPath($data, $targetValue, $currentPath = '') {
    foreach ($data as $key => $value) {
        $path = $currentPath ? "$currentPath.$key" : $key;
        
        if (is_array($value)) {
            $result = getJsonPath($value, $targetValue, $path);
            if ($result) return $result;
        } else {
            if ($value === $targetValue) {
                return $path;
            }
        }
    }
    return null;
}
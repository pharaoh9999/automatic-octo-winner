<?php
require_once './includes/config.php';
require_once './includes/function.php';
require_once 'vendor/autoload.php';
// session_start(); // Uncomment if sessions are not already started in functions.php

if (!isset($_SESSION['user_id']) || !checkUserRole('document_generation')) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized: Missing permissions']));
}

$template_id = filter_input(INPUT_POST, 'template_id', FILTER_VALIDATE_INT);
if (!$template_id || $template_id < 1) {
    die(json_encode(['error' => "Invalid template ID: '{$template_id}'"]));
}

try {
    // Load template
    $stmt = $conn->prepare("SELECT * FROM document_templates WHERE id = ?");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$template || !file_exists($template['file_path'])) {
        die(json_encode(['error' => "Template file not found at " . $template['file_path']]));
    }

    // Load Kestrel data
    if (empty($_SESSION['active_kestrel_file']) || !file_exists($_SESSION['active_kestrel_file'])) {
        die(json_encode(['error' => 'No valid Kestrel file found.']));
    }

    $decryptedData = base64_decode(readCustomFile($_SESSION['active_kestrel_file']));
    $kestrelData = json_decode($decryptedData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die(json_encode(['error' => 'Invalid Kestrel data format: ' . json_last_error_msg()]));
    }

    // Load placeholders
    $stmt = $conn->prepare("SELECT placeholder, data_source FROM template_data_map WHERE template_id = ?");
    $stmt->execute([$template_id]);
    $mappings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!$mappings) {
        die(json_encode(['error' => "No mappings found for template ID: {$template_id}"]));
    }

    // Process Word template
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template['file_path']);
    foreach ($mappings as $placeholder => $jsonPath) {
        $cleanPath = str_replace('.', '.', $jsonPath);
        $value = resolveJsonPath($kestrelData, $cleanPath) ?? 'N/A';
        $templateProcessor->setValue($placeholder, htmlspecialchars($value, ENT_QUOTES));
    }

    // Handle vehicleAssets array
    if (isset($kestrelData['vehicleAssets']['assets']) && is_array($kestrelData['vehicleAssets']['assets'])) {
        $vehiclesText = "";
        foreach ($kestrelData['vehicleAssets']['assets'] as $vehicle) {
            $vehicle_no = $vehicle['mechanical_data']['regNo'] ?? 'N/A';
            $engine_no = $vehicle['mechanical_data']['engineNumber'] ?? 'N/A';
            $vehiclesText .= "- {$vehicle_no} ({$engine_no})\n";
        }
        $templateProcessor->setValue('vehicleAssets_assets', $vehiclesText);
    }

    // Save output file
    $outputFilename = 'generated_' . bin2hex(random_bytes(8)) . '.docx';
    $outputPath = './generated_docs/' . $outputFilename;
    if (!is_dir('./generated_docs') && !mkdir('./generated_docs', 0755, true)) {
        throw new Exception('Failed to create output directory.');
    }
    $templateProcessor->saveAs($outputPath);

    // Log generation history
    $stmt = $conn->prepare("INSERT INTO document_history (user_id, template_id, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $template_id, $outputPath]);

    // If you want immediate download, uncomment below (and remove the JSON response).
    // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    // header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
    // header('Content-Length: ' . filesize($outputPath));
    // ob_clean();
    // flush();
    // readfile($outputPath);
    // exit;

    echo json_encode(['success' => true, 'file' => $outputFilename]);
} catch (PDOException $e) {
    http_response_code(400);
    die(json_encode(['error' => "Database operation failed: " . $e->getMessage()]));
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['error' => $e->getMessage()]));
}

/**
 * Resolves a JSON path and returns the value
 */
function resolveJsonPath(array $data, string $path)
{
    $keys = explode('.', $path);
    $current = $data;
    foreach ($keys as $key) {
        if (preg_match('/(.*)\[(\d+)\]$/', $key, $matches)) {
            if (!isset($current[$matches[1]][$matches[2]])) return null;
            $current = $current[$matches[1]][$matches[2]];
        } else {
            if (!isset($current[$key])) return null;
            $current = $current[$key];
        }
    }
    if (is_array($current)) {
        return implode(', ', array_filter($current, 'is_scalar'));
    }
    return $current;
}

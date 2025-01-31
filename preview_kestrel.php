<?php
require_once '../includes/config.php';
require_once '../includes/function.php';

if (!isset($_SESSION['active_kestrel_file'])) die("No active file");

$data = processKestrelFile($_SESSION['active_kestrel_file']);
if (!$data) die("Invalid kestrel file");

// Extract sample values for UI
$sampleValues = [];
array_walk_recursive($data, function($value, $key) use (&$sampleValues) {
    if (strlen($value) < 50) { // Exclude long values
        $sampleValues[] = $value;
    }
});

header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'sample' => array_slice($sampleValues, 0, 10) // First 10 sample values
]);
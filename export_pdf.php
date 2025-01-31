<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions
require_once 'vendor/autoload.php'; // Twig autoloader

use Dompdf\Dompdf;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Validate the search_id
if (!isset($_GET['search_id']) || empty($_GET['search_id'])) {
    die("Invalid search ID.");
}

$search_id = intval($_GET['search_id']);

// Fetch the result file path
$query = "SELECT results FROM saved_searches WHERE id = :search_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':search_id', $search_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Invalid search ID or no results found.");
}

$file_path = $row['results'];

// Use the custom function to read the file
$file_content = readCustomFile($file_path);

if ($file_content === false) {
    die("Error reading result file.");
}

// Decode the base64-encoded content
$json_content = base64_decode($file_content);

if ($json_content === false) {
    die("Error decoding result file.");
}

// Parse the JSON content
$data = json_decode($json_content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error parsing result file: " . json_last_error_msg());
}

function flattenArray($array, $prefix = '') {
    $result = [];
    foreach ($array as $key => $value) {
        $newKey = $prefix ? $prefix . '.' . $key : $key;
        if (is_array($value)) {
            $result += flattenArray($value, $newKey);
        } else {
            $result[$newKey] = $value;
        }
    }
    return $result;
}

// Example for vehicleAssets
if (isset($data['vehicleAssets']['assets'])) {
    foreach ($data['vehicleAssets']['assets'] as $index => $asset) {
        $data['vehicleAssets']['assets'][$index] = flattenArray($asset);
    }
}


// Set up Twig
$loader = new FilesystemLoader('templates'); // Path to your Twig templates
$twig = new Environment($loader);

// Render the HTML using Twig
$html = $twig->render('results.twig', ['data' => $data]);

// Generate PDF using Dompdf
$dompdf = new Dompdf();
$dompdf->set_option('isHtml5ParserEnabled', true);
$dompdf->set_option('isPhpEnabled', true);
$dompdf->set_option('isRemoteEnabled', true); // If you have external resources
$dompdf->set_option('defaultMediaType', 'print');
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("search_results.pdf");

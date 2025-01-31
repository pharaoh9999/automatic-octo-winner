<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Function to handle API calls
function callExternalApi($url, $headers = [], $postData = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$httpCode, json_decode($response, true)];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = isset($_POST['businessName']) ? trim($_POST['businessName']) : '';

    if (empty($businessName)) {
        echo json_encode(["status" => false, "message" => "Business name keyword is required."]);
        exit;
    }

    $apiUrl = 'https://kever.io/finder_7.php';
    $headers = [
        //"Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgwOTEzODYsImV4cCI6MTczODA5NDk4NiwidXNlcklkIjoiOTZfMSJ9.vyT7_t1LUOQrUOpz2RL01I3N3HThTa5HDOdy_GsgcMo",
        "Cookie: PHPSESSID=dafsg49cj7so6vpu2s9bl356k4; authToken=".$_SESSION['token']."; visitorId=973ad0dd0c565ca2ae839d5ebef8447a"
    ];
    $postData = [
        'businessName' => $businessName
    ];

    // Call the API
    list($httpCode, $response) = callExternalApi($apiUrl, $headers, $postData);

    if ($httpCode === 200 && isset($response['data'])) {
        // Log successful search
        $userId = $_SESSION['user_id']; // Replace with actual user ID from session or auth system
        $queryType = 'keyword_search';
        logSearch($userId, $queryType, $businessName, json_encode($response['data']));

        $objStr = $businessName;
        $finalObjData = base64_encode(json_encode($response));
        $filePath = './temp/' . $objStr . '-ks-' . bin2hex(random_bytes(10)) . '.pkestrel'; // Your custom file type
        createCustomFile($filePath, $finalObjData);
        $search = saveSearch($userId, $objStr, $filePath, $queryType);

        // Return API results
        echo json_encode(["status" => true, "data" => $response['data']]);
    } else {
        echo json_encode(["status" => false, "message" => "No results found or API call failed.", "error" => $response]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
?>

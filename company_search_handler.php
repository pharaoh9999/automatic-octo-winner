<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Function to handle API calls
function callExternalApi($url, $headers = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$httpCode, json_decode($response, true)];
}

// Handle company search API calls
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kraPin = isset($_POST['kraPin']) ? trim($_POST['kraPin']) : '';
    $brsNumber = isset($_POST['brsNumber']) ? trim($_POST['brsNumber']) : '';

    if (empty($kraPin) && empty($brsNumber)) {
        echo json_encode(["status" => false, "message" => "Either KRA PIN or BRS Number is required."]);
        exit;
    }

    // API URLs and Headers
    $headers = [];

    $response = null;
    if (!empty($kraPin)) {
        // Search by KRA PIN
        $apiUrl = "https://nairobiservices.go.ke/api/external/user/kra/pin/" . urlencode($kraPin);
        list($httpCode, $response) = callExternalApi($apiUrl, $headers);
    } elseif (!empty($brsNumber)) {
        // Search by BRS Number
        $apiUrl = "https://nairobiservices.go.ke/api/external/brs?reg_no=" . urlencode($brsNumber);
        list($httpCode, $response) = callExternalApi($apiUrl, $headers);
    }

    // Handle response
    if ($httpCode === 200 && isset($response['data'])) {
        // Log successful search
        $userId = $_SESSION['user_id']; // Replace with actual user ID from session or auth system
        $queryType = 'company_search';
        logSearch($userId, $queryType, $kraPin ?: $brsNumber, json_encode($response['data']));

        $objStr = $kraPin . $brsNumber;
        $finalObjData = base64_encode(json_encode($response));
        $filePath = './temp/' . $objStr . '-ccs-' . bin2hex(random_bytes(10)) . '.pkestrel'; // Your custom file type
        createCustomFile($filePath, $finalObjData);
        $search = saveSearch($userId, $objStr, $filePath, $queryType);

        // Return data
        echo json_encode(["status" => true, "data" => $response['data']]);
    } else {
        echo json_encode(["status" => false, "message" => "No results found or API call failed."]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}

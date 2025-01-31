<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Function to handle API calls
function callExternalApi($url, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$httpCode, json_decode($response, true)];
}

// Handle citizen search API calls
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchType = isset($_POST['searchType1']) ? trim($_POST['searchType1']) : '';
    $searchValue = isset($_POST[$searchType]) ? trim($_POST[$searchType]) : '';

    if (empty($searchType) || empty($searchValue)) {
        echo json_encode(["status" => false, "message" => "Search type or value is missing."]);
        exit;
    }

    // API URLs and Headers
    $apiUrl = '';
    if ($searchType === 'kraPin') {
        $apiUrl = "https://nairobiservices.go.ke/api/external/user/kra/pin/" . urlencode($searchValue);
    } elseif ($searchType === 'idNumber') {
        $apiUrl = "https://nairobiservices.go.ke/api/external/user/kra/id/" . urlencode($searchValue);
    } else {
        echo json_encode(["status" => false, "message" => "Invalid search type."]);
        exit;
    }

    $headers = [
       // "Cookie: csrftoken=VZ2buILEt0Ir5eJtyJcVFmNMeYzLP1En; token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjgxMjUxLCJpZF9udW1iZXIiOiIxIiwia3JhX3BpbiI6bnVsbCwiZW1haWwiOiJtQGdtYWlsLmNvbSIsInBhc3Nwb3J0IjpudWxsLCJ1c2VybmFtZSI6IkRBTklFTCAgVE9ST0lUSUNIIEFSQVAgTU9JIiwiZXhwIjoxNzA4Mzk1Nzg4LCJjdXN0b21lcl9pZCI6IjIwMjBfMjc2NzUzIiwibW9iaWxlX251bWJlciI6IjY2In0.28m6o6GnHOYPVVpeTDtCzrQ1bdzmRv-5ZF57gPZdhXo"
    ];

    // Call the API
    list($httpCode, $response) = callExternalApi($apiUrl, $headers);

    // Handle response
    if ($httpCode === 200 && is_array($response)) {
        if ($searchType === 'kraPin' && isset($response['data']['RESPONSE']['PINDATA'])) {
            $responseData = $response['data']['RESPONSE']['PINDATA'];
        } elseif ($searchType === 'idNumber' && isset($response['id'])) {
            $responseData = $response; // The ID search response is flat
        } else {
            echo json_encode(["status" => false, "message" => "Unexpected response structure.", "error" => $response]);
            exit;
        }

        // Log successful search
        $userId = $_SESSION['user_id']; // Replace with actual user ID from session or auth system
        $queryType = 'citizen_search';
        logSearch($userId, 'citizen_search', $searchType . ": " . $searchValue, json_encode($responseData));

        $objStr = $searchValue;
        $finalObjData = base64_encode(json_encode($response));
        $filePath = './temp/' . $objStr . '-cs-' . bin2hex(random_bytes(10)) . '.pkestrel'; // Your custom file type
        createCustomFile($filePath, $finalObjData);
        $search = saveSearch($userId, $objStr, $filePath,$queryType);

        $obj = [
            'firstName' => $responseData['first_name'] ?? $responseData['FirstName'] ?? 'N/A',
            'lastName' => $responseData['last_name'] ?? $responseData['LastName'] ?? 'N/A',
            'idNumber' => $responseData['nid_no'] ?? $responseData['IdentificationNumber'] ?? 'N/A',
            'myNumbers' => [
                ['number' => $responseData['mobile_number'] ?? $responseData['MobileNumber'] ?? 'N/A', 'status' => 'active'],
                ['number' => $responseData['nrs_mobile_number'] ?? 'N/A', 'status' => 'inactive'],
            ],
        ];

        $base64Data = reverseToBase64($obj);

        // Return data
        echo json_encode(["status" => true, "data" => $responseData, "encoded" => $base64Data]);
    } else {
        echo json_encode(["status" => false, "message" => "No results found or API call failed.", "error" => $response]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
?>

<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Function to handle API calls
function callExternalApi($url, $headers = [], $postData = [])
{
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
    // Gather POST data
    $firstnameId = isset($_POST['firstnameId']) ? trim($_POST['firstnameId']) : '';
    $middlenameId = isset($_POST['middlenameId']) ? trim($_POST['middlenameId']) : '';
    $lastnameId = isset($_POST['lastnameId']) ? trim($_POST['lastnameId']) : '';
    $dateOfBirthLow = isset($_POST['dateOfBirthLow']) ? trim($_POST['dateOfBirthLow']) : '';
    $dateOfBirthHigh = isset($_POST['dateOfBirthHigh']) ? trim($_POST['dateOfBirthHigh']) : '';
    $sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
    $orderBy = isset($_POST['orderBy']) ? trim($_POST['orderBy']) : 'random';
    $orderType = isset($_POST['orderType']) ? trim($_POST['orderType']) : 'asc';
    $limit = isset($_POST['limit']) ? trim($_POST['limit']) : '';

    $apiUrl = 'https://kever.io/finder_9.php';
    $headers = [
        "Cookie: PHPSESSID=dafsg49cj7so6vpu2s9bl356k4; authToken=" . $_SESSION['token'] . "; visitorId=973ad0dd0c565ca2ae839d5ebef8447a"
    ];
    $postData = [
        'photo' => 'NULL', // Hidden by requirement
        'dateOfBirthLow' => $dateOfBirthLow,
        'dateOfBirthHigh' => $dateOfBirthHigh,
        'sex' => $sex,
        'firstnameId' => $firstnameId,
        'middlenameId' => $middlenameId,
        'lastnameId' => $lastnameId,
        'orderBy' => $orderBy,
        'orderType' => $orderType,
        'limit' => $limit
    ];

    // Call the API
    list($httpCode, $response) = callExternalApi($apiUrl, $headers, $postData);

    // Handle API response
    if ($httpCode === 200 && isset($response['data'])) {
        // Log successful search
        $userId = $_SESSION['user_id']; // Replace with actual user ID from session or auth system

        foreach($response['data'] as $key => $row){
            $obj = [
                'firstName' => $row['first_name'],
                'lastName' => $row['last_name'],
                'idNumber' => $row['identity_id'],
                'myNumbers' => [
                    ['number' => '0700000000', 'status' => 'active'],
                    ['number' => '0700000000', 'status' => 'inactive'],
                ],
            ];
    
            $base64Data = reverseToBase64($obj);
            //array_push($row,)
            $response['data'][$key]['encodedData'] = $base64Data;
        }
        
        $queryType = 'citizen_param_search';
        logSearch($userId, $queryType, json_encode($postData), json_encode($response['data']));

        $objStr = $firstnameId.$middlenameId.$lastnameId.$sex;
        $finalObjData = base64_encode(json_encode($response));
        $filePath = './temp/' . $objStr . '-cps-' . bin2hex(random_bytes(10)) . '.pkestrel'; // Your custom file type
        createCustomFile($filePath, $finalObjData);
        $search = saveSearch($userId, $objStr, $filePath,$queryType);
        

        // Return API results
        echo json_encode(["status" => true, "data" => $response['data']]);
    } else {
        echo json_encode(["status" => false, "message" => "No results found or API call failed. - " . $response, "error" => $response]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}

<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Function to log search activity

// Handle vehicle search API call
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve search parameter
    $regNo = isset($_POST['numberPlate']) ? trim($_POST['numberPlate']) : '';

    if (empty($regNo)) {
        echo json_encode(["status" => false, "message" => "Registration number is required."]);
        exit;
    }

    // Prepare API request
    $apiUrl = 'https://kever.io/finder_16.php';
    $authToken = $_SESSION['token'];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        'Cookie: PHPSESSID=7d8j381hsqv050c9ai6i4of0aq; authToken=' . $authToken . '; visitorId=973ad0dd0c565ca2ae839d5ebef8447a'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['regNo' => $regNo]));

    // Execute API call
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle API response
    if ($httpCode === 200) {
        $data = json_decode($response, true);

        if ($data['status']) {
            if (count($data['assets']) < 1) {
                $dt1 = json_decode(httpGet('https://nairobiservices.go.ke/api/external/parking/ntsa/vehicle/local/' . $regNo, []), true);
                if (isset($dt1['data'])) {
                    //$dt2 = $dt1['data'];
                    $dt3 = [];
                    $dt2['ID_Number'] = $dt1['data']['id_number'];
                    $dt2['Owner_Name'] = $dt1['data']['owner_name'];
                    $dt2['passport_no'] = $dt1['data']['passport_no'];
                    $dt2['Pin'] = $dt1['data']['pin'];
                    $dt2['mobile_number'] = $dt1['data']['mobile_number'];
                    $dt2['vehicle_no'] = $dt1['data']['vehicle_no'];
                    $dt2['vehicle_model'] = $dt1['data']['vehicle_model'];
                    $dt2['Use'] = $dt1['data']['use'];
                    $dt2['ntsa_id'] = $dt1['data']['ntsa'];
                    $dt2['capacity'] = $dt1['data']['capacity'];


                    $dt2['mechanical_data']['ChassisNo'] = 'N/A';
                    $dt2['mechanical_data']['yearOfManufacture'] = 'N/A';
                    $dt2['mechanical_data']['carMake'] = 'N/A';
                    $dt2['mechanical_data']['carModel'] = 'N/A';
                    $dt2['mechanical_data']['regNo'] = 'N/A';
                    $dt2['mechanical_data']['bodyType'] = 'N/A';
                    $dt2['mechanical_data']['logbookNumber'] = 'N/A';
                    $dt2['mechanical_data']['registrationDate'] = 'N/A';
                    $dt2['mechanical_data']['engineCapacity'] = 'N/A';
                    $dt2['mechanical_data']['passengerCapacity'] = 'N/A';
                    $dt2['mechanical_data']['bodyColor'] = 'N/A';
                    $dt2['mechanical_data']['engineNumber'] = 'N/A';

                    array_push($dt3,$dt2);
                    $data['assets'] = $dt3;
                } else {
                    $dataNull = true;
                }
            }
            // Log successful search
            $userId = $_SESSION['user_id']; // Replace with actual user ID from session or auth system
            $queryType = 'vehicle_search';
            logSearch($userId, $queryType, $regNo, $response);

            $objStr = $regNo;
            $finalObjData = base64_encode(json_encode($data));
            $filePath = './temp/' . $objStr . '-vs-' . bin2hex(random_bytes(10)) . '.pkestrel'; // Your custom file type
            createCustomFile($filePath, $finalObjData);
            $search = saveSearch($userId, $objStr, $filePath, $queryType);

            if (isset($dataNull)) {
                // Return API result
                echo json_encode(["status" => false, "data" => 'No data found!']);
            } else {
                // Return API result
                echo json_encode(["status" => true, "data" => $data['assets']]);
            }
            
        } else {
            echo json_encode(["status" => false, "message" => "No results found."]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "API call failed." . $response]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}

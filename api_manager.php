<?php
session_start();
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

// Flush headers immediately
ob_implicit_flush(true);
ob_end_flush();

include './includes/function.php';
include './includes/config.php';

$token = $_SESSION['token'] ?? null;
$data_obj = $_GET['data'];

//$data_obj = 'eyJHZXRDdXN0b21lckluZm8iOiJleUprWVhSaElqcDdJbWRsZEVOMWMzUnZiV1Z5U1c1bWJ5STZleUptYVhKemRFNWhiV1VpT2lKS2IyaHVJaXdpYkdGemRFNWhiV1VpT2lKRWIyVWlMQ0pwWkU1MWJXSmxjaUk2SWpNeU5URTFOVEl5SW4xOWZRPT0iLCJRdWVyeU15TnVtYmVycyI6ImV5SmtZWFJoSWpwN0luRjFaWEo1VFhsT2RXMWlaWEp6SWpwYmV5SmpiR1ZoY2lJNklqQTNNakl3TURBd01EQWlMQ0p6ZEdGMGRYTWlPaUpoWTNScGRtVWlmU3g3SW1Oc1pXRnlJam9pTURjek16QXdNREF3TUNJc0luTjBZWFIxY3lJNkltbHVZV04wYVhabEluMWRmWDA9In0=';

//$data_obj = 'eyJHZXRDdXN0b21lckluZm8iOiJleUprWVhSaElqcDdJbWRsZEVOMWMzUnZiV1Z5U1c1bWJ5STZleUptYVhKemRFNWhiV1VpT2lKUVJWUkZVaUFpTENKc1lYTjBUbUZ0WlNJNklrdEJVbFZOUWtsRFNGVWlMQ0pwWkU1MWJXSmxjaUk2SWpFeE1EQTBNRGc0SW4xOWZRPT0iLCJRdWVyeU15TnVtYmVycyI6ImV5SmtZWFJoSWpwN0luRjFaWEo1VFhsT2RXMWlaWEp6SWpwYmV5SmpiR1ZoY2lJNklqQTNNREF3TURBd01EQWlMQ0p6ZEdGMGRYTWlPaUpoWTNScGRtVWlmU3g3SW1Oc1pXRnlJam9pTURjd01EQXdNREF3TUNJc0luTjBZWFIxY3lJNkltbHVZV04wYVhabEluMWRmWDA9In0=';

//$data_obj = 'eyJHZXRDdXN0b21lckluZm8iOiJleUprWVhSaElqcDdJbWRsZEVOMWMzUnZiV1Z5U1c1bWJ5STZleUptYVhKemRFNWhiV1VpT2lKQlRFVllJQ0lzSW14aGMzUk9ZVzFsSWpvaUlGZEJVazlTVlVFaUxDSnBaRTUxYldKbGNpSTZJak01TWprd09UYzBJbjE5ZlE9PSIsIlF1ZXJ5TXlOdW1iZXJzIjoiZXlKa1lYUmhJanA3SW5GMVpYSjVUWGxPZFcxaVpYSnpJanBiZXlKamJHVmhjaUk2SWpBM01EQXdNREF3TURBaUxDSnpkR0YwZFhNaU9pSmhZM1JwZG1VaWZTeDdJbU5zWldGeUlqb2lNRGN3TURBd01EQXdNQ0lzSW5OMFlYUjFjeUk2SW1sdVlXTjBhWFpsSW4xZGZYMD0ifQ==';


if (!$token) {
    echo "data: " . json_encode(['status' => 'error', 'message' => "Token not provided", 'endOfProcessing' => true]) . "\n\n";
    exit;
}

if (!$data_obj) {
    echo "data: " . json_encode(['status' => 'error', 'message' => "Data not provided", 'endOfProcessing' => true]) . "\n\n";
    exit;
}

function dataObjDec($data_obj)
{
    $obj = [];
    $apiResponse = base64_decode($data_obj);
    $responseData = json_decode($apiResponse, true);

    //sendSSEMessage('info', $apiResponse);

    $CustomerInfo = json_decode(base64_decode($responseData['GetCustomerInfo']), true);

    //sendSSEMessage('info', base64_decode($responseData['GetCustomerInfo']));

    $obj['firstName'] = $CustomerInfo['data']['getCustomerInfo']['firstName'];
    $obj['lastName'] = $CustomerInfo['data']['getCustomerInfo']['lastName'];
    $obj['idNumber'] = $CustomerInfo['data']['getCustomerInfo']['idNumber'];

    $MyNumbers = json_decode(base64_decode($responseData['QueryMyNumbers']), true);

    //sendSSEMessage('info', base64_decode($responseData['QueryMyNumbers']));

    $obj['myNumbers'] = [];
    foreach ($MyNumbers['data']['queryMyNumbers'] as $value) {
        $obj['myNumbers'][] = ['number' => $value['clear'], 'status' => $value['status']];
    }

    return $obj;
}

// Function to make external API calls
// Process decoded data object
$decodedData = dataObjDec($data_obj);

sendSSEMessage('info', json_encode($decodedData));

// Array to store results of all calls for the final summary
$finalResults = [];
$finalObj = [];

// List of structured API calls
$apiCalls = [
    'iprs' => [
        'url' => 'https://nairobiservices.go.ke/api/external/user/kra/id/' . $decodedData['idNumber'],
        'method' => 'GET',
        'body' => ['token' => $token]
    ],
    'kra_iprs' => [
        'url' => 'https://nairobiservices.go.ke/api/external/user/kra/pin/' . $decodedData['idNumber'],
        'method' => 'GET',
        'body' => ['token' => $token]
    ],
    'kra_api' => [
        'url' => 'https://api.kra.go.ke/m-service/user/verify',
        'method' => 'POST',
        'body' => [
            "pin" => $decodedData['idNumber'],
            "token" => "20e92a436d4bf28e8c08565df22ae2d6dd3d495709a43d0ce52e9ab2847d995b",
            "ishara" => "016086dc439441d36c739223bf356e676e8ff109a9ca885e915719fe4561af61",
            "version" => "3.0",
            "lugha" => "0"
        ]
    ],
    'kra_portal' => [
        'url' => 'https://itax.kra.go.ke/KRA-Portal/eTreAmendment.htm?actionCode=loadViewProfile&taxPayerPin=',
        'method' => 'POST',
        'body' => [
            'applicantType' => 'taxpayer',
            'cmbTaxpayerType' => 'INDI',
            'fieldsToSkip' => 'representativeName,taxPayerName',
            'representativeName' => '',
            'representativePin' => '',
            //'taxPayerName' => $fullname,
            'taxPayerPin' => '',
            'viewProfileFlag' => 'Y',
        ]
    ],
    // Add more calls as needed
];

// Function to send a message in real-time
function sendSSEMessage($status, $message, $endOfProcessing = false)
{
    $data = [
        'status' => $status,
        'message' => $message.' : '.date('d/m/Y H:i:s:u'),
        'endOfProcessing' => $endOfProcessing
    ];
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Function to make an API call
function makeAPICall($url, $method, $data = [], $headers = [])
{
    $ch = curl_init($url);
    $payload = json_encode($data);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json', 'Content-Length: ' . strlen($payload)], $headers));
    }

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function pingURL($url)
{
    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // Perform a "HEAD" request
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Request timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // Connection timeout
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate issues
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Execute request
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_errno($ch);
    $errorMessage = curl_error($ch);

    // Close cURL session
    curl_close($ch);

    // If there is a cURL error (network failure, DNS failure, no response)
    if ($error || !$httpCode) {
        sendSSEMessage('info', $url . ': Unreachable (' . ($errorMessage ?: 'No response') . ')');
        return false;
    }

    // The server responded, even if it's 404 or 405, so we consider it "up"
    return true;
}


function extractPIN($sentence)
{
    // Define the pattern to match the sentence format
    $pattern = '/^User\s([A-Z0-9]+)\sis\salready\sregistered\.$/';

    // Perform the regular expression match
    if (preg_match($pattern, $sentence, $matches)) {
        // If a match is found, return the dynamic word
        return $matches[1];
    } else {
        // If no match is found, return false
        return false;
    }
}

function kraBck($apiCalls)
{
    sendSSEMessage('info', "Testing Revenue Authority Query Processing 2...");
    $currentCall = $apiCalls['kra_api'];

    if (pingURL($currentCall['url'])) {
        $response = makeAPICall($currentCall['url'], $currentCall['method'], $currentCall['body'] ?? [], $currentCall['headers'] ?? []);
        $gt1 = json_decode($response, true);
        if (is_array($gt1)) {
            if (isset($gt1[0]['login'])) {
                foreach ($gt1[0] as $gtid => $gt1r) {
                    $object_1[$gtid] = $gt1r;
                }
                $brs_pin = $gt1[0]['login'];
                sendSSEMessage('success', "Account Found: " . $brs_pin);
            } elseif (isset($gt1['M-Service'])) {
                //$object_1['kra'] = 'KRA PIN Not available for Identity Provided!';
                $pin_extract = extractPIN($gt1['M-Service']);
                if ($pin_extract !== false) {
                    $brs_pin = $pin_extract;
                    sendSSEMessage('success', "Account Found: " . $brs_pin);
                } else {
                    sendSSEMessage('error', 'KRA Fetching error. Result: ' . $gt1['M-Service']);
                }
                sendSSEMessage('error', 'KRA Fetching error. Result: ' . $gt1['M-Service']);
            } else {
                //$object_1['kra'] = 'KRA PIN Not available for Identity Provided!';
                sendSSEMessage('error', 'KRA PIN Not available for Identity Provided!');
            }
        } else {
            sendSSEMessage('error', 'KRA PIN Not available for Identity Provided!');
        }
    } else {
        sendSSEMessage('error', "KRA Processing Engine 2: Server Inaccessible!");
    }


    if (isset($brs_pin)) {
        return $brs_pin;
    } else {
        return false;
    }
}

function pinData($apiCalls, $kra_pin)
{
    $currentCall = $apiCalls['kra_portal'];
    $url = $currentCall['url'] . $kra_pin;
    $body = [
        'applicantType' => 'taxpayer',
        'cmbTaxpayerType' => 'INDI',
        'fieldsToSkip' => 'representativeName,taxPayerName',
        'representativeName' => '',
        'representativePin' => '',
        //'taxPayerName' => $fullname,
        'taxPayerPin' => $kra_pin,
        'viewProfileFlag' => 'Y',
    ];
    if (pingURL($url)) {
        $response = makeAPICall($url, $currentCall['method'], $body ?? [], $currentCall['headers'] ?? []);
        //$object_101 = json_decode($response, true);
        sendSSEMessage('info', "Revenue Authority Query Processing...");
        $object_101 = scrape_2($response);
        $kraPortal = [];
        if (is_array($object_101)) {
            sendSSEMessage('success', "Revenue Information Processed...");
            foreach ($object_101 as $id => $object_102) {
                $kraPortal[$id] = $object_102;
            }
        } else {
            sendSSEMessage('error', "Revenue Information Processing Error...");
        }
        sendSSEMessage('info', "Revenue Information Pulling Done");
    } else {
        sendSSEMessage('error', "Revenue Information Server Offline...");
    }

    if (isset($kraPortal)) {
        return $kraPortal;
    } else {
        return false;
    }
}
// Process API calls in sequence, based on previous responses
/*
$nextCall = 'call1';
while ($nextCall) {
    $currentCall = $apiCalls[$nextCall];
    $response = makeAPICall($currentCall['url'], $currentCall['method'], $currentCall['body'] ?? [], $currentCall['headers'] ?? []);

    // Track call status in final results
    $finalResults[$nextCall] = [
        'status' => $response['status'] ?? 'unknown',
        'message' => $response['message'] ?? 'No message',
    ];

    // Send each message as soon as it's ready
    sendSSEMessage($response['status'] ?? 'info', "Processing {$nextCall}: " . ($response['message'] ?? 'No message'));

    // Determine the next call based on response
    $nextCall = $response['nextCall'] ?? null;
}
//*/
$finalResults = [];
if (isset($apiCalls)) {
    sendSSEMessage('info', "Starting APIs call sequence...");
    //// STEP A
    $currentCall = $apiCalls['iprs'];
    if (pingURL($currentCall['url'])) {
        //sendSSEMessage('info', "Test 1");
        $response = makeAPICall($currentCall['url'], $currentCall['method'], $currentCall['body'] ?? [], $currentCall['headers'] ?? []);
        //sendSSEMessage('info', "Test 2");
        //sendSSEMessage('info', $response);
        $iprs = json_decode($response, true);

        if (isset($iprs['nid_no'])) {
            $finalObj['iprs'] = $iprs;
            sendSSEMessage('success', "Tunnel 001 response clear...");
        } else {
            sendSSEMessage('warning',
                "Tunnel 001 response null..."
            );
            sendSSEMessage('info', "Opening Tunnel 002...");
            $pinNo1 = kraBck($apiCalls);
            if (kraBck($apiCalls) !== false) {
                sendSSEMessage('success', "Tunnel 002 Patch 1 response clear...");
                $iprs_t2 = json_decode(httpGet('https://nairobiservices.go.ke/api/external/user/kra/real/pin/' . $pinNo1, []), true);
                if (isset($iprs_t2['nid_no'])) {
                    sendSSEMessage('success', "Tunnel 001 response clear...");
                    $iprs = $iprs_t2;
                    $finalObj['iprs'] = $iprs;
                } else {
                    sendSSEMessage('error', "Tunnel 002 response empty...");
                }
            } else {
                sendSSEMessage('error', "Tunnel 002 response null...");
            }
        }

        $serial_no = json_decode(httpGet('https://nairobiservices.go.ke/api/external/user/iprs/real/'.$decodedData['idNumber'],[]),true);
        
        if(isset($serial_no['serial_number'])){
            $finalObj['iprs']['serial_number'] = $serial_no['serial_number'];
        }else{
            $finalObj['iprs']['serial_number'] = 'N/A';
        }
        
        //$finalResults['iprs'] = [];
        foreach ($iprs as $key => $row) {
            //$finalResults['iprs'] = $row[''];
            $key = str_replace("_", "", $key);
            $finalResults[$key] = ['message' => $row, 'status' => 'success'];
            //array_push($finalResults['iprs'],['message'=>$key , 'status'=>$row]);
        }

        sendSSEMessage('info', "Revenue Authority Query Processing...");
        if (isset($iprs['pin_no'])) {
            sendSSEMessage('success', "Account Found: " . $iprs['pin_no']);
        } else {
            sendSSEMessage('warning', "KRA unavailable in Engine 1...");
            sendSSEMessage('info', "Launching Engine 2...");
            $brs_pin = kraBck($apiCalls);
        }
        sendSSEMessage('info', "Revenue Authority Query Done");
    } else {
        sendSSEMessage('error', "KRA Processing Engine 1: Server Inaccessible!");
        sendSSEMessage('info', "Launching Engine 2...");
        $brs_pin = kraBck($apiCalls);
        if (!$brs_pin) {
            sendSSEMessage("error", "-|-|::Engines Offline");
            exit;
        }
    }


    ///// B
    if (isset($iprs['pin_no'])) {
        $kra_pin = $iprs['pin_no'];
    } else {
        $kra_pin = $brs_pin;
    }
    $kraPortal = pinData($apiCalls, $kra_pin);
    $finalObj['kraPortal'] = $kraPortal;
    foreach ($kraPortal as $key => $row) {
        $key = str_replace("_", "", $key);
        $finalResults[$key] = ['message' => $row, 'status' => 'success'];
    }

    ////// C 
    if (isset($decodedData['idNumber'])) {
        $nhif_dt = json_decode(FetchNHIFData($decodedData['idNumber']), true);
        $finalObj['nhif_dt'] = $nhif_dt;
        if (is_array($nhif_dt)) {
            if (isset($nhif_dt['status_code'])) {
                if ($nhif_dt['status_code'] == '1000') {
                    foreach ($nhif_dt['data'] as $key => $row) {
                        $key = str_replace("_", "", $key);
                        $finalResults[$key . '_nhif'] = ['message' => $row, 'status' => 'success'];
                    }
                } elseif ($nhif_dt['status_code'] == '1002') {
                    //$object_1['nhif_status']  = '<div class="font-monospace text-primary">' . $firstname . ' IS NOT REGISTERED FOR NHIF!</div>';
                    sendSSEMessage('error', $firstname . '\'s employer information unavailable! Key 1');
                }
            } else {
                sendSSEMessage('error', $firstname . '\'s employer information unavailable! Key 2');
            }
        }
    }

    ////// D
    if (isset($decodedData['idNumber'])) {
        //$dldt =  DLFetch($decodedData['idNumber']);
        $dldt = httpGet('https://serviceportal.ntsa.go.ke/api/i/v1/verify/driving-license?id_number=' . $decodedData['idNumber'] . '&id_type=citizen', [], ['access-token: ' . generateAccessToken(), 'User-Agent: Dart/3.4 (dart:io)']);
        $dldt_1 = json_decode($dldt, true);
        $finalObj['dldt_1'] = $dldt_1;
        if (is_array($dldt_1)) {
            if (isset($dldt_1['data'])) {
                foreach ($dldt_1['data'] as $key => $row) {
                    $key = str_replace("_", "", $key);
                    $finalResults[$key . '_ntsa'] = ['message' => $row, 'status' => 'success'];
                }
            } elseif (isset($dldt_1['error'])) {
                if (isset($dldt_1['error']['status'])) {
                    if ($dldt_1['error']['status'] == 'Not Found') {
                        //$object_1['error_ntsa']  = 'User does not have a Driving Licence!';
                        sendSSEMessage('error', 'User does not have a Driving Licence!');
                    } else {
                        //$object_1['error_ntsa']  = $dldt_1['error']['status'];
                        sendSSEMessage('error', $dldt_1['error']['status']);
                    }
                } else {
                    //$object_1['error_ntsa']  = 'DL Error: ' . json_encode($dldt_1);
                    sendSSEMessage('error', 'DL Error: ' . json_encode($dldt_1));
                }
            } else {
                //$object_1['error_ntsa']  = 'DL Error: ' . json_encode($dldt_1);
                sendSSEMessage('error', 'DL Error: ' . json_encode($dldt_1));
            }
        }
    }

    ////// E
    sendSSEMessage('info', '::::Motor Vehicle Data Querying Service...');
    $fleetId = $decodedData['idNumber'];
    //$fleetId = '4838593';

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://kever.io/finder_15.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('idNumber' => $fleetId),
      CURLOPT_HTTPHEADER => array(
        'Cookie: PHPSESSID=7d8j381hsqv050c9ai6i4of0aq; authToken='.$token.'; visitorId=973ad0dd0c565ca2ae839d5ebef8447a'
      ),
    ));
    
    $fleetDt = curl_exec($curl);
    
    curl_close($curl);

    $dt1 = json_decode($fleetDt, true);
    $finalObj['vehicleAssets'] = $dt1;
    if (isset($dt1['assets'])) {
        if (count($dt1['assets']) > 0) {
            foreach ($dt1['assets'] as $key) {

                if(!is_array($key)){
                    //$key = str_replace("_", "", $key);
                }
                
                $recHold = bin2hex(random_bytes(4));

                $finalResults['plate_' . $recHold] = ['message' => $key['vehicle_no'], 'status' => 'info'];
                $finalResults['vehicle_model_' . $recHold] = ['message' => $key['vehicle_model'], 'status' => 'info'];

                if (isset($key['mechanical_data']['ChassisNo'])) {
                    $finalResults['chassis_' . $recHold] = ['message' => $key['mechanical_data']['ChassisNo'], 'status' => 'info'];
                }

                if (isset($key['mechanical_data']['carModel'])) {
                    $finalResults['carModel_' . $recHold] = ['message' => $key['mechanical_data']['carModel'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['bodyType'])) {
                    $finalResults['bodyType_' . $recHold] = ['message' => $key['mechanical_data']['bodyType'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['registrationDate'])) {
                    $finalResults['registrationDate_' . $recHold] = ['message' => $key['mechanical_data']['registrationDate'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['engineCapacity'])) {
                    $finalResults['engineCapacity_' . $recHold] = ['message' => $key['mechanical_data']['engineCapacity'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['bodyColor'])) {
                    $finalResults['bodyColor_' . $recHold] = ['message' => $key['mechanical_data']['bodyColor'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['engineNumber'])) {
                    $finalResults['engineNumber_' . $recHold] = ['message' => $key['mechanical_data']['engineNumber'], 'status' => 'info'];
                }
                if (isset($key['mechanical_data']['ChassisNo'])) {
                    $finalResults['chassis_' . $recHold] = ['message' => $key['mechanical_data']['ChassisNo'], 'status' => 'info'];
                }

                if (isset($key['mechanical_data']['ntsa_id'])) {
                    $finalResults['ntsa_id_' . $recHold] = ['message' => $key['mechanical_data']['ntsa_id'], 'status' => 'info'];
                }

                
                

                //$finalResults[$key. '_ntsa'] = ['message' => $row, 'status' => 'success'];
                //$finalResults[$key. '_ntsa'] = ['message' => $row, 'status' => 'success'];
            }
        } else {
            sendSSEMessage('warning', 'User Vehicle Data Error: No data found!');
        }
    } else {
        sendSSEMessage('error', 'Error querying fleet data!');
        sendSSEMessage('error', 'API Response: '.json_encode($fleetDt));
    }

    ////// F
    //step1
    sendSSEMessage('info', '::::Nairobi Resident Query Engine Started ...');
    $url = 'https://nairobiservices.go.ke/api/authentication/auth/get_user_details/?id_number=' . $decodedData['idNumber'];
    $dt1 = json_decode(httpGet($url, []), true);
    $finalObj['nrsData'] = $dt1;
    if (isset($dt1['data'])) {
        if ($dt1['data']['id_number'] ==  $decodedData['idNumber']) {
            $finalResults['nrs_customer_id'] = ['message' => $dt1['data']['customer_id'], 'status' => 'info'];
            $nrs_customer_id = $dt1['data']['customer_id'];
            foreach ($dt1['data'] as $key => $row) {
                $key = str_replace("_", "", $key);
                $finalResults['nrs_' . $key] = ['message' => $row, 'status' => 'success'];
            }
        } else {
            sendSSEMessage('warning', '-|-\/-|-Nairobi System Malfunction: Report issue to admin! ');
        }
    } elseif (isset($dt1['error'])) {
        sendSSEMessage('error', 'Nairobi Error: ' . $dt1['error']);
    } else {
        sendSSEMessage('error', '::::Nairobi records unavailable!');
    }
    //step 2
    //$nrs_customer_id = '2020_276753';
    sendSSEMessage('info', '::::Generating NRS Token ...');
    if (isset($nrs_customer_id)) {
        $url = 'https://nairobiservices.go.ke/api/authentication/auth/generate_customer_token';
        $data = ['customer_no' => $nrs_customer_id];
        $dt1 = json_decode(httpGet($url, $data), true);
        if (is_array($dt1)) {
            if (isset($dt1['token'])) {
                //$_SESSION['token'] = $dt1['token'];
                $nrs_token = $dt1['token'];
                //$_SESSION['phone_number'] = $dt1['phone_number'];
                //$_SESSION['is_psv'] = $dt1['is_psv'];

                sendSSEMessage('success', '::::NRS Token set successfully');
                sendSSEMessage('info', $dt1['token']);
            } else {
                if (isset($dt1['error'])) {
                    sendSSEMessage('error', '::::NRS Error: ' . $dt1['error']);
                } else {
                    sendSSEMessage('error', '::::NRS Token not set! Unknown error!');
                }
            }
        } else {
            sendSSEMessage('error', '::::Error Quering NRS records!');
        }
    } else {
        sendSSEMessage('error', '::::NRS Customer ID needed to process token!');
    }
    //step 3
    sendSSEMessage('info', '::::Searching NRS Businesses ...');
    if (isset($nrs_token)) {
        $url = 'https://edev.nairobiservices.go.ke/api/sbp/ubp/get_ubp_register';
        $dt1 = json_decode(httpGet($url, [], ['Authorization: Bearer ' . $nrs_token]), true);
        $finalObj['nrsUbpData'] = $dt1;
        if (isset($dt1['success'])) {
            if ($dt1['success']) {
                if (isset($dt1['UBP_Register'])) {
                    if (count($dt1['UBP_Register']) > 0) {
                        sendSSEMessage('success', count($dt1['UBP_Register']) . ' NRS Businesses found!-----');
                        foreach ($dt1['UBP_Register'] as $key => $row) {
                            $key = str_replace("_", "", $key);
                            foreach($row as $key1=>$row1){
                                if(!is_array($row1)){
                                   $finalResults[$key . '_bus_nrs_' . $key1] = ['message' => $row1, 'status' => 'success']; 
                                }
                            }
                        }
                    } else {
                        sendSSEMessage('warning', ':::::No NRS Businesses found! P1-----');
                    }
                } else {
                    sendSSEMessage('warning', ':::::NRS Business Record Retrieval Error!-----');
                }
            } else {
                sendSSEMessage('warning', ':::::No NRS Businesses found! P2-----');
            }
        } else {
            if (isset($dt1['error'])) {
                sendSSEMessage('error', 'NRS error: ' . $dt1['error']);
            } else {
                sendSSEMessage('warning', 'NRS Systems Error: Alert Admin-----');
            }
        }
    } else {
        sendSSEMessage('error', '::::NRS Token not generated!');
    }
    sendSSEMessage('success', '::::NRS Business search Done ...');

    ////////// G
    
    


    $finalResults['kra'] = ['message' => $kra_pin, 'status' => 'success'];
}

// Send final results as a summary once all calls are processed
sendSSEMessage('info', "Preparing final summary...", false);

// Prepare a final structured summary of all results
$finalSummary = "<table style='border-collapse: collapse; width: 100%; color: #00c853; background-color: #1b1f23;'>";
$finalSummary .= "<tr style='border-bottom: 1px solid #444;'><th>Call</th><th>Status</th><th>Message</th></tr>";
foreach ($finalResults as $callName => $result) {
    $finalSummary .= "<tr style='border-bottom: 1px solid #333;'>";
    $finalSummary .= "<td>{$callName}</td><td>{$result['status']}</td><td>{$result['message']}</td>";
    $finalSummary .= "</tr>";
}
$finalSummary .= "</table>";

// Send the final summary in a cybersecurity-themed format
sendSSEMessage('info', $finalSummary, false);

sendSSEMessage('info', '::::Generating Data Disc ...');

$finalObjData = base64_encode(json_encode($finalObj));

$filePath = './temp/'.$decodedData['idNumber'].'-'.time().'-'.bin2hex(random_bytes(10)).'.pkestrel'; // Your custom file type

createCustomFile($filePath, $finalObjData);

$search = saveSearch($_SESSION['user_id'], $decodedData['idNumber'], $filePath);
if($search == true){
    sendSSEMessage('success', 'Search Logged Successfully!');
}else{
    sendSSEMessage('error', $search);
}

sendSSEMessage('success', '::::Data Disc Created...');

//echo "Custom file created: $filePath\n";

sendSSEMessage('info', '<a href="'.$filePath.'" data-target="_blank" class="btn btn-success" download>Download Data Disc</a>', true);
exit;

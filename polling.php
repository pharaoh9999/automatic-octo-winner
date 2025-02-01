<?php
function login($username, $password)
{
    // Step 1: Authenticate username and password with the API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://kever.io/finder_10_auth.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'username' => $username,
        'password' => $password,
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, "visitorId=973ad0dd0c565ca2ae839d5ebef8447a");

    $response = curl_exec($ch);
    $apiResponse = json_decode($response, true);
    curl_close($ch);

    return $apiResponse;
}


if (!isset($_SESSION['token'])) {
    $token = login('kever', '24051786');
    $sesToken = $token['token'];
} else {
    $sesToken = $_SESSION['token'];
}
$dt = [];

// Check if 'parsekey' is provided in the URL
if (isset($_GET['parsekey'])) {
    $parseKey = $_GET['parsekey'];
} else {
    $dt['status'] = false;
    $dt['message'] = 'Missing parsekey parameter';
    echo json_encode($dt, JSON_PRETTY_PRINT);
    exit; // Exit if parsekey is not provided
}

// Initialize cURL
$curl = curl_init();

// Set up cURL options
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://kever.io/finder_13.php?parsekey=' . urlencode($parseKey),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Cookie: PHPSESSID=3ads4kng4q2l4g0rlbrfr3kca1; authToken=' . $sesToken . '; visitorId=973ad0dd0c565ca2ae839d5ebef8447a'
    ),
));

// Execute the cURL request and handle errors
$response = curl_exec($curl);

if ($response === false) {
    $dt['status'] = false;
    $dt['message'] = 'cURL error: ' . curl_error($curl);
    echo json_encode($dt, JSON_PRETTY_PRINT);
} else {
    //$db = 
    echo $response; // Output the API response if successful
}

curl_close($curl);

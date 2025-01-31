<?php
class CookieManager
{
    private $cookies = [];

    // Update cookies from a response header
    public function updateCookies($newCookies)
    {
        // Parse the new cookies string and update the internal cookies array
        $cookiePairs = array_filter(array_map('trim', explode(';', $newCookies)));
        foreach ($cookiePairs as $cookie) {
            list($key, $value) = explode('=', $cookie, 2);
            $this->cookies[$key] = $value;
        }
    }

    // Get cookies as a single string for the cURL request
    public function getCookieString()
    {
        $cookieArray = [];
        foreach ($this->cookies as $key => $value) {
            $cookieArray[] = "$key=$value";
        }
        return implode('; ', $cookieArray);
    }
}

// Define the function for making a single request
function makeCurlRequest($url, $headers = [], $cookieManager = null, $method = 'GET', $body = null)
{
    $ch = curl_init();

    // Set the URL for the request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in output to capture cookies

    // Use cookies from the CookieManager if available
    if ($cookieManager && !empty($cookieManager->getCookieString())) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookieManager->getCookieString());
    }

    // Set headers, if any
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    // Handle POST request with body
    if (strtoupper($method) === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
    }

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Separate headers and body
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    // Extract cookies from headers and update CookieManager
    if ($cookieManager) {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
        $newCookies = implode('; ', $matches[1]);
        $cookieManager->updateCookies($newCookies);
    }

    curl_close($ch);

    // Return the response body
    return $body;
}


// Initialize the CookieManager instance
$cookieManager = new CookieManager();

/*
// Define the URLs for each request
$url1 = "https://httpbin.org/cookies/set?cookie1=value1";
$url2 = "https://httpbin.org/cookies/set?cookie2=value2";
$url3 = "https://httpbin.org/cookies/set?cookie2=new_value";
$url4 = "https://httpbin.org/cookies";

// Request 1
$response1 = makeCurlRequest($url1, $headers, $cookieManager, 'GET');
echo "Response 1 Body: " . $response1 . "<br/><br/><br/>";

// Request 2 (automatically uses cookies set in Request 1)
$response2 = makeCurlRequest($url2, $headers, $cookieManager, 'GET');
echo "Response 2 Body: " . $response2 . "<br/><br/><br/>";
*/
$finalResponse = [];

$headers5 = [
    'Accept: application/json, text/plain, */*',
    'Content-Type: application/json',
    'DNT: 1',
    'sec-ch-ua-mobile: ?0',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    'sec-ch-ua-platform: "Windows"',
    'Sec-Fetch-Site: same-site',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Dest: empty',
    'Host: identity.safaricom.com',
];
$url5 = "https://identity.safaricom.com/graphql?grant_type=client_credentials";
$body5 = '{"query":"\n            query GenerateToken{\n                generateToken{\n                  status\n                  message\n                  token \n                }\n            }\n           "}';
$response5 = makeCurlRequest($url5, $headers5, $cookieManager, 'POST', $body5);
echo "Response 5 Body: " . $response5 . "<br/><br/><br/>";
$response5 = json_decode($response5, true);
if (!isset($response5['data']['generateToken']['token'])) {
    echo 'Error processing request!';
    die();
}
$finalResponse['token'] = base64_encode($response5['data']['generateToken']['token']);

$headersPri = [
    'Accept: application/json, text/plain, */*',
    'Content-Type: application/json',
    'DNT: 1',
    'sec-ch-ua-mobile: ?0',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    'sec-ch-ua-platform: "Windows"',
    'Sec-Fetch-Site: same-site',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Dest: empty',
    'Host: myaccount.safaricom.co.ke',
];
$urlPri = "https://myaccount.safaricom.co.ke/graphql";


$body_1 = '{"operationName":"GetFeatureStatus","variables":{"featureName":"dealerKyc"},"query":"query GetFeatureStatus($featureName: String!) {\n  getFeatureStatus(featureName: $featureName) {\n    status\n    message\n    __typename\n  }\n}"}';
$response_1 = makeCurlRequest($urlPri, $headersPri, $cookieManager, 'POST', $body_1);
//echo "Response _1 Body: " . $response_1 . "<br/><br/><br/>";


$body_2 = '{"operationName":"CheckRegistrationStatus","variables":{},"query":"query CheckRegistrationStatus {\n  checkRegistrationStatus {\n    status\n    message\n    __typename\n  }\n}"}';
$response_2 = makeCurlRequest($urlPri, $headersPri, $cookieManager, 'POST', $body_2);
//echo "Response _2 Body: " . $response_2 . "<br/><br/><br/>";

array_push($headersPri, 'Hetoken: ' . $response5['data']['generateToken']['token']);

$body_3 = '{"operationName":"GetCustomerInfo","variables":{},"query":"query GetCustomerInfo {\n  getCustomerInfo {\n    status\n    message\n    customerType\n    firstName\n    lastName\n    idNumber\n    blazer\n    blazeTariff\n    tariff\n    blazerId\n    __typename\n  }\n}"}';
$response_3 = makeCurlRequest($urlPri, $headersPri, $cookieManager, 'POST', $body_3);
//echo "Response _3 Body: " . $response_3 . "<br/><br/><br/>";
$finalResponse['GetCustomerInfo'] = base64_encode($response_3);

$body_4 = '{"operationName":"QueryMyNumbers","variables":{},"query":"query QueryMyNumbers {\n  queryMyNumbers {\n    clear\n    masked\n    status\n    __typename\n  }\n}"}';
$response_4 = makeCurlRequest($urlPri, $headersPri, $cookieManager, 'POST', $body_4);
//echo "Response _4 Body: " . $response_4 . "<br/><br/><br/>";
$finalResponse['QueryMyNumbers'] = base64_encode($response_4);

$body_5 = '{"operationName":"QueryPrimaryMsisdnReporting","variables":{},"query":"query QueryPrimaryMsisdnReporting {\n  queryIfPrimaryNumberReporting {\n    status\n    message\n    formattedMobile\n    primaryMsisdn\n    __typename\n  }\n}"}';
$response_5 = makeCurlRequest($urlPri, $headersPri, $cookieManager, 'POST', $body_5);
//echo "Response _5 Body: " . $response_5 . "<br/><br/><br/>";
$finalResponse['QueryPrimaryMsisdnReporting'] = base64_encode($response_5);

$outputData = base64_encode(json_encode($finalResponse));
echo $outputData;

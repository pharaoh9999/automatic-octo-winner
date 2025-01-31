<?php
session_start();
header("Content-Type: application/json");

$response = [];

// Check if a token was sent in the request
$input = json_decode(file_get_contents("php://input"), true);
//$input['token'] = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7Im1zaXNkbiI6IjI1NDcxNjkxMjAwMiIsImNsaWVudElwIjoiMTk2LjIwMS4yMTguMTI2Iiwib3MiOiJXaW5kb3dzIDEwLjAiLCJzb3VyY2UiOiJNb3ppbGxhLzUuMCAoV2luZG93cyBOVCAxMC4wOyBXaW42NDsgeDY0KSBBcHBsZVdlYktpdC81MzcuMzYgKEtIVE1MLCBsaWtlIEdlY2tvKSBDaHJvbWUvMTE0LjAuMC4wIFNhZmFyaS81MzcuMzYiLCJwbGF0Zm9ybSI6Ik1pY3Jvc29mdCBXaW5kb3dzIiwiaXNNb2JpbGUiOmZhbHNlLCJpc1RhYmxldCI6ZmFsc2UsImlzRGVza3RvcCI6dHJ1ZSwiaXNJcG9kIjpmYWxzZSwiaXNJcGhvbmUiOmZhbHNlLCJpc0FuZHJvaWQiOmZhbHNlLCJpc0JsYWNrYmVycnkiOmZhbHNlLCJpc09wZXJhIjpmYWxzZSwiaXNJRSI6ZmFsc2UsImlzRWRnZSI6ZmFsc2UsImlzU2FmYXJpIjpmYWxzZSwiaXNGaXJlZm94IjpmYWxzZX0sImlhdCI6MTczMTA2NzQxMiwiZXhwIjoxNzMxMjQwMjEyfQ.QhfeAusJXhYEXNjHb_sJkZQAWKxya78phS7JIphnWcs';
if (isset($input['token']) && !empty($input['token'])) {
    $data = $input['token'];

    // Here you might validate the token further if needed.
    // For example, match it against a known value or check for a certain format.
    if ($data === $data) {  // Replace with actual validation logic if needed
        // Process the token (e.g., save to database, send to another API, etc.)

        $apiResponse = base64_decode($data);

        //echo $apiResponse;

        // Check if the API responded with success
        if ($apiResponse) {
            $responseData = json_decode($apiResponse, true);
            if (isset($responseData['GetCustomerInfo'])) {
                $resData = json_decode(base64_decode($responseData['GetCustomerInfo']), true);
                if (isset($resData['data']['getCustomerInfo']['status'])) {
                    if ($resData['data']['getCustomerInfo']['status'] === true) {
                        $response['status'] = 'success';
                        $response['message'] = 'Account processed successfully:::'.$resData['data']['getCustomerInfo']['firstName'].' '.$resData['data']['getCustomerInfo']['lastName'];
                        $response['data'] = $data;
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Processing endpoint returned an error. Part 1';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Processing endpoint returned an error ~ Outbound Network'; 
                }
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to process the token with the endpoint.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid token provided.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No token provided in the request.';
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);

<?php
// Start session if needed
session_start();

// Set headers for SSE
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

// Flush headers immediately
ob_implicit_flush(true);
ob_end_flush();

function sendSSEMessage($status, $message, $endOfProcessing = false) {
    $data = [
        'status' => $status,
        'message' => $message,
        'endOfProcessing' => $endOfProcessing
    ];
    echo "data: " . json_encode($data) . "\n\n";
    flush();
}

// Simulate sending a series of API call results
$testMessages = [
    ["status" => "info", "message" => "Initializing SSE Test..."],
    ["status" => "info", "message" => "Test API Call 1 - Fetching data..."],
    ["status" => "success", "message" => "Test API Call 1 completed successfully."],
    ["status" => "info", "message" => "Test API Call 2 - Processing..."],
    ["status" => "error", "message" => "Test API Call 2 encountered an error. Retrying..."],
    ["status" => "success", "message" => "Test API Call 2 retry successful."],
    ["status" => "success", "message" => "SSE Test completed."]
];

// Send each test message as an SSE message
foreach ($testMessages as $msg) {
    sendSSEMessage($msg['status'], $msg['message']);
    sleep(2); // Simulate delay between each message
}

// Signal end of processing
sendSSEMessage("success", "All test API calls have completed.", true);

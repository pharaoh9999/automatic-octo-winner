<?php
session_start();
include 'config.php';

require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;

$loadUrl = "https://kever.io/1sfxmrptepzcngxjgmb.php";
$reportUrl = "https://kever.io/1sfxmrptepzcngxjgmb.php";
$parseKey = bin2hex(random_bytes(16));

$dataString = $loadUrl . "," . $reportUrl . "," . $parseKey;
$base64EncodedString = base64_encode($dataString);

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($base64EncodedString)
    ->encoding(new Encoding('UTF-8'))
    ->size(300)
    ->margin(10)
    ->backgroundColor(new Color(255, 255, 255))
    ->foregroundColor(new Color(0, 0, 0))
    ->build();

$qrCodeDataUri = $result->getDataUri();
?>

<!DOCTYPE html>
<html lang="en">

<?php include './includes/head.php' ?>


<body>
    <div class="container">
        <?php include './includes/navbar.php' ?>
        <div class="row">

            <!-- Main Content Area -->
            <div class="col-md-12">
                <div class="main-content">
                    <h1 class="h2 pb-2 mb-4 text-success border-bottom border-success">QR Code Processing</h1>

                    <div class="card">
                        <div class="card-body">
                            <p>Scan the QR code below with the special app.</p>
                            <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code" class="mb-4 img-fluid">
                        </div>
                    </div>


                    <!-- Polling Results Display -->
                    <div id="pollResults">
                        <h4>Polling Results</h4>
                        <p id="pollOutput">Polling in progress...</p>
                    </div>

                    <!-- Terminal Display for API Responses -->
                    <div class="terminal" id="terminal">
                        <div id="loader" class="loader"></div>
                        <div id="consoleOutput">Terminal awaiting data...</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

<script>
    const parseKey = "<?php echo $parseKey; ?>";
    const apiUrl = `./polling.php?parsekey=${parseKey}`;
    const pollingInterval = 5000;
    const timeoutDuration = 2 * 60 * 1000;
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    let pollCount = 0;
    let timeoutId;

    // Display polling results in a separate area
    function displayPollingResult(text) {
        pollCount++;
        const pollOutput = document.getElementById("pollOutput");
        pollOutput.innerHTML = `<strong>Step #${pollCount}:</strong> ${text}`;
    }

    function showLoader() {
        document.getElementById("loader").style.display = "block";
    }

    function hideLoader() {
        document.getElementById("loader").style.display = "none";
    }

    // Display messages in the terminal with specific types
    function displayConsoleMessage(text, type = "info") {
        const consoleOutput = document.getElementById("consoleOutput");

        const messageElement = document.createElement("div");
        messageElement.classList.add("console-message");

        switch (type) {
            case "success":
                messageElement.classList.add("message-success");
                break;
            case "error":
                messageElement.classList.add("message-error");
                break;
            case "warning":
                messageElement.classList.add("message-warning");
                break;
            default:
                messageElement.classList.add("message-info");
        }

        messageElement.innerHTML = text;
        consoleOutput.appendChild(messageElement);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }

    function initiateAPIEventStream(token) {
        showLoader();
        const eventSource = new EventSource(`./api_manager.php?data=${encodeURIComponent(token)}`);

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            displayConsoleMessage(data.message, data.status);

            if (data.endOfProcessing) {
                eventSource.close();
                hideLoader();
                displayConsoleMessage("All API calls have completed.", "success");
            }
        };

        eventSource.onerror = function() {
            displayConsoleMessage("An error occurred while receiving updates.", "error");
            hideLoader();
            eventSource.close();
        };
    }

    function pollAPI() {
        fetch(apiUrl, {
                method: "GET",
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.status && data.key === parseKey) {
                    clearTimeout(timeoutId);
                    displayPollingResult("Success response received! Processing data...");
                    processToken(data.token);
                } else {
                    displayPollingResult(`Status: ${data.status}, Key: ${data.key}`);
                    setTimeout(pollAPI, pollingInterval);
                }
            })
            .catch(error => {
                displayPollingResult(`Error: ${error}`);
            });
    }

    function processToken(token) {
        showLoader();
        fetch("./pointer_a.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    token: token
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoader();
                if (data.status === "success") {
                    displayConsoleMessage(data.message, "success");
                    initiateAPIEventStream(data.data);
                } else {
                    displayConsoleMessage(`Failed to process token: ${data.message}`, "error");
                }
            })
            .catch(error => {
                hideLoader();
                displayConsoleMessage(`Error processing token: ${error}`, "error");
            });
    }

    timeoutId = setTimeout(() => {
        displayPollingResult("Timeout: No response received within 2 minutes.");
    }, timeoutDuration);

    if (urlParams.has('kestrelToken')) {
        // Retrieve the value of the parameter
        const paramValue = urlParams.get('kestrelToken');
        initiateAPIEventStream(paramValue);
    } else {
        pollAPI();
    }
</script>

</html>
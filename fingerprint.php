<?php
//session_start();
$TokenVerificationExeception = true;
require './includes/config.php';
require './includes/function.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;

// Redirect if already authenticated
// if (isset($_COOKIE['auth_token']) && verify_access($_SERVER['PHP_SELF'])) {
//     header("Location: ./login.php");
//     exit;
// }

if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

$error = $_SERVER['HTTP_COOKIE'];
//EvE5pHQx+3rt6lejXQRsJEwrMy9GOE9WZXo5MDN4THhaNXRQanc9PQ==
//2Hu2PYI9QM3RMkn+Vuk2WzZGdW9DbXdVRVBlU2JtV3hLVUh2Umc9PQ==

//QR Code Processing
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

// Generate device fingerprint hash
$currentDeviceHash = generate_device_hash();

// Handle 2FA Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    // Verify 2FA code (pseudo-code)
    $google2fa = new GoogleAuthenticator();

    if (isset($_POST['uniqueKey'])) {
        if ($_POST['uniqueKey'] !== '' || !empty($_POST['uniqueKey'])) {
            $systemKey = $_POST['uniqueKey'];
            $authKey = decrypt_token($systemKey);
            $stmt = $conn->prepare("SELECT * FROM users WHERE systemKey = :systemKey");
            $stmt->execute(['systemKey' => $authKey]);
            $user = $stmt->fetch();
            if (isset($user['username'])) {
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['user_id'] = $user['id'];
                if ($google2fa->checkCode($_SESSION['temp_secret'], $_POST['code'])) {
                    // Generate security file
                    //$systemKey = bin2hex(random_bytes(32));
                    $userPassword = $_POST['password']; // From login
                    $username = $_SESSION['user_name'] = $user['username'];
                    if (password_verify($userPassword, $user['password'])) {
                        $apiResponse = login($username, $userPassword);
                        if (isset($apiResponse['token'])) {
                            $_SESSION['temp_secret'] = base64_decode($apiResponse['ga_secret']);


                            // Layer 1: System Key Encryption
                            $layer1 = openssl_encrypt(
                                $authKey,
                                'aes-256-cbc',
                                $authKey,
                                0,
                                str_repeat('0', 16)
                            );

                            // Layer 2: User Password Encryption
                            $layer2 = openssl_encrypt(
                                $layer1,
                                'aes-256-cbc',
                                hash('sha256', $userPassword),
                                0,
                                str_repeat('0', 16)
                            );

                            // Store system key reference
                            $stmt = $conn->prepare("REPLACE INTO security_files  (user_id, device_hash, layer1_key) VALUES (?, ?, ?)");
                            $stmt->execute([
                                $_SESSION['user_id'],
                                $currentDeviceHash,
                                $systemKey
                            ]);

                            // Output security file
                            header('Content-Type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="' . $user['username'] . '_security.safetoken"');
                            echo $layer2;
                            exit;
                        } else {
                            //echo json_encode(['success' => false, 'redirect' => 'User not authorized for setup!']);
                            $error = 'User not authorized for setup!';
                            //exit;
                        }
                    } else {
                        $error = 'Verification Key mismatch!';
                    }
                } else {
                    $error = "Invalid 2FA code";
                }
            } else {
                $error = 'User account not authorized for clearance!';
            }
        } else {
            $error = 'System Unique Key Not Generated!';
        }
    } else {
        $error = 'System Unique Key Unavailable!';
    }
}

// Handle File Upload Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['security_file'])) {
    $fileContent = file_get_contents($_FILES['security_file']['tmp_name']);
    $userPassword = $_POST['password'];

    if (isset($_POST['uniqueKey'])) {
        if ($_POST['uniqueKey'] !== '' || !empty($_POST['uniqueKey'])) {
            $systemKey = $_POST['uniqueKey'];
            $authKey = decrypt_token($systemKey);
            $stmt = $conn->prepare("SELECT * FROM users WHERE systemKey = :systemKey");
            $stmt->execute(['systemKey' => $authKey]);
            $user = $stmt->fetch();
            if (isset($user['username'])) {
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['user_id'] = $user['id'];
                try {
                    // Layer 2 Decryption (User Password)
                    $layer1 = openssl_decrypt(
                        $fileContent,
                        'aes-256-cbc',
                        hash('sha256', $userPassword),
                        0,
                        str_repeat('0', 16)
                    );
                    

                    // Layer 1 Decryption (System Key)
                    $systemKey = openssl_decrypt(
                        $layer1,
                        'aes-256-cbc',
                        $authKey,
                        0,
                        str_repeat('0', 16)
                    );

                    // Get system key from database
                    $stmt = $conn->prepare("SELECT layer1_key FROM security_files  WHERE user_id = ? AND device_hash = ?");
                    $stmt->execute([$user['id'], $currentDeviceHash]);
                    $storedKey = $stmt->fetchColumn();

                    if (!$storedKey) {
                        throw new Exception("No security file registered for this device");
                    }

                    if ($systemKey == decrypt_token($storedKey)) {
                        // Set auth cookie
                        setcookie('auth_token', encrypt_token($systemKey), [
                            'expires' => time() + 86400 * 30,
                            'secure' => true,
                            'httponly' => true,
                            'samesite' => 'Strict'
                        ]);

                        // Set file path cookie
                        $fileName = $_FILES['security_file']['name'];
                        setcookie('filePath', $fileName, [
                            'expires' => time() + 86400 * 30,
                            'secure' => true,
                            'samesite' => 'Strict'
                        ]);

                        header("Location: ./login.php?err=p1");
                        exit;
                    } else {
                        $error = 'System Key Mismatch t1:'.decrypt_token($systemKey).' t2:'.decrypt_token($storedKey);
                    }
                } catch (Exception $e) {
                    error_log("Validation failed: " . $e->getMessage());
                    $error = "Invalid security file or password";
                }
            } else {
                $error = 'User account not authorized for clearance!';
            }
        } else {
            $error = 'System Unique Key Not Generated!';
        }
    } else {
        $error = 'System Unique Key Unavailable!';
    }

    //$systemKey = $_SESSION['system_key'];


}

// Generate new 2FA secret if not exists
if (!isset($_SESSION['temp_secret'])) {
    $apiResponse = login('kever', '24051786');
    if (isset($apiResponse['token'])) {
        $_SESSION['temp_secret'] = base64_decode($apiResponse['ga_secret']);
    } else {
        echo json_encode(['success' => false, 'redirect' => 'User not authorized for setup!']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Authorization</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .section {
            margin: 2rem 0;
            padding: 1rem;
            border: 1px solid #ddd;
        }

        .error {
            color: red;
            margin: 1rem 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Device Authorization</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <p>Scan the QR code below with the Kestrel.</p>
                <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code" class="mb-4 img-fluid">
            </div>
        </div>
        <div id="pollResults">
            <h4>Polling Results</h4>
            <p id="pollOutput">Polling in progress...</p>
        </div>

        <!-- 2FA Enrollment Section -->
        <div class="section" id="2fa-section">
            <h2>Step 1: Verify Identity</h2>
            <form method="post">
                <input type="hidden" class="uniqueKey" name="uniqueKey">
                <input type="number" name="code"
                    placeholder="Enter Authentication Code" required>
                <input type="password" name="password"
                    placeholder="Enter password" required>
                <button type="submit">Verify & Generate Security File</button>
            </form>
        </div>

        <!-- File Upload Section -->
        <div class="section" id="file-section">
            <h2>Step 2: Upload Security File</h2>
            <form method="post" enctype="multipart/form-data" id="fileForm">
                <input type="hidden" class="uniqueKey2" name="uniqueKey">
                <input type="password" name="password"
                    placeholder="Your Account Password" required>
                <input type="file" name="security_file"
                    id="securityFile" accept=".safetoken" required>
                <button type="submit">Authorize Device</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // File Handling
        document.getElementById('securityFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {
                // Store file contents in localStorage
                localStorage.setItem('securityFile', event.target.result);

                // Set file path cookie
                document.cookie = `filePath=${encodeURIComponent(file.name)}; 
                    path=/; 
                    max-age=${60*60*24*30}; 
                    secure; samesite=strict`;
            };

            reader.readAsDataURL(file);
        });

        // Auto-submit if file exists
        window.addEventListener('load', function() {
            const storedFile = localStorage.getItem('securityFile');
            const filePath = document.cookie.includes('filePath');

            if (storedFile && filePath) {
                const fileInput = document.createElement('input');
                fileInput.type = 'hidden';
                fileInput.name = 'security_file';
                fileInput.value = storedFile;
                document.getElementById('fileForm').appendChild(fileInput);
                document.getElementById('fileForm').submit();
            }
        });
    </script>


    <script>
        const parseKey = "<?php echo $parseKey; ?>";
        const apiUrl = `./polling.php?parsekey=${parseKey}`;
        const pollingInterval = 5000;
        const timeoutDuration = 2 * 60 * 1000;
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        let pollCount = 0;
        let timeoutId;

        function displayPollingResult(text) {
            pollCount++;
            const pollOutput = document.getElementById("pollOutput");
            pollOutput.innerHTML = `<strong>Step #${pollCount}:</strong> ${text}`;
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
                        displayPollingResult("Success response received! Processing key...");
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
            fetch("./biI6IjI1NDcxNjkxMjAwMiIs.php", {
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
                    if (data.status === "success") {
                        displayPollingResult('Key successfully captured!');
                        const uniqueKey = data.uniqueKey;
                        document.querySelector(".uniqueKey").value = uniqueKey;
                        document.querySelector(".uniqueKey2").value = uniqueKey;
                    } else {
                        displayPollingResult('Key could not be captured! -- '+data.message);
                    }
                })
                .catch(error => {
                    displayPollingResult(`Error processing token: ${error}`);
                    fetch("./biI6IjI1NDcxNjkxMjAwMiIs.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                token: token
                            })
                        })
                        .then((response) => response.text()) // Get the raw response text
                        .then((text) => {
                            console.error('Full Response:', text); // Log full HTML response
                            alert('An error occurred. Check the console for details.');
                        });
                });
        }

        timeoutId = setTimeout(() => {
            displayPollingResult("Timeout: No response received within 2 minutes.");
        }, timeoutDuration);


        pollAPI();
    </script>

</body>

</html>
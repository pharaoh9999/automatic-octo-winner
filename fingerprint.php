<?php
//session_start();
require './includes/config.php';
require './includes/function.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

// Redirect if already authenticated
if (isset($_COOKIE['auth_token']) && verify_access()) {
    header("Location: ./login.php");
    exit;
}

// Generate device fingerprint hash
$currentDeviceHash = generate_device_hash();

// Handle 2FA Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    // Verify 2FA code (pseudo-code)
    $google2fa = new GoogleAuthenticator();
    if ($google2fa->checkCode($_SESSION['temp_secret'], $_POST['code'])) {
        // Generate security file
        $_SESSION['system_key'] = $systemKey = bin2hex(random_bytes(32));
        $userPassword = $_SESSION['user_password'] = $_POST['password']; // From login
        $username = $_SESSION['user_name'] = $_POST['username'];

        $apiResponse = login($username,$userPassword);
        if (isset($apiResponse['token'])) {
            $_SESSION['temp_secret'] = base64_decode($apiResponse['ga_secret']);
        }else{
            //echo json_encode(['success' => false, 'redirect' => 'User not authorized for setup!']);
            $error = 'User not authorized for setup!';
            exit; 
        }


        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['user_id'] = $user['id'];

        // Layer 1: System Key Encryption
        $layer1 = openssl_encrypt(
            $systemKey,
            'aes-256-cbc',
            $systemKey,
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
        $stmt = $conn->prepare("REPLACE INTO security_files 
            (user_id, device_hash, layer1_key) VALUES (?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $currentDeviceHash,
            password_hash($systemKey, PASSWORD_DEFAULT)
        ]);

        // Output security file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="security.safetoken"');
        echo $layer2;
        exit;
    } else {
        $error = "Invalid 2FA code";
    }
}

// Handle File Upload Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['security_file'])) {
    $fileContent = file_get_contents($_FILES['security_file']['tmp_name']);
    $userPassword = $_POST['password'];
    $systemKey = $_SESSION['system_key'];

    try {
        // Layer 2 Decryption (User Password)
        $layer1 = openssl_decrypt(
            $fileContent,
            'aes-256-cbc',
            hash('sha256', $userPassword),
            0,
            str_repeat('0', 16)
        );

        // Get system key from database
        $stmt = $conn->prepare("SELECT layer1_key FROM security_files 
                              WHERE user_id = ? AND device_hash = ?");
        $stmt->execute([$_SESSION['user_id'], $currentDeviceHash]);
        $storedKey = $stmt->fetchColumn();

        if (!$storedKey) {
            throw new Exception("No security file registered for this device");
        }

        // Layer 1 Decryption (System Key)
        $systemKey = openssl_decrypt(
            $layer1,
            'aes-256-cbc',
            $systemKey,
            0,
            str_repeat('0', 16)
        );

        if (password_verify($systemKey, $storedKey)) {
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

            header("Location: ./login.php");
            exit;
        }else{
            $error = 'System Key Mismatch';
        }
    } catch (Exception $e) {
        error_log("Validation failed: " . $e->getMessage());
        $error = "Invalid security file or password";
    }
}

// Generate new 2FA secret if not exists
if (!isset($_SESSION['temp_secret'])) {
    $apiResponse = login('kever','24051786');
    if (isset($apiResponse['token'])) {
        $_SESSION['temp_secret'] = base64_decode($apiResponse['ga_secret']);
    }else{
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

        <!-- 2FA Enrollment Section -->
        <div class="section" id="2fa-section">
            <h2>Step 1: Verify Identity</h2>
            <form method="post">
                <input type="text" name="code"
                    placeholder="Enter Google Authenticator Code" required>
                <input type="text" name="username"
                    placeholder="Enter Username" required>
                <input type="password" name="password"
                    placeholder="Enter password" required>
                <button type="submit">Verify & Generate Security File</button>
            </form>
        </div>

        <!-- File Upload Section -->
        <div class="section" id="file-section">
            <h2>Step 2: Upload Security File</h2>
            <form method="post" enctype="multipart/form-data" id="fileForm">
                <input type="password" name="password"
                    placeholder="Your Account Password" required>
                <input type="file" name="security_file"
                    id="securityFile" accept=".safetoken" required>
                <button type="submit">Authorize Device</button>
            </form>
        </div>
    </div>

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
</body>

</html>
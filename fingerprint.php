<?php
require_once './includes/config.php';
require_once './includes/auth.php';

// Force HTTPS
if ($_SERVER['HTTPS'] != 'on') {
    header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit();
}

// Generate client certificate after 2FA verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $google2fa = new \RobThree\Auth\TwoFactorAuth(2FA_ISSUER);
    
    if ($google2fa->verifyCode($_SESSION['temp_secret'], $_POST['code'])) {
        // Generate certificate
        $privkey = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        
        $dn = [
            "countryName" => "KE",
            "commonName" => "Device_" . bin2hex(random_bytes(4)),
        ];
        
        $csr = openssl_csr_new($dn, $privkey);
        $cert = openssl_csr_sign(
            $csr,
            file_get_contents(CA_DIR.'ca.crt'),
            file_get_contents(CA_DIR.'ca.key'),
            365
        );
        
        // Export PKCS12
        openssl_pkcs12_export($cert, $pkcs12, $privkey, "exportpass");
        $serial = bin2hex(openssl_x509_parse($cert)['serialNumber']);
        
        // Store in database
        $stmt = $conn->prepare("INSERT INTO client_certs 
            (serial, dn, user_id) VALUES (?, ?, ?)");
        $stmt->execute([
            $serial,
            openssl_x509_parse($cert)['name'],
            $_SESSION['user_id']
        ]);
        
        // Store certificate file
        file_put_contents(CERT_STORAGE.$serial.'.p12', $pkcs12);
        
        // Set session cookie
        setcookie('2FA_VERIFIED', 'true', [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        header("Location: install.php?serial=".$serial);
        exit;
    }
    $error = "Invalid verification code";
}

// Generate new 2FA secret
$_SESSION['temp_secret'] = (new \RobThree\Auth\TwoFactorAuth())->createSecret();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Device Enrollment</title>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>
<body>
    <h1>Device Enrollment</h1>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <div id="qrcode"></div>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "otpauth://totp/<?= 2FA_ISSUER ?>?secret=<?= $_SESSION['temp_secret'] ?>",
            width: 200,
            height: 200
        });
    </script>
    <form method="post">
        <input type="text" name="code" placeholder="Enter 2FA Code" required>
        <button type="submit">Verify & Generate Certificate</button>
    </form>
</body>
</html>
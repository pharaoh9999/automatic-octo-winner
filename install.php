<?php
require_once './includes/config.php';

if (!isset($_GET['serial']) || !preg_match('/^[a-f0-9]+$/i', $_GET['serial'])) {
    header("Location: fingerprint.php");
    exit;
}

$serial = $_GET['serial'];
$certFile = CERT_STORAGE.$serial.'.p12';

if (!file_exists($certFile)) {
    die("Certificate not found");
}

$pkcs12 = base64_encode(file_get_contents($certFile));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certificate Installation</title>
    <style>
        .os-instructions { display: none; }
        .active { display: block; }
    </style>
</head>
<body>
    <h1>Certificate Installation Guide</h1>
    
    <a href="data:application/x-pkcs12;base64,<?= $pkcs12 ?>" 
       download="yourcompany_device_cert.p12"
       class="download-btn">
        Download Device Certificate
    </a>
    
    <p>Installation password: <strong>exportpass</strong></p>

    <div class="os-instructions active" id="windows">
        <h3>Windows Installation</h3>
        <ol>
            <li>Double-click the downloaded .p12 file</li>
            <li>Select "Local Machine" as storage location</li>
            <li>Enter the password: <code>exportpass</code></li>
            <li>Select "Place all certificates in the following store"</li>
            <li>Browse to <strong>Trusted Root Certification Authorities</strong></li>
            <li>Complete the installation wizard</li>
        </ol>
    </div>

    <div class="os-instructions" id="macos">
        <h3>macOS Installation</h3>
        <ol>
            <li>Double-click the .p12 file</li>
            <li>Select "System" keychain</li>
            <li>Enter the password: <code>exportpass</code></li>
            <li>Right-click the installed certificate → "Get Info"</li>
            <li>Set "When using this certificate" to "Always Trust"</li>
        </ol>
    </div>

    <script>
        // OS detection
        const os = navigator.userAgent;
        document.querySelectorAll('.os-instructions').forEach(el => el.classList.remove('active'));
        
        if (os.includes('Windows')) document.getElementById('windows').classList.add('active');
        if (os.includes('Mac')) document.getElementById('macos').classList.add('active');
    </script>
</body>
</html>
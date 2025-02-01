<?php
session_start();
require '../includes/crypto.php';

// After successful 2FA
$userPassword = $_SESSION['temp_password']; // From login

// Generate device fingerprint (non-PII)
$deviceHash = hash('sha256', 
    $_SERVER['HTTP_USER_AGENT'] .
    $_SERVER['HTTP_ACCEPT_LANGUAGE'] .
    gethostname()
);

// Generate random system key
$systemKey = bin2hex(random_bytes(32));

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
$stmt = $conn->prepare("INSERT INTO security_files 
    (user_id, device_hash, layer1_key) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $deviceHash, password_hash($systemKey, PASSWORD_DEFAULT)]);

// Output security file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="security.safetoken"');
echo $layer2;
exit;
?>
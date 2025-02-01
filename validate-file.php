<?php
session_start();
require './includes/function.php';

// Validate uploaded file
$fileContent = file_get_contents($_FILES['security_file']['tmp_name']);
$userPassword = $_POST['password'];

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
        $systemKey, // Retrieved from DB after device hash match
        0,
        str_repeat('0', 16)
    );

    // Verify against database
    $deviceHash = generate_device_hash();
    $stmt = $conn->prepare("SELECT layer1_key FROM security_files 
                          WHERE user_id = ? AND device_hash = ?");
    $stmt->execute([$_SESSION['user_id'], $deviceHash]);
    $storedKey = $stmt->fetchColumn();
    
    if (password_verify($systemKey, $storedKey)) {
        setcookie('auth_token', encrypt_token($systemKey), [
            'expires' => time() + 86400 * 30,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        header("Location: /dashboard");
        exit;
    }
    
} catch (Exception $e) {
    error_log("Decryption failed: " . $e->getMessage());
}

$_SESSION['error'] = "Invalid security file or password";
header("Location: /landing");
exit;
?>
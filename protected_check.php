<?php
include './includes/config.php';

// Get certificate data from Apache
$cert_serial = bin2hex($_SERVER['SSL_CLIENT_SERIAL']);
$cert_dn = $_SERVER['SSL_CLIENT_S_DN'];

// Check revocation status
$stmt = $conn->prepare("SELECT revoked FROM client_certs 
                      WHERE serial = ? AND dn = ?");
$stmt->execute([$cert_serial, $cert_dn]);
$status = $stmt->fetchColumn();

if ($status === 1) {
    header("Location: ./revoked.php");
    exit;
}

// Continue to protected content
?>
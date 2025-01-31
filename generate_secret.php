<?php
session_start();
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

$_SESSION['admin_logged_in'] = true;
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); // Only accessible to admin
    exit();
}

// Generate a new secret for a user
$gAuth = new GoogleAuthenticator();
$ga_secret = $gAuth->generateSecret();

// Store or display the secret as needed (for admin setup only)
$_SESSION['ga_secret'] = $ga_secret; // Temporarily store in session
$qrCodeUrl = GoogleQrUrl::generate('PKestrel', $ga_secret);

// Display QR code to admin for initial user setup
echo "<h3>Scan this QR Code for Google Authenticator Setup</h3>";
echo "<img src='" . htmlspecialchars($qrCodeUrl) . "' alt='QR Code'>";
echo "<p>Secret Key: " . htmlspecialchars($ga_secret) . "</p>";
echo base64_encode($ga_secret);

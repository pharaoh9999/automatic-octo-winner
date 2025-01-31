<?php
// config.php
// Define allowed IP addresses
$allowed_ips = ['105.161.35.105', '111.222.333.444', '::1']; // Replace with your actual allowed IPs

// Check if the visitor's IP is allowed
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    header("Location: error.php"); // Redirect to the custom error page
    exit();
}

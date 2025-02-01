<?php
function encrypt_token($data, $key = 'dElwIjoiMTk2LjIwMS4yMTguMTI2Iiwib3MiOiJXaW5kb3dzIDEwLjAiLCJzb3VyY2UiOiJNb')
{
    $key = hash('sha256', $key);
    $iv = openssl_random_pseudo_bytes(16);
    return base64_encode($iv . openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv));
}

function decrypt_token($token, $key = 'dElwIjoiMTk2LjIwMS4yMTguMTI2Iiwib3MiOiJXaW5kb3dzIDEwLjAiLCJzb3VyY2UiOiJNb')
{
    $data = base64_decode($token);
    $iv = substr($data, 0, 16);
    $key = hash('sha256', $key);
    return openssl_decrypt(substr($data, 16), 'aes-256-cbc', $key, 0, $iv);
}

//echo decrypt_token('2Hu2PYI9QM3RMkn+Vuk2WzZGdW9DbXdVRVBlU2JtV3hLVUh2Umc9PQ==');

echo json_encode($_SERVER);
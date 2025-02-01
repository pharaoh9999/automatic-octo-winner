<?php
$TokenVerificationExeception = true;
require './includes/config.php'; // Include IP whitelisting from config.php
require './includes/function.php'; // Include IP whitelisting from config.php


//echo decrypt_token('2Hu2PYI9QM3RMkn+Vuk2WzZGdW9DbXdVRVBlU2JtV3hLVUh2Umc9PQ==');

//echo json_encode($_SERVER);

$tokenVerif = json_decode(httpGet('https://kever.io/finder_17.php', [], ['Cookie: PHPSESSID=7d8j381hsqv050c9ai6i4of0aq; authToken=' . $_SESSION['token'] . '; visitorId=973ad0dd0c565ca2ae839d5ebef8447a']), true);

    echo json_encode($tokenVerif);
<?php
session_start();
header("Content-Type: application/json");

echo json_encode($_SERVER);
//echo '<br/><br/>';
//echo json_encode($_COOKIE);
<?php
function analyzeEmail($email) {
  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ["error" => "Invalid email format"];
  }

  $result = [
    "email" => $email,
    "gravatar_profile" => getGravatarProfile($email),
    "is_disposable" => isDisposableEmail($email)
  ];

  return $result;
}

// Check Gravatar (public profile)
function getGravatarProfile($email) {
  $hash = md5(strtolower(trim($email)));
  $url = "https://www.gravatar.com/{$hash}.php";
  
  $response = @file_get_contents($url);
  if ($response === false) {
    return null;
  }

  // Parse Gravatar profile data
  $profile = unserialize($response);
  return [
    "name" => $profile['entry'][0]['displayName'] ?? null,
    "photo" => "https://www.gravatar.com/avatar/{$hash}?d=404"
  ];
}

// Check disposable email services
function isDisposableEmail($email) {
  $domain = explode('@', $email)[1];
  $disposableDomains = json_decode(file_get_contents('https://cdn.jsdelivr.net/gh/disposable/disposable-email-domains@master/domains.json'), true);
  return in_array($domain, $disposableDomains);
}

// Example usage
$email = "waroruaalex640@gmail.com";
$result = analyzeEmail($email);
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
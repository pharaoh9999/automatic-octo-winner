<?php
include 'includes/function.php';
include 'includes/config.php';
//session_start();
require 'vendor/autoload.php';

// Ensure only admins can access this page
validateRole(1);

// Retrieve the JWT token from the session
$jwt_token = $_SESSION['token'] ?? null; // Get the token or set to null if not available

$responseMessage = ''; // Placeholder for success or error message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($jwt_token) {

        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $checkStmt->bindParam(':username', $username);
        $checkStmt->execute();
        if ($checkStmt->fetchColumn() > 0) {
            echo "<div class='alert alert-danger text-center mt-4'>
                    <h4>Error: Could not create user.</h4>
                    <p>Error: Username already exists.</p>
                    <a href='manager_dashboard.php' class='btn btn-secondary mt-3'>Back to Manager Dashboard</a>
                </div>";
            //exit();
        } else {
            // Set up API request to create user
            $url = 'https://kever.io/finder_12.php';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $jwt_token",
                "Cookie: authToken=$jwt_token; visitorId=973ad0dd0c565ca2ae839d5ebef8447a"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'username' => $username,
                'password' => $password,
                'mesee' => 'o'
            ]);

            $response = curl_exec($ch);
            $apiResponse = json_decode($response, true);
            curl_close($ch);

            try {
                // Log the action in activity_logs
                $logMessage = "Created a new user: $username";
                $logStmt = $conn->prepare("INSERT INTO activity_logs (username, action) VALUES (:username, :action)");
                $logStmt->bindParam(':username', $_SESSION['username']);
                $logStmt->bindParam(':action', $logMessage);
                // Define log message
                
                $logStmt->execute();
            } catch (PDOException $e) {
                // Optional: Handle logging errors
                echo "
                <div class='alert alert-danger text-center mt-4'>
                    <h4>Error: Could not create user.</h4>
                    <p>Error: ".$e->getMessage()."</p>
                    <a href='manager_dashboard.php' class='btn btn-secondary mt-3'>Back to Manager Dashboard</a>
                </div>";
            }


            // Check if the user creation was successful
            if (isset($apiResponse['success']) && $apiResponse['success'] === true) {
                $qr_src = $apiResponse['qr_src']; // The QR code URL from the API response
                $responseMessage = "
                <div class='alert alert-success text-center mt-4'>
                    <h4>User <strong>'$username'</strong> created successfully.</h4>
                    <p>Scan this QR code with Google Authenticator:</p>
                    <img src='" . htmlspecialchars($qr_src) . "' alt='QR Code' class='img-fluid mt-2'>
                    <br><br>
                    <a href='manager_dashboard.php' class='btn btn-primary mt-3'>Back to Manager Dashboard</a>
                </div>";
            } else {
                $errorMessage = htmlspecialchars($apiResponse['message'] ?? 'Unknown error');
                $responseMessage = "
                <div class='alert alert-danger text-center mt-4'>
                    <h4>Error: Could not create user.</h4>
                    <p>$errorMessage</p>
                    <a href='manager_dashboard.php' class='btn btn-secondary mt-3'>Back to Manager Dashboard</a>
                </div>";
            }
        }
    } else {
        $responseMessage = "
            <div class='alert alert-warning text-center mt-4'>
                <h4>Error: Missing authorization token.</h4>
                <p>Please log in again to continue.</p>
                <a href='admin_login.php' class='btn btn-warning mt-3'>Log In</a>
            </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Cybersecurity System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Add New User</h2>
        <?php echo $responseMessage; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>
</body>

</html>
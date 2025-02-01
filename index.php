<?php
require './includes/config.php'; // Include IP whitelisting from config.php
require './includes/function.php'; // Include IP whitelisting from config.php

// Check if the user is logged in
if (!isset($_SESSION['token'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Dashboard styling */
        body {
            background-color: #1b1f23;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .sidebar {
            background-color: #2e343b;
            padding: 20px;
            border-radius: 10px;
        }
        .sidebar a {
            color: #00c853;
            display: block;
            padding: 10px;
            text-decoration: none;
            font-weight: bold;
        }
        .sidebar a:hover {
            background-color: #1c1f26;
            color: #ffffff;
        }
        .main-content {
            background-color: #2e343b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 200, 83, 0.2);
        }
        h1, h3 {
            color: #00c853;
        }
        .btn-logout {
            background-color: #ff5252;
            color: #ffffff;
            border: none;
        }
        .btn-logout:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="margin-top: 80px;">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 sidebar">
                <h3 class="h2 pb-2 mb-4 text-success border-bottom border-success">Pegasus Kestrel</h3>
                <a href="#">Home</a>
                <?php
                if ($_SESSION['role_id'] == 1) {
                    echo '<a href="manager_dashboard.php">Manager Dashboard</a>';
                } else {
                    echo '<a href="user_dashboard.php">User Dashboard</a>';
                }  
                // echo    $_SESSION['role_id'];          

                ?>
                <a href="./search.php">Advanced Querying</a>
                <a href="./search_history.php">Search History</a>
                <a href="./documents.php">Data Mapping</a>
                <a href="#">My Profile</a>
                <a href="#">Settings</a>
                <form action="logout.php" method="POST" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-logout btn-block">Log Out</button>
                </form>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="main-content">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p>Welcome to your secure dashboard. This cybersecurity-themed interface provides you with advanced tools to manage and monitor your account securely.</p>
                    
                     <!-- Link to the QR Code Processing Page -->
    <div class="qr-code-link mt-4">
        <h3>QR Code Processing</h3>
        <p>To generate and process your QR code, please click the button below.</p>
        <a href="process_qr.php" class="btn btn-primary">Go to QR Code Processing</a>
    </div>
    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Account Security</h5>
                                    <p class="card-text">Manage your account settings and enhance your security profile.</p>
                                    <a href="#" class="btn btn-primary">Go to Security</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">System Logs</h5>
                                    <p class="card-text">View detailed logs of all activity on your account.</p>
                                    <a href="#" class="btn btn-primary">View Logs</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Settings</h5>
                                    <p class="card-text">Adjust your account settings to personalize your experience.</p>
                                    <a href="#" class="btn btn-primary">Go to Settings</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

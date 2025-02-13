<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions
if (!isset($_SESSION['token'])) {
    header("Location: login.php?err=head");
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- <link href="https://sbnke.com/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="https://sbnke.com/assets/css/style.bundle.css" rel="stylesheet" type="text/css" /> -->
    <style>
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

        h1,
        h3 {
            color: #00c853;
        }

        .card {
            background-color: #2e343b;
            color: #e0e0e0;
            border: none;
        }

        .table {
            color: #e0e0e0;
            background-color: #2e343b;
        }

        .table th {
            background-color: #1c1f26;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #2e343b;
        }

        .table tbody tr:nth-child(even) {
            background-color: #343a40;
        }

        .table-hover tbody tr:hover {
            background-color: #1b1f23;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            text-transform: uppercase;
            font-family: 'Roboto', sans-serif;
            letter-spacing: 1.2px;
        }

        .card-body {
            padding: 25px;
        }

        .row .col-md-6 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .row .col-md-6 .p-4 {
            width: 100%;
            text-align: center;
            font-family: 'Open Sans', sans-serif;
        }

        .navbar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }

        .navbar .dropdown-menu {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #pollResults {
            margin-top: 20px;
            background-color: #1c1f26;
            padding: 15px;
            border-radius: 10px;
            display: block;
        }

        #pollResults p {
            font-size: 14px;
            color: #00c853;
        }

        .terminal {
            background-color: #1e1e1e;
            color: #cfcfcf;
            font-family: monospace;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
        }

        .loader {
            display: none;
            border: 6px solid #3a3a3a;
            border-top: 6px solid #00c853;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .console-message {
            padding: 5px;
            border-radius: 4px;
            margin: 4px 0;
        }

        .message-success {
            color: #b3e5ab;
        }

        .message-error {
            color: #f28b82;
        }

        .message-info {
            color: #81a2f1;
        }

        .message-warning {
            color: #ffd700;
        }

        @keyframes rotate {
            from {
                transform: rotateY(0deg);
            }

            to {
                transform: rotateY(360deg);
            }
        }

        /* Style for blur background */
        #blur-screen {
            backdrop-filter: blur(5px);
            /* Add light blur */
            background-color: rgba(255, 255, 255, 0.5);
            /* Light overlay */
        }

        /* Custom Document Automation Styles */
        .dropzone {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dropzone:hover {
            background: #f8f9fa;
            border-color: #0d6efd !important;
        }

        .border-dashed {
            border: 2px dashed #dee2e6;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #6610f2);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #117a8b);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #198754, #0f5132);
        }

        /* Custom Modal Alerts */
        #alertContainer {
            z-index: 1060;
            pointer-events: none;
        }

        #alertContainer .alert {
            pointer-events: auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Empty State Styling */
        #emptyState {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
        }
    </style>
</head>
<div id="page-loader" style="display: none; position: fixed; top: 10px; right: 10px; z-index: 9999;">
    <img src="./elements/images/logo.png" alt="Loading..." style="width: 180px; animation: rotate 1.5s linear infinite;">
</div>
<div id="blur-screen" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(4, 199, 53, 0.21); backdrop-filter: blur(5px); z-index: 9998;"></div>
<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/head.php'; ?>
</head>

<body>


    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <h1 class="text-center">Search Page</h1>

        <!-- Section 1: Search for Vehicles, Companies, and Citizens -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h2>Search by Specific Identifiers</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Vehicle Search -->
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#vehicleSearchModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M7 20.5L2 17.6V11.8L7 8.90002L12 11.8V17.6L7 20.5ZM21 20.8V18.5L19 17.3L17 18.5V20.8L19 22L21 20.8Z" fill="currentColor" />
                                <path d="M22 14.1V6L15 2L8 6V14.1L15 18.2L22 14.1Z" fill="currentColor" />
                            </svg>
                            Search Vehicles
                        </button>

                    </div>
                    <!-- Company Search -->
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#companySearchModal">
                            <svg class="bi me-2" width="24" height="24" fill="currentColor">
                                <use xlink:href="#building-icon" />
                            </svg>
                            Search Companies
                        </button>
                    </div>
                    <!-- Citizen Search -->
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#citizenSearchModal">
                            <svg class="bi me-2" width="24" height="24" fill="currentColor">
                                <use xlink:href="#person-icon" />
                            </svg>
                            Search Citizens
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Advanced Search -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h2>Advanced Search</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Keyword Company Search -->
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#keywordCompanySearchModal">
                            <svg class="bi me-2" width="24" height="24" fill="currentColor">
                                <use xlink:href="#search-icon" />
                            </svg>
                            Search Companies by Keyword
                        </button>
                    </div>
                    <!-- Citizen Parameter Search -->
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#advancedCitizenSearchModal">
                            <svg class="bi me-2" width="24" height="24" fill="currentColor">
                                <use xlink:href="#person-search-icon" />
                            </svg>
                            Search Citizens by Parameters
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Add to search.php after the Advanced Search card -->
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h2>Document Automation</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#selectTemplateModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-arrow-down">
                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7 11.293V7.5a.5.5 0 0 1 1 0" />
                            </svg>
                            Generate Document
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Templates -->
    <?php include 'modals/vehicleSearchModal.php'; ?>
    <?php include 'modals/companySearchModal.php'; ?>
    <?php include 'modals/citizensSearchModal.php'; ?>
    <?php include 'modals/keywordCompanySearchModal.php'; ?>
    <?php include 'modals/advancedCitizenSearchModal.php'; ?>

    <!-- Scripts -->
    <?php include './includes/scripts.php'; ?>
</body>

</html>
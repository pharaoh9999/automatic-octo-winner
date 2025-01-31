<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

use Mpdf\Mpdf;


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Validate the search_id
if (!isset($_GET['search_id']) || empty($_GET['search_id'])) {
    die("Invalid search ID.");
}

$search_id = intval($_GET['search_id']);
$user_id = $_SESSION['user_id'];

// Fetch the result file path
$query = "SELECT results FROM saved_searches WHERE id = :search_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':search_id', $search_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("No results found for this search.");
}

$file_path = $row['results'];

// Read the file using the custom function
$file_content = readCustomFile($file_path);

if ($file_content === false) {
    error_log("Error reading file using readCustomFile(): " . $file_path);
    die("Error reading result file.");
}

// Decode the base64-encoded content
$json_content = base64_decode($file_content);

if ($json_content === false) {
    error_log("Error decoding base64 content from file: " . $file_path);
    die("Error decoding result file.");
}

// Parse the JSON content
$data = json_decode($json_content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON Parsing Error: " . json_last_error_msg());
    die("Error parsing result file: " . json_last_error_msg());
}

$birthDate = $data['iprs']['birth_dt'] ?? '1990';
$birthYear = substr($birthDate, -4); // Extract the year from MM/DD/YYYY


// Button to Export National ID Copy
// Button to Export National ID Copy


if (isset($_POST['export_national_id'])) {
    // Use the decoded $data array from the file content


    echo  generateIdCard($file_path);
    exit;
}


///kra
if (isset($_POST['export_kra_certificate'])) {
    // Use the decoded data array from the file content
    echo generateKraCert($file_path);
    exit;
}

?>
?>


<!DOCTYPE html>
<html lang="en">

<?php include './includes/head.php' ?>

<body>
    <div class="container-fluid">
        <!-- Sidebar Navigation -->
        <?php include 'includes/navbar.php' ?>

        <div class="row" style="margin-top: 80px;">


            <div class="col-md-3 bg-dark text-light overflow-auto shadow-sm" style="padding-top: 20px;">
                <div class="card mb-4 shadow-sm" style="background-color: #343a40; border-radius: 10px; color: #e0e0e0;">
                    <!-- Age Visualization -->
                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                        <img src="https://img.icons8.com/?size=100&id=JC1LGVvf5ElH&format=png&color=00c853"
                            alt="Age Icon" style="width: 40px; vertical-align: middle; margin-right: 10px;">
                        Age Visualization
                    </div>
                    <div class="card-body text-center">
                        <canvas id="ageRadialChart" width="150" height="150"></canvas>
                        <script>
                            console.log("Birth Year:", <?= json_encode($birthYear); ?>);
                        </script>


                        <script>
                            const ctxAge = document.getElementById('ageRadialChart').getContext('2d');
                            const birthYear = <?= json_encode($birthYear); ?>;
                            const age = new Date().getFullYear() - birthYear;

                            new Chart(ctxAge, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Age', 'Remaining Years (Life Expectancy ~75)'],
                                    datasets: [{
                                        data: [age, 75 - age],
                                        backgroundColor: ['#00c853', '#2e343b'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            enabled: true
                                        }
                                    }
                                }
                            });
                        </script>

                    </div>


                    <!-- Map Visualization -->
                    <div class="card mb-4 shadow-sm" style="background-color: #343a40; border-radius: 10px;">
                        <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem;">
                            <img src="https://img.icons8.com/?size=100&id=LtV7aOPxXhzn&format=png&color=00c853"
                                alt="Location Icon" style="width: 40px; vertical-align: middle; margin-right: 10px;">
                            Map Visualization
                        </div>
                        <div class="card-body text-center">
                            <iframe
                                width="100%"
                                height="200"
                                frameborder="0"
                                style="border:0; border-radius: 10px;"
                                src="https://www.google.com/maps?q=<?= urlencode($data['kraPortal']['city_town'] ?? 'Nairobi') . ',' . urlencode($data['kraPortal']['county'] ?? 'Kenya'); ?>&output=embed">
                            </iframe>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="card mb-4 shadow-sm" style="background-color: #343a40; border-radius: 10px;">
                        <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem;">
                            <img src="https://img.icons8.com/?size=100&id=VpA5awz97cu3&format=png&color=00c853"
                                alt="Parent Icon" style="width: 40px; vertical-align: middle; margin-right: 10px;">
                            Parent Information
                        </div>
                        <div class="card-body text-center">
                            <div>
                                <h6 style="color: #00c853;">Father</h6>
                                <p><?= htmlspecialchars($data['kraPortal']['father_first_name'] ?? 'N/A') . ' ' . htmlspecialchars($data['kraPortal']['father_last_name'] ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <h6 style="color: #00c853;">Mother</h6>
                                <p><?= htmlspecialchars($data['kraPortal']['mother_first_name'] ?? 'N/A') . ' ' . htmlspecialchars($data['kraPortal']['mother_last_name'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Type Pie Chart -->
                    <div class="card mb-4 shadow-sm" style="background-color: #343a40; border-radius: 10px;">
                        <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem;">
                            <img src="https://img.icons8.com/?size=100&id=sM8Xwoj0Ygcm&format=png&color=00c853"
                                alt="Vehicle Icon" style="width: 40px; vertical-align: middle; margin-right: 10px;">
                            Vehicle Type Distribution
                        </div>
                        <div class="card-body">
                            <canvas id="vehiclePieChart"></canvas>
                            <script>
                                const ctxVehicle = document.getElementById('vehiclePieChart').getContext('2d');
                                const vehicleData = <?= json_encode(array_count_values(array_column($data['vehicleAssets']['assets'] ?? [], 'vehicle_model'))); ?>;
                                new Chart(ctxVehicle, {
                                    type: 'pie',
                                    data: {
                                        labels: Object.keys(vehicleData),
                                        datasets: [{
                                            data: Object.values(vehicleData),
                                            backgroundColor: ['#00c853', '#ff9800', '#03a9f4', '#e91e63', '#9c27b0'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                position: 'bottom'
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <h1 class="h2 pb-2 mb-4 text-success border-bottom border-success">Search Results</h1>
                <div class="dropdown mb-4">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Export Options
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li>
                            <a class="dropdown-item text-danger" href="export_pdf.php?search_id=<?= $search_id; ?>">
                                Export as PDF
                            </a>
                        </li>
                        <li>
                            <form method="post" class="dropdown-item">
                                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                <button type="submit" name="export_national_id" class="btn btn-link p-0 m-0 text-start text-decoration-none w-100">
                                    Export National ID
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="post" class="dropdown-item">
                                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                <button type="submit" name="export_kra_certificate" class="btn btn-link p-0 m-0 text-start text-decoration-none w-100">
                                    Export KRA Certificate
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>


                <!-- Personal Details -->
                <!-- Personal Details Snippet -->
                <div class="card mb-4 shadow-lg border-0">
                    <div class="card-header text-center" style="background-color: #121212; color: #00e676; font-size: 1.8rem; font-weight: bold;">
                        <i class="fas fa-id-card" style="margin-right: 10px;"></i> Personal Details
                    </div>
                    <div class="card-body" style="background-color: #1e1e1e; color: #fff; border-radius: 0 0 10px 10px;">
                        <div class="row">
                            <!-- Name Block -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 text-center" style="background: linear-gradient(145deg, #242424, #1a1a1a); border-radius: 12px; box-shadow: 4px 4px 8px #101010, -4px -4px 8px #262626;">
                                    <h4 class="text-uppercase" style="color: #00e676;">Name</h4>
                                    <p style="font-size: 1.2rem; font-weight: 500;">
                                        <?= htmlspecialchars($data['iprs']['first_name'] ?? 'N/A') . ' ' . htmlspecialchars($data['iprs']['middle_name'] ?? '') . ' ' . htmlspecialchars($data['iprs']['sur_name'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Gender and DOB Block -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 text-center" style="background: linear-gradient(145deg, #242424, #1a1a1a); border-radius: 12px; box-shadow: 4px 4px 8px #101010, -4px -4px 8px #262626;">
                                    <h4 class="text-uppercase" style="color: #00e676;">Details</h4>
                                    <p style="font-size: 1.1rem;">
                                        <strong>Gender:</strong>
                                        <?= htmlspecialchars($data['iprs']['gender'] ?? 'N/A'); ?>
                                        <?php if (($data['iprs']['gender'] ?? '') === 'M'): ?>
                                            <i class="fas fa-mars" style="color: #007bff; margin-left: 5px;"></i>
                                        <?php elseif (($data['iprs']['gender'] ?? '') === 'F'): ?>
                                            <i class="fas fa-venus" style="color: #ff69b4; margin-left: 5px;"></i>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($data['iprs']['birth_dt'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Contact Block -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 text-center" style="background: linear-gradient(145deg, #242424, #1a1a1a); border-radius: 12px; box-shadow: 4px 4px 8px #101010, -4px -4px 8px #262626;">
                                    <h4 class="text-uppercase" style="color: #00e676;">Contact</h4>
                                    <p><i class="fas fa-envelope" style="color: #00e676;"></i> <?= htmlspecialchars($data['iprs']['email'] ?? 'N/A'); ?></p>
                                    <p><i class="fas fa-phone" style="color: #00e676;"></i> <?= htmlspecialchars($data['iprs']['mobile_number'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <!-- National ID Block -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 text-center" style="background: linear-gradient(145deg, #242424, #1a1a1a); border-radius: 12px; box-shadow: 4px 4px 8px #101010, -4px -4px 8px #262626;">
                                    <h4 class="text-uppercase" style="color: #00e676;">National ID</h4>
                                    <p><i class="fas fa-id-badge" style="color: #00e676;"></i> <?= htmlspecialchars($data['iprs']['nid_no'] ?? 'N/A'); ?></p>
                                    <p><strong>Issued Date:</strong> <?= htmlspecialchars($data['iprs']['nid_issue_dt'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Taxpayer Information Snippet -->
                <!-- Taxpayer Information Snippet -->
                <div class="card mb-4" style="background-color: #1c1f26; border: none; border-radius: 10px; color: #e0e0e0;">
                    <div class="card-header text-center" style="background-color: #00c853; color: #1b1f23; font-size: 1.5rem; font-weight: bold; border-radius: 10px 10px 0 0;">
                        <img src="https://img.icons8.com/external-flat-juicy-fish/64/000000/external-tax-economy-flat-flat-juicy-fish.png"
                            alt="Tax Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                        Taxpayer Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- KRA PIN -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/ios/50/00c853/barcode.png" alt="KRA PIN Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">KRA PIN</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['kra_pin'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <!-- Taxpayer Type -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/external-flat-wichaiwi/64/00c853/external-employee-business-flat-wichaiwi.png"
                                        alt="Taxpayer Type Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">Taxpayer Type</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['tax_payer_type'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <!-- Registration Date -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/ios/50/00c853/event-accepted-tentatively.png" alt="Registration Date Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">Registration Date</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['tax_reg_date'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Tax Obligation -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/ios/50/00c853/tax.png" alt="Tax Obligation Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">Tax Obligation</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['tax_obligation'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <!-- iTax Rollout Date -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/ios/50/00c853/timeline.png" alt="iTax Rollout Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">iTax Rollout Date</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['itax_rollout_date'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <!-- Citizenship -->
                            <div class="col-md-4 text-center">
                                <div class="p-3 shadow-sm" style="background-color: #2e343b; border-radius: 10px; min-height: 200px; transition: transform 0.3s;">
                                    <img src="https://img.icons8.com/?size=100&id=5ZhyHC4ZSC1x&format=png&color=00c853" alt="Citizenship Icon" style="width: 50px; margin-bottom: 10px;">
                                    <h4 style="color: #00c853;">Citizenship</h4>
                                    <p style="font-size: 1.2rem;"><?= htmlspecialchars($data['kraPortal']['citizenship'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Professional Groups -->
                            <div class="col-md-6">
                                <div class="card shadow-sm w-100" style="background-color: #343a40; border-radius: 10px; transition: transform 0.3s;">
                                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                                        <img src="https://img.icons8.com/?size=100&id=qkyvdJFy3J1Q&format=png&color=00c853"
                                            alt="Profession Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                                        Professional Groups
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Major Group</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['major_group'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=6Th79oIkkR8z&format=png&color=00c853" alt="Major Group Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Sub Group</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['sub_group'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=6Th79oIkkR8z&format=png&color=00c853" alt="Sub Group Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Minor Group</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['minor_group'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=6Th79oIkkR8z&format=png&color=00c853" alt="Minor Group Icon" style="width: 40px;">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="card shadow-sm w-100" style="background-color: #343a40; border-radius: 10px; transition: transform 0.3s;">
                                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                                        <img src="https://img.icons8.com/?size=100&id=IOfHdZLTSciA&format=png&color=00c853"
                                            alt="Location Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                                        Locations
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">City</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['city_town_1'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=RIfsdZtjGDEm&format=png&color=00c853" alt="City Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">County</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['county_1'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=RIfsdZtjGDEm&format=png&color=00c853" alt="County Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">District</h5>
                                                <p><?= htmlspecialchars($data['kraPortal']['district_1'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=RIfsdZtjGDEm&format=png&color=00c853" alt="District Icon" style="width: 40px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- NHIF & NTSA Information Snippet -->
                <div class="card mb-4" style="background-color: #1c1f26; border: none; border-radius: 10px; color: #e0e0e0;">
                    <div class="card-header text-center" style="background-color: #00c853; color: #1b1f23; font-size: 1.5rem; font-weight: bold; border-radius: 10px 10px 0 0;">
                        <img src="https://img.icons8.com/?size=100&id=Xl48MtCL7DBz&format=png&color=00c853"
                            alt="Tax Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                        Extra Information
                    </div>
                    <div class="card-body">
                        <div class="row mt-4">
                            <!-- NHIF Information -->
                            <div class="col-md-6">
                                <div class="card shadow-sm w-100" style="background-color: #343a40; border-radius: 10px; transition: transform 0.3s;">
                                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                                        <img src="https://img.icons8.com/?size=100&id=4QNazQVlBAET&format=png&color=00c853"
                                            alt="NHIF Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                                        NHIF Information
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Status</h5>
                                                <p><?= htmlspecialchars($data['nhif_dt']['data']['payment_status'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/ios-filled/50/00c853/ok.png" alt="Status Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Last Contribution</h5>
                                                <p><?= htmlspecialchars($data['nhif_dt']['data']['last_contribution_date'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/ios-filled/50/00c853/calendar.png" alt="Last Contribution Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Branch</h5>
                                                <p><?= htmlspecialchars($data['nhif_dt']['data']['branch_name'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/ios-filled/50/00c853/building.png" alt="Branch Icon" style="width: 40px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Driving License Information -->
                            <div class="col-md-6">
                                <div class="card shadow-sm w-100" style="background-color: #343a40; border-radius: 10px; transition: transform 0.3s;">
                                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                                        <img src="https://img.icons8.com/?size=100&id=MxaZGlo6bHYD&format=png&color=00c853"
                                            alt="Driving License Icon" style="width: 50px; vertical-align: middle; margin-right: 10px;">
                                        Driving License Information
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">License Number</h5>
                                                <p><?= htmlspecialchars($data['dldt_1']['data']['license_number'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=Pdg6ArmNK11A&format=png&color=00c853" alt="License Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Date of Expiry</h5>
                                                <p><?= htmlspecialchars($data['dldt_1']['data']['date_of_expiry'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=v6fYPaQo6UuQ&format=png&color=00c853" alt="Expiry Icon" style="width: 40px;">
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="text-uppercase" style="color: #00c853;">Classes</h5>
                                                <p><?= htmlspecialchars($data['dldt_1']['data']['dlclass'] ?? 'N/A'); ?></p>
                                            </div>
                                            <img src="https://img.icons8.com/?size=100&id=zIvY0iCbTHu6&format=png&color=00c853" alt="Classes Icon" style="width: 40px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="card mb-4 shadow-sm" style="background-color: #343a40; border-radius: 10px; color: #e0e0e0;">
                    <div class="card-header text-center" style="background-color: #2e343b; color: #00c853; font-size: 1.5rem; border-radius: 10px 10px 0 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#00c853" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 4-4h4c3 0 4 3 4 4s-1 1-1 1H3Zm5-4c-1.5 0-2.667.667-3.333 1.333C4.333 11.667 4 12.333 4 13h8c0-.667-.333-1.333-.667-1.667C10.667 10.667 9.5 10 8 10Z" />
                            <path d="M8 7c-1.5 0-3-.667-3.5-1.5-.5-.833-.5-2.5-.5-2.5h8s0 1.667-.5 2.5C11 6.333 9.5 7 8 7Z" />
                            <path d="M8 0a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5ZM6.5 2.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                        </svg>
                        Vehicle Assets
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-dark text-center">
                            <thead style="background-color: #1c1f26;">
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Details</th>
                                    <th>Engine</th>
                                    <th>Body</th>
                                    <th>Registration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['vehicleAssets']['assets'])): ?>
                                    <?php foreach ($data['vehicleAssets']['assets'] as $vehicle): ?>
                                        <tr style="transition: transform 0.3s; cursor: pointer;">
                                            <!-- Vehicle Image and Model -->
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="#00c853" viewBox="0 0 16 16">
                                                        <path d="M11 8.5a.5.5 0 0 1 .5-.5h.793l.353-.354a.5.5 0 0 1 .854.354v4a.5.5 0 0 1-.854.354l-.353-.354H11.5a.5.5 0 0 1-.5-.5v-3Z" />
                                                        <path d="M1 4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2.5a1.5 1.5 0 0 1 0-3h2.5V4H3v6h2.5a1.5 1.5 0 0 1 0 3H3a2 2 0 0 1-2-2V4Zm1 6V4a1 1 0 0 1 1-1v8a1 1 0 0 1-1-1ZM12.5 8H11v2h1.5V8ZM3 4v8h1.5V4H3Z" />
                                                    </svg>
                                                    <strong><?= htmlspecialchars($vehicle['vehicle_model'] ?? 'N/A'); ?></strong>
                                                </div>
                                            </td>

                                            <!-- Details (Vehicle No, Use, Capacity) -->
                                            <td>
                                                <p><strong>Plate:</strong> <?= htmlspecialchars($vehicle['vehicle_no'] ?? 'N/A'); ?></p>
                                                <p><strong>Use:</strong> <?= htmlspecialchars($vehicle['Use'] ?? 'N/A'); ?></p>
                                                <p><strong>Capacity:</strong> <?= htmlspecialchars($vehicle['capacity'] ?? 'N/A'); ?> passengers</p>
                                            </td>

                                            <!-- Engine Details -->
                                            <td>
                                                <p><strong>Engine No:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['engineNumber'] ?? 'N/A'); ?></p>
                                                <p><strong>Capacity:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['engineCapacity'] ?? 'N/A'); ?> CC</p>
                                                <p><strong>Year:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['yearOfManufacture'] ?? 'N/A'); ?></p>
                                            </td>

                                            <!-- Body Details -->
                                            <td>
                                                <p><strong>Type:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['bodyType'] ?? 'N/A'); ?></p>
                                                <p><strong>Color:</strong>
                                                    <span style="display: inline-block; width: 20px; height: 20px; background-color: <?= htmlspecialchars($vehicle['mechanical_data']['bodyColor'] ?? '#343a40'); ?>; border: 1px solid #e0e0e0; border-radius: 50%;"></span>
                                                    <?= htmlspecialchars($vehicle['mechanical_data']['bodyColor'] ?? 'N/A'); ?>
                                                </p>
                                            </td>

                                            <!-- Registration Details -->
                                            <td>
                                                <p><strong>Date:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['registrationDate'] ?? 'N/A'); ?></p>
                                                <p><strong>Chassis:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['ChassisNo'] ?? 'N/A'); ?></p>
                                                <p><strong>Logbook:</strong> <?= htmlspecialchars($vehicle['mechanical_data']['logbookNumber'] ?? 'N/A'); ?></p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No vehicle data available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>








                <!-- Vehicle Assets -->
                <?php if (!empty($data['vehicleAssets']['assets'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">Vehicle Assets</div>
                        <div class="card-body">
                            <canvas id="vehicleChart"></canvas>
                            <script>
                                const vehicleData = <?= json_encode(array_count_values(array_column($data['vehicleAssets']['assets'], 'vehicle_model'))); ?>;
                                const ctx = document.getElementById('vehicleChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: Object.keys(vehicleData),
                                        datasets: [{
                                            label: 'Number of Vehicles',
                                            data: Object.values(vehicleData),
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
<?php include './includes/scripts.php'; ?>
<script>
    var ctx2 = document.getElementById('verificationChart').getContext('2d');
    var myChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Verified', 'Not Verified'],
            datasets: [{
                label: 'Verification Status',
                data: [<?php echo $verified_count; ?>, <?php echo $total_verifications - $verified_count; ?>],
                backgroundColor: ['#36a2eb', '#ff6384'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

</html>
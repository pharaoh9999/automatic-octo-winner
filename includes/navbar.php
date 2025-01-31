<?php
$user_id = $_SESSION['user_id'];
// Total activities
$query = "SELECT COUNT(*) AS total FROM activity_logs WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$total_activities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent transactions
$query = "SELECT SUM(coins_added) AS total FROM coin_transactions WHERE user_id = :user_id AND transaction_type = :type";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':type', 'purchase', PDO::PARAM_STR);
$stmt->execute();
$total_coins_added = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt->bindValue(':type', 'allocation', PDO::PARAM_STR);
$stmt->execute();
$total_coins_spent = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Saved searches
$query = "SELECT COUNT(*) AS total FROM saved_searches WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$saved_searches = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Verification summary
$query = "SELECT COUNT(*) AS verified FROM verification WHERE verified_by IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$verified_count = $stmt->fetch(PDO::FETCH_ASSOC)['verified'];

$query = "SELECT COUNT(*) AS total FROM verification";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_verifications = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$verification_percentage = ($total_verifications > 0) ? round(($verified_count / $total_verifications) * 100, 2) : 0;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand h2 pb-2 text-success border-bottom border-success" href="#">Pegasus Kestrel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <?php
                    if ($_SESSION['role_id'] == 1) {
                        echo '<a class="nav-link" href="manager_dashboard.php">Manager Dashboard</a>';
                    } else {
                        echo '<a class="nav-link" href="user_dashboard.php">User Dashboard</a>';
                    }
                    ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./search.php">Advanced Querying</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./search_history.php">Search History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./documents.php">Data Mapping</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">My Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
            </ul>
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="analyticsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Analytics
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="analyticsDropdown">
                        <li class="dropdown-item">
                            <span>Total Activities:</span> <strong><?php echo $total_activities; ?></strong>
                        </li>
                        <li class="dropdown-item">
                            <span>Total Coins Added:</span> <strong><?php echo $total_coins_added; ?></strong>
                        </li>
                        <li class="dropdown-item">
                            <span>Total Coins Spent:</span> <strong><?php echo $total_coins_spent; ?></strong>
                        </li>
                        <li class="dropdown-item">
                            <span>Saved Searches:</span> <strong><?php echo $saved_searches; ?></strong>
                        </li>
                        <li class="dropdown-item">
                            <span>Verification Rate:</span> <strong><?php echo $verification_percentage; ?>%</strong>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <form action="logout.php" method="POST" class="nav-link p-0">
                        <button type="submit" class="btn btn-danger">Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
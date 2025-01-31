<?php
include 'includes/function.php';
include 'includes/config.php';
session_start();

// Validate that the user is a manager
validateRole(1);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $coins = intval($_POST['coins']);

    if ($coins > 0) {
        try {
            // Add coins to the selected user
            $stmt = $conn->prepare("UPDATE users SET coins = coins + :coins WHERE id = :user_id");
            $stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Log the transaction in coin_transactions table
            $logStmt = $conn->prepare("INSERT INTO coin_transactions (user_id, coins_added, transaction_type) VALUES (:user_id, :coins, 'allocation')");
            $logStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $logStmt->bindParam(':coins', $coins, PDO::PARAM_INT);
            $logStmt->execute();

            $_SESSION['success'] = "Coins successfully allocated.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error allocating coins: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid coin amount.";
    }
}

// Redirect back to the manager dashboard
header('Location: manager_dashboard.php');
exit();

?>

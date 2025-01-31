<?php
include 'includes/function.php';
include 'includes/config.php';
session_start();

// Validate that the user is a manager
validateRole(1);


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "User deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
}

header('Location: manager_dashboard.php');
exit();
?>

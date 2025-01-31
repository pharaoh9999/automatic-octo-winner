<?php
include 'includes/function.php';
include 'includes/config.php';
//session_start();

// Validate that the user is a manager
validateRole(1);


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        // Fetch the user details
        $stmt = $conn->prepare("SELECT id, username, coins FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header('Location: manager_dashboard.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error fetching user: " . $e->getMessage();
        header('Location: manager_dashboard.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $coins = intval($_POST['coins']);

    try {
        // Update the user's details
        $stmt = $conn->prepare("UPDATE users SET username = :username, coins = :coins WHERE id = :user_id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "User updated successfully.";
        header('Location: manager_dashboard.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit User</h1>
        <?php if (isset($user)): ?>
        <form method="POST" action="edit_user.php">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo $user['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="coins">Coins</label>
                <input type="number" name="coins" id="coins" class="form-control" value="<?php echo $user['coins']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="manager_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>

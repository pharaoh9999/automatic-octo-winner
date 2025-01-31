<?php
// Include necessary files and start session
include 'includes/function.php';
include 'includes/config.php'; // Includes the database connection class
//session_start();

// Validate that the user is a manager
validateRole(1); // Assuming '1' is the role ID for managers

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Your theme's CSS -->
</head>
<body>
    <header class="bg-dark text-white p-3 mb-4">
        <div class="container">
            <h1 class="h3">Manager Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <nav>
                <a href="index.php" class="btn btn-primary btn-sm">Dashboard</a>
                <a href="allocate_coins.php" class="btn btn-info btn-sm">Allocate Coins</a>
                <a href="add_user.php" class="btn btn-warning btn-sm">Add User</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- User Management Section -->
        <section class="mb-5">
            <h2 class="mb-3">Manage Users</h2>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Coins</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("SELECT id, username, role_id, coins FROM users");
                        while ($row = $stmt->fetch()) {
                            $role = $row['role_id'] == 1 ? 'Manager' : 'User';
                            echo "<tr>
                                <td>{$row['username']}</td>
                                <td>$role</td>
                                <td>{$row['coins']}</td>
                                <td>
                                    <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                    <a href='delete_user.php?id={$row['id']}' class='btn btn-sm btn-danger'>Delete</a>
                                </td>
                            </tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='4' class='text-danger'>Error fetching users: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Coin Allocation Section -->
        <section class="mb-5">
            <h2 class="mb-3">Allocate Coins</h2>
            <form method="POST" action="allocate_coins.php">
                <div class="form-group">
                    <label for="user">Select User:</label>
                    <select name="user_id" id="user" class="form-control" required>
                        <?php
                        try {
                            $stmt = $conn->query("SELECT id, username FROM users WHERE role_id != 1");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['id']}'>{$row['username']}</option>";
                            }
                        } catch (PDOException $e) {
                            echo "<option disabled>Error fetching users</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="coins">Coins to Allocate:</label>
                    <input type="number" name="coins" id="coins" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-success">Allocate</button>
            </form>
        </section>

        <!-- Activity Logs Section -->
        <section>
            <h2 class="mb-3">User Activity Logs</h2>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>User ID</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("SELECT user_id, action, timestamp FROM activity_logs ORDER BY timestamp DESC");
                        while ($row = $stmt->fetch()) {
                            echo "<tr>
                                <td>{$row['user_id']}</td>
                                <td>{$row['action']}</td>
                                <td>{$row['timestamp']}</td>
                            </tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='3' class='text-danger'>Error fetching activity logs: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?php echo date('Y'); ?> Your Company. All rights reserved.</p>
    </footer>

    <!-- Bootstrap 4 JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$pdo->close();
?>

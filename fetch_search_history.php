<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

//session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized access']));
}

$user_id = $_SESSION['user_id'];

// Fetch pagination parameters
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
if ($page < 1) $page = 1;

$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Initialize the query and parameters
$conditions = ["user_id = :user_id"];
$params = [':user_id' => $user_id];

// Filter by start_date
if (!empty($_POST['start_date'])) {
    $conditions[] = "DATE(timestamp) >= :start_date";
    $params[':start_date'] = $_POST['start_date'];
}

// Filter by end_date
if (!empty($_POST['end_date'])) {
    $conditions[] = "DATE(timestamp) <= :end_date";
    $params[':end_date'] = $_POST['end_date'];
}

// Filter by keyword
if (!empty($_POST['keyword'])) {
    $conditions[] = "search_query LIKE :keyword";
    $params[':keyword'] = '%' . $_POST['keyword'] . '%';
}

// Build the query
$where_clause = implode(" AND ", $conditions);
$total_query = "SELECT COUNT(*) AS total FROM saved_searches WHERE $where_clause";
$query = "SELECT id, search_query, results, timestamp FROM saved_searches WHERE $where_clause LIMIT :limit OFFSET :offset";

// Prepare and execute the total count query
$total_stmt = $conn->prepare($total_query);
foreach ($params as $key => $value) {
    $total_stmt->bindValue($key, $value);
}
$total_stmt->execute();
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$total_pages = ceil($total_records / $records_per_page);

// Prepare and execute the paginated query
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response
echo json_encode([
    'data' => $data,
    'total_pages' => $total_pages,
    'current_page' => $page
]);

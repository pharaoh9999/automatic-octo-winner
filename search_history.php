<?php
require_once 'includes/config.php'; // Database connection
require_once 'includes/function.php'; // Utility functions

// Start session
//session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Define records per page
$records_per_page = 10;

// Get the current page number
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

// Calculate the offset
$offset = ($page - 1) * $records_per_page;

// Get the total number of records
$total_query = "SELECT COUNT(*) AS total FROM saved_searches WHERE user_id = :user_id";
$total_stmt = $conn->prepare($total_query);
$total_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$total_stmt->execute();
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);

// Fetch records for the current page
$query = "SELECT id, search_query, results, timestamp FROM saved_searches WHERE user_id = :user_id LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<?php include './includes/head.php' ?>

<div class="container-fluid">
  <!-- Sidebar Navigation -->
        <?php include 'includes/navbar.php' ?>

    <div class="row" style="margin-top: 80px;">
      

        <!-- Main Content Area -->
        <div class="col-md-12">
            <div class="main-content">
                <h1 class="h2 pb-2 mb-4 text-success border-bottom border-success">Your Search History</h1>

                <!-- Filters -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="keyword">Keyword</label>
                            <input type="text" name="keyword" class="form-control" placeholder="Search query...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Filter</button>
                </form>

                <!-- Dynamic Search Results -->
                <div id="search-results">
                    <!-- AJAX content will load here -->
                </div>
            </div>

        </div>
    </div>
</div>
<?php include './includes/scripts.php'; ?>

<script>
    function fetchSearchHistory(page = 1) {
        const startDate = $('input[name="start_date"]').val();
        const endDate = $('input[name="end_date"]').val();
        const keyword = $('input[name="keyword"]').val();

        $.ajax({
            url: 'fetch_search_history.php',
            type: 'POST',
            data: {
                page,
                start_date: startDate,
                end_date: endDate,
                keyword
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.error) {
                    alert(result.error);
                    return;
                }

                // Build table
                let tableHtml = `
                    <table class="table table-striped table-dark table-bordered border-success rounded">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Search Query</th>
                                <th>Date/Time</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                result.data.forEach((row, index) => {
                    tableHtml += `
                        <tr>
                            <td>${(page - 1) * 10 + (index + 1)}</td>
                            <td>${row.search_query}</td>
                            <td>${row.timestamp}</td>
                            <td>${row.type}</td>
                            <td>
                                <a href="view_results.php?search_id=${row.id}" class="btn btn-primary">View Results</a>
                                <a href="${row.results}" class="btn btn-success" download>Download</a>
                            </td>
                        </tr>
                    `;
                });

                tableHtml += `</tbody></table>`;

                // Pagination
                tableHtml += `<nav aria-label="Page navigation"><ul class="pagination justify-content-center">`;
                if (result.current_page > 1) {
                    tableHtml += `<li class="page-item"><a class="page-link" href="#" onclick="fetchSearchHistory(${result.current_page - 1}); return false;">&laquo;</a></li>`;
                }
                for (let i = 1; i <= result.total_pages; i++) {
                    tableHtml += `<li class="page-item ${i === result.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="fetchSearchHistory(${i}); return false;">${i}</a></li>`;
                }
                if (result.current_page < result.total_pages) {
                    tableHtml += `<li class="page-item"><a class="page-link" href="#" onclick="fetchSearchHistory(${result.current_page + 1}); return false;">&raquo;</a></li>`;
                }
                tableHtml += `</ul></nav>`;

                $('#search-results').html(tableHtml);
            },
            error: function() {
                alert('Failed to fetch search history. Please try again.');
            }
        });
    }

    // Load first page on document ready
    $(document).ready(function() {
        fetchSearchHistory();
    });
</script>
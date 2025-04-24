<?php
include "connection.php";

// Initialize sort variables
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
$orderBy = "ORDER BY b.date DESC"; // Default sorting

// Set ORDER BY clause based on sort parameter
switch ($sort) {
    case 'price_asc':
        $orderBy = "ORDER BY b.total_price ASC";
        break;
    case 'price_desc':
        $orderBy = "ORDER BY b.total_price DESC";
        break;
    case 'date_asc':
        $orderBy = "ORDER BY b.date ASC";
        break;
    case 'date_desc':
        $orderBy = "ORDER BY b.date DESC";
        break;
}

// Fetch booking details with search functionality
$searchCondition = "";
if (isset($_POST['search']) && !empty($_POST['text'])) {
    $searchText = mysqli_real_escape_string($connect, $_POST['text']);
    $searchCondition = " WHERE u.name LIKE '%$searchText%' OR w.name LIKE '%$searchText%' OR b.booking_id LIKE '%$searchText%' OR b.date LIKE '%$searchText%'";
}

$select_bookings = "SELECT 
    b.booking_id,
    u.user_id,                
    u.name AS user_name,
    w.workspace_id,           
    w.name AS workspace_name,
    b.status AS status,
    b.total_price,
    DATE_FORMAT(b.date, '%Y-%m-%d') AS booking_date
FROM 
    grad_proj.bookings b
JOIN 
    grad_proj.users u ON b.user_id = u.user_id
JOIN 
    grad_proj.rooms r ON b.room_id = r.room_id
JOIN 
    grad_proj.workspaces w ON r.workspace_id = w.workspace_id
$searchCondition
$orderBy";

$run_select = mysqli_query($connect, $select_bookings);

// If it's an AJAX request, return properly formatted table rows
if (isset($_POST['search'])) {
    if (mysqli_num_rows($run_select) > 0) {
        foreach ($run_select as $row) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                <td><a href="../profile.php?user_id=<?php echo $row['user_id']; ?>">
                        <?php echo htmlspecialchars($row['user_name']); ?>
                    </a></td>
                <td><a href="../workspace_details.php?ws_id=<?php echo $row['workspace_id']; ?>">
                        <?php echo htmlspecialchars($row['workspace_name']); ?>
                    </a></td>
                <td><span class="status-badge <?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span></td>
                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                <td><?php echo number_format($row['total_price'], 2); ?> EGP</td>
                <td><?php echo number_format($row['total_price'] * 0.15, 2); ?> EGP</td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td colspan="7" class="text-center">No bookings found</td></tr>';
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings List</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/users-list.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge.confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .page-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .search-wrapper {
            margin-bottom: 20px;
        }

        .search-wrapper input {
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid #ddd;
        }

        .sort-dropdown {
            margin-left: 10px;
        }

        .sort-dropdown .btn {
            border-radius: 20px;
            padding: 8px 15px;
        }

        .sort-icon {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title">Bookings List</h1>
                <div class="controls-container d-flex align-items-center">
                    <div class="search-wrapper flex-grow-1">
                        <input type="text" id="searchText" class="form-control"
                            placeholder="Search by user name, workspace name, booking ID or date...">
                    </div>
                    <div class="sort-dropdown">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Sort By
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item" href="?sort=price_asc">Price (Low to High)</a></li>
                                <li><a class="dropdown-item" href="?sort=price_desc">Price (High to Low)</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="?sort=date_asc">Booking Date (Earliest)</a></li>
                                <li><a class="dropdown-item" href="?sort=date_desc">Booking Date (Latest)</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User Name</th>
                                <th>Workspace Name</th>
                                <th>Status</th>
                                <th>Booking Date
                                    <?php if ($sort == 'date_asc'): ?>
                                        <i class="sort-icon">↑</i>
                                    <?php elseif ($sort == 'date_desc'): ?>
                                        <i class="sort-icon">↓</i>
                                    <?php endif; ?>
                                </th>
                                <th>Total Fees
                                    <?php if ($sort == 'price_asc'): ?>
                                        <i class="sort-icon">↑</i>
                                    <?php elseif ($sort == 'price_desc'): ?>
                                        <i class="sort-icon">↓</i>
                                    <?php endif; ?>
                                </th>
                                <th>Profit (15%)</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <?php
                            if (mysqli_num_rows($run_select) > 0) {
                                foreach ($run_select as $row) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                        <td><a href="../profile.php?user_id=<?php echo $row['user_id']; ?>">
                                                <?php echo htmlspecialchars($row['user_name']); ?>
                                            </a></td>
                                        <td><a href="../workspace_details.php?ws_id=<?php echo $row['workspace_id']; ?>">
                                                <?php echo htmlspecialchars($row['workspace_name']); ?>
                                            </a></td>
                                        <td><span
                                                class="status-badge <?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span></td>
                                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                        <td><?php echo number_format($row['total_price']); ?> EGP</td>
                                        <td><?php echo number_format($row['total_price'] * 0.15); ?> EGP</td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No bookings found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Dynamic search with debounce
            var searchTimeout;
            $("#searchText").on("input", function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    var searchText = $(this).val();
                    if (searchText.length === 0) {
                        // If search is empty, reload the full page to reset
                        location.reload();
                        return;
                    }

                    $.ajax({
                        url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                        type: "POST",
                        data: {
                            text: searchText,
                            search: true
                        },
                        beforeSend: function () {
                            $("#usersTable").html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        },
                        success: function (data) {
                            $("#usersTable").html(data);
                        },
                        error: function (xhr, status, error) {
                            console.error("Search error:", error);
                            $("#usersTable").html('<tr><td colspan="7" class="text-center">Error loading results</td></tr>');
                        }
                    });
                }.bind(this), 300);
            });
        });
    </script>
</body>

</html>
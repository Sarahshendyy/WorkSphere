<?php
// include "connection.php";
include "sidebar.php";

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
            $status_class = '';
            switch (strtolower($row['status'])) {
                case 'upcoming': $status_class = 'status-upcoming'; break;
                case 'ongoing': $status_class = 'status-ongoing'; break;
                case 'completed': $status_class = 'status-completed'; break;
                case 'canceled': $status_class = 'status-canceled'; break;
                default: $status_class = 'status-upcoming'; break;
            }
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['booking_id']) . '</td>';
            echo '<td><a href="../profile.php?user_id=' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['user_name']) . '</a></td>';
            echo '<td><a href="../workspace_details.php?ws_id=' . htmlspecialchars($row['workspace_id']) . '">' . htmlspecialchars($row['workspace_name']) . '</a></td>';
            echo '<td><span class="status-badge ' . $status_class . '">' . htmlspecialchars($row['status']) . '</span></td>';
            echo '<td>' . htmlspecialchars($row['booking_date']) . '</td>';
            echo '<td>' . number_format($row['total_price'], 2) . ' EGP</td>';
            echo '<td>' . number_format($row['total_price'] * 0.15, 2) . ' EGP</td>';
            echo '</tr>';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #071739;
            --secondary-color: #4B6382;
            --info-color: #A4B5C4;
            --light-color: #CDD5DB;
            --accent-warm: #A68868;
            --accent-light: #E3C39D;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'DM Sans', sans-serif;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .table-container {
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-container h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 12px;
            border: none;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--light-color);
        }

        .table tbody tr:hover {
            background-color: var(--light-color);
        }

        .table a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .table a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            min-width: 100px;
        }

        .status-upcoming { background-color: var(--info-color); color: var(--primary-color); }
        .status-ongoing { background-color: var(--accent-light); color: var(--primary-color); }
        .status-completed { background-color: var(--secondary-color); color: white; }
        .status-canceled { background-color: var(--accent-warm); color: white; }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
        }

        .search-wrapper {
            margin-bottom: 20px;
            position: relative;
        }

        .search-wrapper input {
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid var(--light-color);
            width: 100%;
            max-width: 300px;
            transition: all 0.3s ease;
        }

        .search-wrapper input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(75, 99, 130, 0.1);
        }

        .sort-dropdown select {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid var(--light-color);
            background-color: white;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sort-dropdown select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(75, 99, 130, 0.1);
        }

        .controls-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
          .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--primary-color);
            padding: 20px;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .toggle-sidebar {
            background: none;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .toggle-sidebar:hover {
            color: var(--accent-light);
        }

        .sidebar.collapsed .sidebar-header h3,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .toggle-sidebar {
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1002;
                background-color: var(--primary-color);
                border-radius: 50%;
                width: 40px;
                height: 40px;
            }

            .toggle-sidebar.collapsed {
                left: 20px;
            }

            .controls-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper input {
                max-width: 100%;
            }

            .sort-dropdown select {
                width: 100%;
            }
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .nav-link.active {
            background-color: var(--accent-warm);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <h2><i class="fas fa-calendar-check"></i> Bookings List</h2>
            
            <div class="table-container">
                <div class="controls-container">
                    <div class="search-wrapper">
                        <input type="text" id="searchText" class="form-control" placeholder="Search bookings...">
                    </div>
                    <div class="sort-dropdown">
                        <select class="form-select" id="sortSelect">
                            <option value="date_desc">Date (Newest First)</option>
                            <option value="date_asc">Date (Oldest First)</option>
                            <option value="price_desc">Price (High to Low)</option>
                            <option value="price_asc">Price (Low to High)</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Workspace</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Total Price</th>
                                <th>Admin Profit (20%)</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
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
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>
    <script>
        $(document).ready(function() {
            // Search functionality
            $("#searchText").on("keyup", function() {
                var searchText = $(this).val();
                var sortValue = $("#sortSelect").val();
                
                $.ajax({
                    url: "bookings_list.php",
                    method: "POST",
                    data: {
                        search: true,
                        text: searchText,
                        sort: sortValue
                    },
                    success: function(response) {
                        $("#bookingsTableBody").html(response);
                    }
                });
            });

            // Sort functionality
            $("#sortSelect").on("change", function() {
                var searchText = $("#searchText").val();
                var sortValue = $(this).val();
                
                $.ajax({
                    url: "bookings_list.php",
                    method: "POST",
                    data: {
                        search: true,
                        text: searchText,
                        sort: sortValue
                    },
                    success: function(response) {
                        $("#bookingsTableBody").html(response);
                    }
                });
            });
        });
    </script>
</body>

</html>

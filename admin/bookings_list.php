<?php
include "connection.php";
// include "sidebar.php";

// Initialize sort variables
$sort = 'date_desc';
if (isset($_POST['sort'])) {
    $sort = $_POST['sort'];
} elseif (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
}
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
        default:
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
    </style>
</head>

<body>
   <div class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="../img/logo.png" alt="Logo">
            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3): ?>
                <h3>Workspace Owner</h3>
            <?php else: ?>
                <h3>WorkSphere Admin</h3>
            <?php endif; ?>
        </div>
        <button class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <ul class="nav-menu">
        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3): ?>
            <!-- Workspace Owner Sidebar -->
            <li class="nav-item">
                <a href="../workspace/workspaces_dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'workspaces_dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../workspace/booking_overview.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'booking_overview.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Booking Overview</span>
                </a>
            </li>
         <li class="nav-item">
                <a href="../workspace/rooms_table.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'rooms_table.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Rooms Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../workspace/workspace-calendar.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'workspace-calendar.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    <span>Workspace Calendar</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../workspace/chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user profile-icon"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <form action="../workspace/connection.php" method="POST" style="margin:0;">
                    <button type="submit" name="logout" class="nav-link" style="width:100%;background:none;border:none;padding:0;text-align:left;display:flex;align-items:center;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        <?php elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 4): ?>
            <!-- Admin Sidebar -->
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="bookings_list.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings_list.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="workspaces_list.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'workspaces_list.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Workspaces</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="users_list.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users_list.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admins_list.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admins_list.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Admins</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="homee.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'homee.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="workspace_approval.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'workspace_approval.php' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i>
                    <span>Workspace Approval</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user profile-icon"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <form action="connection.php" method="POST" style="margin:0;">
                    <button type="submit" name="logout" class="nav-link" style="width:100%;background:none;border:none;padding:0;text-align:left;display:flex;align-items:center;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        <?php endif; ?>
    </ul>
</div>
    <!-- Main Content -->
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
                            <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Newest First)</option>
                            <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Oldest First)</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
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
                                <th>Admin Profit (15%)</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <?php
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
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                        <td><a href="../profile.php?user_id=<?php echo $row['user_id']; ?>">
                                                <?php echo htmlspecialchars($row['user_name']); ?>
                                            </a></td>
                                        <td><a href="../workspace_details.php?ws_id=<?php echo $row['workspace_id']; ?>">
                                                <?php echo htmlspecialchars($row['workspace_name']); ?>
                                            </a></td>
                                        <td><span class="status-badge <?php echo $status_class; ?>">
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
    <script>
    // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            // Check for saved state
            const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isSidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleBtn.classList.add('collapsed');
            }

            // Check if we're on mobile
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }

            toggleBtn.addEventListener('click', function() {
                if (isMobile) {
                    sidebar.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    toggleBtn.classList.toggle('collapsed');
                    
                    // Save state only for desktop
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                } else {
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                        toggleBtn.classList.add('collapsed');
                    }
                }
            });
        });
    

        // Search and sort functionality
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
    <script src="js/sidebar.js"></script>
</body>

</html>
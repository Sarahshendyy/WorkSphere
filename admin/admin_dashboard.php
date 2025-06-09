<?php
include "connection.php";
include "sidebar.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header("Location: login.php");
    exit();
}

// Handle room status updates
if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_query = "UPDATE bookings SET status = '$new_status' WHERE booking_id = '$booking_id'";
    $run_update = mysqli_query($connect, $update_query);
}

// Get all bookings for admin view
$bookings_query = "
    SELECT 
        b.booking_id, 
        u.name AS customer_name, 
        w.name AS workspace_name, 
        r.room_name, 
        b.date, 
        b.start_time, 
        b.end_time, 
        b.status, 
        b.total_price,
        b.total_price * 0.2 AS profit,
        p.payment_method,
        p.transaction_id
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN rooms r ON b.room_id = r.room_id
    JOIN workspaces w ON r.workspace_id = w.workspace_id
    LEFT JOIN payments p ON b.booking_id = p.booking_id
    ORDER BY b.date DESC, b.start_time DESC
";

$bookings_result = mysqli_query($connect, $bookings_query);

// Get booking statistics for all workspaces
$booking_statuses = ["ongoing", "canceled", "upcoming", "completed"];
$booking_counts = [];

foreach ($booking_statuses as $status) {
    $query = "SELECT COUNT(*) AS count FROM bookings WHERE status = '$status'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);
    $booking_counts[$status] = $row['count'];
}

// Get earnings statistics (20% of total_price from all bookings)
$earnings_query = "
    SELECT 
        w.workspace_id,
        w.name AS workspace_name,
        SUM(b.total_price) AS total_revenue,
        SUM(b.total_price) * 0.2 AS admin_profit
    FROM workspaces w
    LEFT JOIN rooms r ON w.workspace_id = r.workspace_id
    LEFT JOIN bookings b ON r.room_id = b.room_id
    GROUP BY w.workspace_id
    ORDER BY admin_profit DESC
";

$earnings_result = mysqli_query($connect, $earnings_query);

// Get total system earnings
$total_earnings_query = "SELECT SUM(total_price) * 0.2 AS total_profit FROM bookings";
$total_earnings_result = mysqli_query($connect, $total_earnings_query);
$total_earnings = mysqli_fetch_assoc($total_earnings_result)['total_profit'];

// Get all workspaces for management
$workspaces_query = "SELECT w.*, z.zone_name FROM workspaces w JOIN zone z ON w.zone_id = z.zone_id";
$workspaces_result = mysqli_query($connect, $workspaces_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .summary-card h5 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .summary-card h3 {
            color: var(--secondary-color);
            font-weight: 700;
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

        .chart-container {
            width: 48%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart-container h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-upcoming { background-color: var(--info-color); color: var(--primary-color); }
        .status-ongoing { background-color: var(--accent-light); color: var(--primary-color); }
        .status-completed { background-color: var(--secondary-color); color: white; }
        .status-canceled { background-color: var(--accent-warm); color: white; }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .table tbody tr:hover {
            background-color: var(--light-color);
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <!-- <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../assets/images/logo.png" alt="Logo">
                <h3>WorkSphere</h3>
            </div>
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="bookings_list.php" class="nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="workspaces_list.php" class="nav-link">
                    <i class="fas fa-building"></i>
                    <span>Workspaces</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="users_list.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admins_list.php" class="nav-link">
                    <i class="fas fa-user-shield"></i>
                    <span>Admins</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="chat.php" class="nav-link">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="connection.php?logout=1" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div> -->

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="summary-card text-center">
                        <h5>Total Earnings</h5>
                        <h3><?php echo number_format($total_earnings, 2); ?> EGP</h3>
                        <p class="text-muted">20% of all bookings</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card text-center">
                        <h5>Total Workspaces</h5>
                        <h3><?php echo mysqli_num_rows($workspaces_result); ?></h3>
                        <p class="text-muted">Registered spaces</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card text-center">
                        <h5>Upcoming Bookings</h5>
                        <h3><?php echo $booking_counts['upcoming']; ?></h3>
                        <p class="text-muted">Future reservations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card text-center">
                        <h5>Completed Bookings</h5>
                        <h3><?php echo $booking_counts['completed']; ?></h3>
                        <p class="text-muted">Past reservations</p>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="table-container">
                <h4><i class="fas fa-calendar-check"></i> Recent Bookings</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Workspace</th>
                                <th>Room</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Payment</th>
                                <th>Profit (20%)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = mysqli_fetch_assoc($bookings_result)) { ?>
                                <tr>
                                    <td><?php echo $booking['booking_id']; ?></td>
                                    <td><?php echo $booking['workspace_name']; ?></td>
                                    <td><?php echo $booking['room_name']; ?></td>
                                    <td><?php echo $booking['customer_name']; ?></td>
                                    <td><?php echo $booking['date']; ?></td>
                                    <td><?php echo $booking['start_time'] . ' - ' . $booking['end_time']; ?></td>
                                    <td>
                                        <?php 
                                        if ($booking['payment_method']) {
                                            echo $booking['payment_method'] . ' (' . $booking['transaction_id'] . ')';
                                        } else {
                                            echo 'Pending';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($booking['profit'], 2); ?> EGP</td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Charts -->
            <div class="dashboard-container">
                <div class="chart-container">
                    <h4><i class="fas fa-chart-pie"></i> Booking Statistics</h4>
                    <canvas id="bookingChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4><i class="fas fa-chart-bar"></i> Earnings by Workspace</h4>
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>

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

        // Define chart colors based on our palette
        const chartColors = {
            primary: '#071739',
            secondary: '#4B6382',
            info: '#A4B5C4',
            light: '#CDD5DB',
            accentWarm: '#A68868',
            accentLight: '#E3C39D'
        };

        // Booking Statistics Pie Chart
        var ctx1 = document.getElementById('bookingChart').getContext('2d');
        var bookingChart = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Ongoing', 'Canceled', 'Upcoming', 'Completed'],
                datasets: [{
                    data: [
                        <?php echo $booking_counts['ongoing']; ?>,
                        <?php echo $booking_counts['canceled']; ?>,
                        <?php echo $booking_counts['upcoming']; ?>,
                        <?php echo $booking_counts['completed']; ?>
                    ],
                    backgroundColor: [
                        chartColors.accentLight,
                        chartColors.accentWarm,
                        chartColors.info,
                        chartColors.secondary
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Earnings by Workspace Bar Chart
        var ctx2 = document.getElementById('earningsChart').getContext('2d');
        var earningsLabels = [];
        var earningsData = [];

        <?php 
        mysqli_data_seek($earnings_result, 0); // Reset pointer to beginning
        while ($earning = mysqli_fetch_assoc($earnings_result)): ?>
            earningsLabels.push("<?php echo $earning['workspace_name']; ?>");
            earningsData.push(<?php echo $earning['admin_profit']; ?>);
        <?php endwhile; ?>

        var earningsChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: earningsLabels,
                datasets: [{
                    label: 'Admin Profit (EGP)',
                    data: earningsData,
                    backgroundColor: chartColors.primary,
                    borderColor: chartColors.secondary,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.light
                        }
                    },
                    x: {
                        grid: {
                            color: chartColors.light
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
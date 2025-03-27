<?php
include "connection.php";

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
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .chart-container {
            width: 48%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-upcoming {
            background-color: #66b3ff;
            color: white;
        }
        .status-ongoing {
            background-color: #ffcc00;
            color: black;
        }
        .status-completed {
            background-color: #4caf50;
            color: white;
        }
        .status-canceled {
            background-color: #ff4d4d;
            color: white;
        }
    </style>
</head>
<body>

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
                    <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                    <tr>
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td><?php echo $booking['workspace_name']; ?></td>
                        <td><?php echo $booking['room_name']; ?></td>
                        <td><?php echo $booking['customer_name']; ?></td>
                        <td><?php echo date('M j, Y', strtotime($booking['date'])); ?></td>
                        <td><?php echo date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time'])); ?></td>
                        <td><?php echo $booking['payment_method'] ?? 'N/A'; ?></td>
                        <td><?php echo number_format($booking['profit'], 2); ?> EGP</td>
                        <td>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
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
                backgroundColor: ['#ffcc00', '#ff4d4d', '#66b3ff', '#4caf50']
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
                backgroundColor: '#6c757d'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
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
<?php
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

$bookings_query = "
    SELECT workspaces.*, 
           zone.zone_name, 
           rooms.images, 
           AVG(reviews.rating) AS avg_rating, 
           bookings.booking_id, 
           users.name AS customer, 
           rooms.room_name, 
           bookings.date, 
           bookings.start_time, 
           bookings.end_time, 
           bookings.status, 
           payments.amount AS total_price, 
           payments.payment_method
    FROM workspaces 
    INNER JOIN rooms ON workspaces.workspace_id = rooms.workspace_id
    INNER JOIN bookings ON rooms.room_id = bookings.room_id
    LEFT JOIN zone ON workspaces.zone_id = zone.zone_id
    LEFT JOIN reviews ON bookings.booking_id = reviews.booking_id
    LEFT JOIN users ON bookings.user_id = users.user_id
    LEFT JOIN payments ON bookings.booking_id = payments.booking_id
    WHERE workspaces.user_id = '$owner_id'
    GROUP BY bookings.booking_id
    ORDER BY bookings.date DESC
";

$bookings_result = mysqli_query($connect, $bookings_query);

$booking_statuses = ["ongoing", "canceled", "upcoming", "completed"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Overview</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Booking Overview</h2>
    <a href="workspaces_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Amount (EGP)</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['customer']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($booking['end_time']); ?></td>
                    <td>
                        <form method="POST" action="workspaces_dashboard.php" id="form_<?php echo $booking['booking_id']; ?>">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                            <select class="form-select" name="status" onchange="document.getElementById('form_<?php echo $booking['booking_id']; ?>').submit();">
                                <?php foreach ($booking_statuses as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo ($booking['status'] == $status) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($booking['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
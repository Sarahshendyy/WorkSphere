<?php
include "connection.php";

$owner_id = $_SESSION['user_id'];


if (isset($_POST["room_name"])) {
    $room_name = mysqli_real_escape_string($connect, $_POST['room_name']);
    $seats = mysqli_real_escape_string($connect, $_POST['seats']);
    $room_type = mysqli_real_escape_string($connect, $_POST['room_type']);
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $room_status = mysqli_real_escape_string($connect, $_POST['room_status']);

    
    $workspace_query = "SELECT workspace_id FROM workspaces WHERE user_id = '$owner_id' LIMIT 1";
    $workspace_result = mysqli_query($connect, $workspace_query);

    if ($workspace_result && mysqli_num_rows($workspace_result) > 0) {
        $workspace_row = mysqli_fetch_assoc($workspace_result);
        $workspace_id = $workspace_row['workspace_id'];

        
        $insert_query = "INSERT INTO rooms (workspace_id, room_name, seats, room_type, `p/hr`, room_status) 
                 VALUES ('$workspace_id', '$room_name', '$seats', '$room_type', '$price', '$room_status')";
        if (mysqli_query($connect, $insert_query)) {
            $room_id = mysqli_insert_id($connect); 

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $imagePaths = [];
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_tmp = $_FILES['images']['tmp_name'][$key];
                    $upload_dir = "img/";
                    $file_path = $upload_dir . basename($file_name);

                    if (move_uploaded_file($file_tmp, $file_path)) {
                        $imagePaths[] = $file_name;
                    }
                }

                if (!empty($imagePaths)) {
                    $image_string = implode(",", $imagePaths);
                    $update_image_query = "UPDATE rooms SET images = '$image_string' WHERE room_id = '$room_id'";
                    mysqli_query($connect, $update_image_query);
                }
            }

            header("Location: workspaces_dashboard.php");
            exit();
        } else {
            echo "Error adding room: " . mysqli_error($connect);
        }
    } else {
        echo "Error: No workspace found for this user.";
    }
}



if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_query = "UPDATE bookings SET status = '$new_status' WHERE booking_id = '$booking_id'";
    $run_update = mysqli_query($connect, $update_query);
    if ($run_update) {
        header("Location: workspaces_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connect);
    }
}


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
    INNER JOIN bookings ON rooms.room_id = bookings.room_id  -- Ensures only booked rooms are included
    LEFT JOIN zone ON workspaces.zone_id = zone.zone_id
    LEFT JOIN reviews ON bookings.booking_id = reviews.booking_id
    LEFT JOIN users ON bookings.user_id = users.user_id
    LEFT JOIN payments ON bookings.booking_id = payments.booking_id
    WHERE workspaces.user_id = '$owner_id'
    GROUP BY bookings.booking_id
    ORDER BY bookings.date DESC
";




$bookings_result = mysqli_query($connect, $bookings_query);

$workspaces_query = "SELECT * FROM `workspaces` WHERE `user_id` = '$owner_id'";
$workspaces_result = mysqli_query($connect, $workspaces_query);

$booking_statuses = ["ongoing", "canceled", "upcoming", "completed"];
$booking_counts = [];

foreach ($booking_statuses as $status) {
    $query = "SELECT COUNT(*) AS count FROM `bookings` 
              WHERE `room_id` IN (SELECT `room_id` FROM `rooms` WHERE `workspace_id` IN 
              (SELECT `workspace_id` FROM `workspaces` WHERE `user_id` = '$owner_id'))
              AND `status` = '$status'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);
    $booking_counts[$status] = $row['count'];
}


$earnings_query = "
    SELECT workspaces.*, 
           zone.zone_name, 
           rooms.images, 
           AVG(reviews.rating) AS avg_rating, 
           rooms.room_id, 
           rooms.room_name, 
           SUM(bookings.total_price) * 0.8 AS earnings
    FROM workspaces
    LEFT JOIN rooms ON workspaces.workspace_id = rooms.workspace_id
    LEFT JOIN zone ON workspaces.zone_id = zone.zone_id
    LEFT JOIN bookings ON rooms.room_id = bookings.room_id
    LEFT JOIN reviews ON bookings.booking_id = reviews.booking_id
    WHERE workspaces.user_id = '$owner_id'
    GROUP BY rooms.room_id
";

$earnings_result = mysqli_query($connect, $earnings_query);


$rooms_query = "SELECT r.*, GROUP_CONCAT(a.amenity SEPARATOR ', ') AS amenities 
                FROM `rooms` r 
                LEFT JOIN `amenities` a ON r.room_id = a.room_id
                WHERE r.workspace_id IN 
                (SELECT `workspace_id` FROM `workspaces` WHERE `user_id` = '$owner_id')
                GROUP BY r.room_id";
$rooms_result = mysqli_query($connect, $rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
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
        }
    </style>
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<body>

<h2>Workspace Owner Dashboard</h2>

<div class="table-container mt-4">
    <h4>Bookings Overview</h4>
    <form method="POST" action="workspaces_dashboard.php">
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
                    <th>Update Booking</th>
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
            <!-- Each row has its own form to update individually -->
            <form method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                <select class="form-select" name="status">
                    <?php foreach ($booking_statuses as $status): ?>
                        <option value="<?php echo $status; ?>" <?php echo ($booking['status'] == $status) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($status); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </td>
        <td><?php echo htmlspecialchars($booking['total_price']); ?></td>
        <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
        <td>
                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>

        </table>
    </form>
</div>


<div class="dashboard-container">

    
    <div class="chart-container">
        <h4>Booking Statistics</h4>
        <canvas id="bookingChart"></canvas>
    </div>

    
    <div class="chart-container">
        <h4>Earnings Per Room</h4>
        <canvas id="earningsChart"></canvas>
    </div>

</div>

<!-- Existing Rooms Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Room Name</th>
            <th>Seats</th>
            <th>Type</th>
            <th>Price/hr</th>
            <th>Images</th>
            <th>Room Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($room['room_name']); ?></td>
            <td><?php echo htmlspecialchars($room['seats']); ?></td>
            <td><?php echo htmlspecialchars($room['room_type']); ?></td>
            <td><?php echo htmlspecialchars($room['p/hr']); ?> EGP</td>
            <td>
                <?php
                $imageFiles = explode(',', $room['images']);
                foreach ($imageFiles as $image) {
                    echo "<img src='img/$image' width='50' height='50' style='margin-right:5px;'>";
                }
                ?>
            </td>
            <td><?php echo htmlspecialchars($room['room_status']); ?></td>
            <td>
                <a href="edit_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Add Room Button -->
<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addRoomModal">
    + Add Room
</button>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="workspaces_dashboard.php" enctype="multipart/form-data">
                
                    <div class="mb-3">
                        <label class="form-label">Room Name:</label>
                        <input type="text" name="room_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seats:</label>
                        <input type="number" name="seats" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Type:</label>
                        <input type="text" name="room_type" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price per Hour (EGP):</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Status:</label>
                        <select name="room_status" class="form-select" required>
                            <option value="available">Available</option>
                            <option value="ongoing">Not Available</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Room Images:</label>
                        <input type="file" name="images[]" class="form-control" multiple required>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Room</button>
                </form>
            </div>
        </div>
    </div>
</div>





<script>
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
        }
    });

    var ctx2 = document.getElementById('earningsChart').getContext('2d');
    var earningsLabels = [];
    var earningsData = [];

    <?php while ($earning = mysqli_fetch_assoc($earnings_result)): ?>
        earningsLabels.push("<?php echo $earning['room_name']; ?>");
        earningsData.push(<?php echo $earning['earnings']; ?>);
    <?php endwhile; ?>

    var earningsChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: earningsLabels,
            datasets: [{ label: 'Earnings (EGP)', data: earningsData, backgroundColor: '#66b3ff' }]
        }
    });
</script>

</body>
</html>

<?php
// include "connection.php";
include "mail.php";

$owner_id = $_SESSION['user_id'];

if (isset($_POST["room_name"])) {
    $room_name = mysqli_real_escape_string($connect, $_POST['room_name']);
    $seats = mysqli_real_escape_string($connect, $_POST['seats']);
    $type_id = mysqli_real_escape_string($connect, $_POST['type_id']); 
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $room_status = mysqli_real_escape_string($connect, $_POST['room_status']);

    $workspace_query = "SELECT workspace_id FROM workspaces WHERE user_id = '$owner_id' LIMIT 1";
    $workspace_result = mysqli_query($connect, $workspace_query);

    if ($workspace_result && mysqli_num_rows($workspace_result) > 0) {
        $workspace_row = mysqli_fetch_assoc($workspace_result);
        $workspace_id = $workspace_row['workspace_id'];

        $insert_query = "INSERT INTO rooms (workspace_id, room_name, seats, type_id, `p/hr`, room_status) 
                 VALUES ('$workspace_id', '$room_name', '$seats', '$type_id', '$price', '$room_status')";
        if (mysqli_query($connect, $insert_query)) {
            $room_id = mysqli_insert_id($connect); 

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $imagePaths = [];
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_tmp = $_FILES['images']['tmp_name'][$key];
                    $upload_dir = "";
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

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_query = "UPDATE bookings SET status = '$new_status' WHERE booking_id = '$booking_id'";
    $run_update = mysqli_query($connect, $update_query);
    
    if ($run_update) {
        // If status was changed to "completed", send the review email
        if ($new_status == 'completed') {
            // Get booking and user details
            $email_query = "SELECT `users`.`email`, `users`.`name` 
                           FROM `bookings` 
                           JOIN `users` ON `bookings`.`user_id` = `users`.`user_id` 
                           WHERE booking_id = '$booking_id'";
            $email_result = mysqli_query($connect, $email_query);
            
            if ($email_result && mysqli_num_rows($email_result) > 0) {
                $user_data = mysqli_fetch_assoc($email_result);
                $email = $user_data['email'];
                $user_name = $user_data['name'];

                // Compose the email
                $subject = "We Value Your Feedback!";
                $message = "
                    <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
                        <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                            <h1>We Value Your Feedback, $user_name!</h1>
                        </div>
                        <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                            <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$user_name</span>,</p>
                            <p style='color: #00000a;'>Thank you for choosing our workspace! We hope you had a great experience.</p>
                            <p style='color: #00000a;'>We would love to hear your feedback. Please take a moment to review your booking:</p>
                            <p style='text-align: center;'>
                                <a href='http://localhost/graduation/review.php?booking_id=$booking_id' 
                                   style='display: inline-block; padding: 10px 20px; background-color: #fda521; color: #fffffa; 
                                          text-decoration: none; font-weight: bold; border-radius: 5px;'>
                                    Leave a Review
                                </a>
                            </p>
                            <p style='color: #00000a;'>Thank you for your time!</p>
                            <p style='color: #00000a;'>Best regards,<br>Your Workspace Team</p>
                        </div>
                        <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                            <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                            <p style='color: #fffffa;'>Email: <a href='mailto:support@yourworkspace.com' style='color: #fda521;'>support@yourworkspace.com</a></p>
                        </div>
                    </body>
                ";
                
                // Set up the email
                $mail->setFrom('deskify0@gmail.com', 'Deskify');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;

                // Send the email
                if ($mail->send()) {
                    // Update the email sent flag
                    $update_email_flag = "UPDATE bookings SET `review-email` = 1 WHERE booking_id = '$booking_id'";
                    mysqli_query($connect, $update_email_flag);
                }
            }
        }
        
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
    <a href="booking_overview.php" class="btn btn-primary">View All Bookings</a>
</div>

<div class="table-container mt-4">
    <h4>Rooms Management</h4>
    <a href="rooms_table.php" class="btn btn-primary">Manage Rooms</a>
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

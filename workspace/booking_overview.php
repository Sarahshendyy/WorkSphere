<?php
//include "connection.php";
include "mail.php";

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
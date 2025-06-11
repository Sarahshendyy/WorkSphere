<?php
//include "connection.php";
include "../admin/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

$bookings_query = "SELECT workspaces.*, 
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
           bookings.total_price, 
           bookings.pay_method, 
           payments.amount AS paid_amount, 
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

$booking_statuses = ["ongoing", "canceled", "upcoming", "completed", "no-show"];

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

 
    $fetch_booking = "
        SELECT status, total_price, pay_method 
        FROM bookings 
        WHERE booking_id = '$booking_id'
    ";
    $booking_data = mysqli_fetch_assoc(mysqli_query($connect, $fetch_booking));
    $old_status = $booking_data['status'];
    $amount = $booking_data['total_price'];
    $pay_method = $booking_data['pay_method'];
    $update_query = "UPDATE bookings SET status = '$new_status' WHERE booking_id = '$booking_id'";
    $run_update = mysqli_query($connect, $update_query);

    if ($run_update) {

            if ($new_status == "ongoing" && $pay_method == "pay at host") {
            
            $workspace_query = "SELECT workspaces.workspace_id 
            FROM bookings 
            JOIN rooms ON bookings.room_id = rooms.room_id 
            JOIN workspaces ON rooms.workspace_id = workspaces.workspace_id 
            WHERE bookings.booking_id = '$booking_id'
            ";
            $workspace_result = mysqli_query($connect, $workspace_query);
            $workspace_data = mysqli_fetch_assoc($workspace_result);
            $workspace_id = $workspace_data['workspace_id'];
            $payment_check = mysqli_query($connect, "SELECT * FROM payments WHERE booking_id = '$booking_id'");
            if (mysqli_num_rows($payment_check) == 0) {
            $insert_payment = "INSERT INTO `payments` VALUES (NULL,'$booking_id','$amount','paid at host',NULL,NULL,'$workspace_id')";
            $run_insert=mysqli_query($connect,$insert_payment); 
            }
            }


    
        if ($new_status == 'completed') {
            $email_query = "SELECT users.email, users.name 
                            FROM bookings 
                            JOIN users ON bookings.user_id = users.user_id 
                            WHERE booking_id = '$booking_id'";
            $email_result = mysqli_query($connect, $email_query);
            
            if ($email_result && mysqli_num_rows($email_result) > 0) {
                $user_data = mysqli_fetch_assoc($email_result);
                $email = $user_data['email'];
                $user_name = $user_data['name'];

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
                                <a href='http://localhost/WorkSphere-main/review.php?booking_id=$booking_id' 
                                   style='display: inline-block; padding: 10px 20px; background-color: #fda521; color: #fffffa; 
                                          text-decoration: none; font-weight: bold; border-radius: 5px;'>
                                    Leave a Review
                                </a>
                            </p>
                            <p style='color: #00000a;'>Thank you for your time!</p>
                            <p style='color: #00000a;'>Best regards,<br>WorkSphere Team</p>
                        </div>
                        <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                            <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                            <p style='color: #fffffa;'>Email: <a href='mailto:worksphere04@gmail.com' style='color: #fda521;'>worksphere04@gmail.com</a></p>
                        </div>
                    </body>
                ";

                $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;

                if ($mail->send()) {
                    mysqli_query($connect, "UPDATE bookings SET `review-email` = 1 WHERE booking_id = '$booking_id'");
                }
            }
        }

        header("Location: booking_overview.php");
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
    <link rel="stylesheet" href="css/booking_overview.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Booking Overview</h2>
    
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
                        <form method="POST" id="form_<?php echo $booking['booking_id']; ?>">
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
                    <td><?php echo htmlspecialchars($booking['pay_method']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

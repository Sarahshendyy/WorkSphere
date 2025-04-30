<?php
// Include your database connection and mail files
include "mail.php";


// Check if booking_id is provided in the URL
if (!isset($_GET['booking_id'])) {
    die("Booking ID is missing.");
}

$bookingId = mysqli_real_escape_string($connect, $_GET['booking_id']);

// Fetch booking details
$query = "SELECT b.*, r.room_name, r.`p/hr`, r.workspace_id, w.name AS workspace_name, u.email AS user_email 
          FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          JOIN workspaces w ON r.workspace_id = w.workspace_id
          JOIN users u ON b.user_id = u.user_id
          WHERE b.booking_id = ?";
$stmt = $connect->prepare($query);
if (!$stmt) {
    die("Database error: " . $connect->error);
}

$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$bookingData = $result->fetch_assoc();

// Extract booking details
$workspaceName = $bookingData['workspace_name'];
$roomName = $bookingData['room_name'];
$date = $bookingData['date'];
$startTime = $bookingData['start_time'];
$endTime = $bookingData['end_time'];
$pricePerHour = $bookingData['p/hr'];
$userEmail = $bookingData['user_email']; // User's email from the database

// Calculate the number of hours booked
$startDateTime = new DateTime($startTime);
$endDateTime = new DateTime($endTime);
$interval = $startDateTime->diff($endDateTime);
$hoursBooked = $interval->h + ($interval->i / 60); // Convert minutes to hours

// Calculate the total amount
$totalAmount = $pricePerHour * $hoursBooked;
$transactionId = rand(10000, 99999);

if (isset($_POST['pay_at_host'])) {
    // Get the workspace_id from the booking data
    $workspaceId = $bookingData['workspace_id'];
    
    $insert = "INSERT INTO payments (booking_id, workspace_id, amount, payment_method, transaction_id, created_at) 
               VALUES ('$bookingId', '$workspaceId', '$totalAmount', 'Pay at Host', '$transactionId', NOW())";
    $run_insert = mysqli_query($connect, $insert);

    if ($run_insert) {
        // Send email to the user
        $subject = "Your Booking Confirmation";
        $message = "
            <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
                <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                    <h1>Your Booking is Confirmed!</h1>
                </div>
                <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                    <p style='color: #00000a;'>Dear Valued Customer,</p>
                    <p style='color: #00000a;'>Thank you for booking with Deskify! Below are the details of your booking:</p>
                    <p style='color: #00000a;'><strong>Workspace:</strong> $workspaceName</p>
                    <p style='color: #00000a;'><strong>Room:</strong> $roomName</p>
                    <p style='color: #00000a;'><strong>Date:</strong> $date</p>
                    <p style='color: #00000a;'><strong>Time:</strong> $startTime to $endTime</p>
                    <p style='color: #00000a;'><strong>Total Amount:</strong> $" . number_format($totalAmount, 2) . "</p>
                    <p style='color: #00000a;'><strong>Transaction ID:</strong> $transactionId</p>
                    <p style='color: #00000a;'>Please provide the above Transaction ID at the reception to access your booking.</p>
                    <p style='color: #00000a;'>If you have any questions, feel free to contact us.</p>
                    <p style='color: #00000a;'>Best regards,<br>The Deskify Team</p>
                </div>
                <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                    <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                    <p style='color: #fffffa;'>Email: <a href='mailto:deskify0@gmail.com' style='color: #fda521;'>deskify0@gmail.com</a></p>
                </div>
            </body>
        ";

        // Use your mail function to send the email
        $mail->setFrom('deskify0@gmail.com', 'Deskify');
        $mail->addAddress($userEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            header("Location: payment_success.php"); // Redirect to success page
            exit;
        } else {
            $error = "Payment was successful, but the email could not be sent.";
        }
    } else {
        $error = "Payment failed. Please try again.";
    }
}

// Handle Pay Online (redirect to payment page)
if (isset($_POST['pay_online'])) {
    header("Location: payment.php?booking_id=$bookingId");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff; /* White background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff; /* White container */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        /* Booking Details Section */
        .booking-details {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #e0e0e0; /* Light gray border */
            border-radius: 8px;
            background-color: #f9f9f9; /* Light gray background */
        }

        .booking-details h3 {
            margin-top: 0;
            font-size: 24px;
            color: #333333; /* Dark gray text */
        }

        .booking-details p {
            margin: 10px 0;
            font-size: 16px;
            color: #555555; /* Medium gray text */
        }

        .booking-details strong {
            color: #333333; /* Dark gray text */
        }

        /* Error Message */
        .error {
            color: #ff0000; /* Red text */
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Payment Buttons */
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .buttons button.pay-online {
            background-color: #007bff; /* Blue */
            color: white;
        }

        .buttons button.pay-online:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .buttons button.pay-at-host {
            background-color: #28a745; /* Green */
            color: white;
        }

        .buttons button.pay-at-host:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Display Booking Details -->
        <div class="booking-details">
            <h3>Booking Details</h3>
            <p><strong>Workspace:</strong> <?php echo $workspaceName; ?></p>
            <p><strong>Room:</strong> <?php echo $roomName; ?></p>
            <p><strong>Date:</strong> <?php echo $date; ?></p>
            <p><strong>Time:</strong> <?php echo $startTime . " to " . $endTime; ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
        </div>

        <!-- Payment Options -->
        <form method="POST">
            <div class="buttons">
                <button type="submit" name="pay_online" class="pay-online">Pay Online</button>
                <button type="submit" name="pay_at_host" class="pay-at-host">Pay at Host</button>
            </div>
        </form>

        <!-- Display Error Message -->
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
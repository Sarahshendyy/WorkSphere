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
    
    // Start transaction
    mysqli_begin_transaction($connect);
    
    try {
        // Update the bookings table with payment info
        $updateBooking = "UPDATE bookings 
                         SET total_price = '$totalAmount', 
                             pay_method = 'Pay at Host',
                             status = 'upcoming'
                         WHERE booking_id = '$bookingId'";
        $runUpdate = mysqli_query($connect, $updateBooking);
        
        if (!$runUpdate) {
            throw new Exception("Failed to update booking record.");
        }
        
        // Insert into payments table
        $insert = "INSERT INTO payments (booking_id, workspace_id, amount, payment_method, transaction_id, created_at) 
                   VALUES ('$bookingId', '$workspaceId', '$totalAmount', 'Pay at Host', '$transactionId', NOW())";
        $run_insert = mysqli_query($connect, $insert);
        
        if (!$run_insert) {
            throw new Exception("Failed to insert payment record.");
        }
        
        // Commit transaction
        mysqli_commit($connect);
        
        // Send email to the user
        $subject = "Your Booking Confirmation";
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #fff; color: #071739;'>
            <div style='background-color: #071739; padding: 28px 0 18px 0; text-align: left; color: #E3C39D;'>
                <h1 style='margin: 0 0 0 40px; font-size: 2.2rem; font-weight: bold; letter-spacing: 1px;'>Booking Confirmation</h1>
            </div>
            <div style='padding: 32px 40px 24px 40px; background-color: #fff; color: #071739; text-align: left;'>
                <p>Dear <span style='color: #A68868;'>Customer</span>,</p>
                <p>Thank you for booking with <b>WorkSphere</b>! Your booking has been confirmed. Here are your details:</p>
                <ul style='list-style: none; padding-left: 0; margin-bottom: 18px;'>
                    <li style='margin-bottom: 8px;'><strong>Workspace:</strong> <span style='color: #A68868;'>$workspaceName</span></li>
                    <li style='margin-bottom: 8px;'><strong>Room:</strong> <span style='color: #A68868;'>$roomName</span></li>
                    <li style='margin-bottom: 8px;'><strong>Date:</strong> <span style='color: #A68868;'>$date</span></li>
                    <li style='margin-bottom: 8px;'><strong>Time:</strong> <span style='color: #A68868;'>$startTime to $endTime</span></li>
                    <li style='margin-bottom: 8px;'><strong>Total Amount:</strong> <span style='color: #A68868;'>$" . number_format($totalAmount, 2) . "</span></li>
                    <li style='margin-bottom: 8px;'><strong>Transaction ID:</strong> <span style='color: #A68868;'>$transactionId</span></li>
                </ul>
                <p style='color: #A68868; margin-bottom: 18px;'>Please provide the above Transaction ID at the reception to access your booking.</p>
                <p>If you have any questions, feel free to contact us.</p>
                <p style='margin-top: 32px;'>Best regards,<br>The WorkSphere Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 18px 40px; text-align: left; color: #E3C39D;'>
                <p style='margin: 0 0 6px 0;'>For any questions, please contact:</p>
                <p style='margin: 0;'>Email: <a href='mailto:worksphere50@gmail.com' style='color: #A68868; text-decoration: underline;'>worksphere50@gmail.com</a></p>
            </div>
        </body>";

        // Use your mail function to send the email
        $mail->setFrom('worksphere50@gmail.com', 'WorkSphere');
        $mail->addAddress($userEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            // Instead of redirecting, set a flag to show the popup
            $showSuccessPopup = true;
        } else {
            $error = "Payment was successful, but the email could not be sent.";
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connect);
        $error = "Payment failed: " . $e->getMessage();
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
        :root {
            --deep-navy: #071739;
            --steel-blue: #4B6382;
            --warm-taupe: #A68868;
            --beige-cream: #E3C39D;
            --light-grayish: #CDD5DB;
            --font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --shadow-light: 0 4px 12px rgba(7, 23, 57, 0.15);
            --shadow-hover: 0 6px 16px rgba(7, 23, 57, 0.25);
        }

        /* General Styles */
        body {
            font-family: var(--font-family);
            background-color: var(--light-grayish);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow-light);
            max-width: 600px;
            width: 90%;
            margin: 100px auto;
            transition: box-shadow 0.3s ease;
        }

        .container:hover {
            box-shadow: var(--shadow-hover);
        }

        /* Booking Details Section */
        .booking-details {
            margin-bottom: 30px;
            padding: 25px;
            border: 2px solid var(--light-grayish);
            border-radius: 12px;
            background-color: #ffffff;
            position: relative;
        }

        .booking-details h3 {
            margin-top: 0;
            font-size: 28px;
            color: var(--deep-navy);
            margin-bottom: 25px;
            font-weight: 600;
        }

        .booking-details p {
            margin: 15px 0;
            font-size: 16px;
            color: var(--steel-blue);
            line-height: 1.6;
        }

        .booking-details strong {
            color: var(--deep-navy);
            font-weight: 600;
        }

        /* Error Message */
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fff5f5;
            border-radius: 8px;
            border: 1px solid #ffcdd2;
        }

        /* Payment Buttons */
        .buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            justify-content: center;
            padding: 20px;
            border-radius: 12px;
        }

        .buttons form {
            flex: 1;
            max-width: 250px;
        }

        .buttons button {
            width: 100%;
            padding: 16px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .buttons button.pay-online {
            background-color: var(--deep-navy);
            color: white;
            box-shadow: 0 4px 12px rgba(7, 23, 57, 0.2);
        }

        .buttons button.pay-online:hover {
            background-color: var(--steel-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(7, 23, 57, 0.3);
        }

        .buttons button.pay-at-host {
            background-color: var(--warm-taupe);
            color: white;
            box-shadow: 0 4px 12px rgba(166, 136, 104, 0.2);
        }

        .buttons button.pay-at-host:hover {
            background-color: var(--beige-cream);
            color: var(--deep-navy);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(166, 136, 104, 0.3);
        }

        /* Price Display */
        .total-amount {
            font-size: 24px;
            color: var(--deep-navy);
            font-weight: 600;
            margin: 20px 0;
            padding: 15px;
            background-color: var(--light-grayish);
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 80px auto;
            }

            .buttons {
                flex-direction: column;
                align-items: center;
                padding: 15px;
            }

            .buttons form {
                width: 100%;
                max-width: 100%;
            }

            .booking-details h3 {
                font-size: 24px;
            }
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--light-grayish);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--deep-navy);
            font-size: 18px;
            text-decoration: none;
            z-index: 1;
        }

        .close-btn:hover {
            background-color: var(--steel-blue);
            color: white;
            transform: rotate(90deg);
        }

        @media (max-width: 768px) {
            .close-btn {
                top: 10px;
                right: 10px;
                width: 28px;
                height: 28px;
                font-size: 16px;
            }
        }
    </style>
    <!-- Add Font Awesome for the X icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <div class="booking-details">
            <a href="javascript:history.back()" class="close-btn" title="Go back">
                <i class="fas fa-times"></i>
            </a>
            <h3>Booking Details</h3>
            <p><strong>Workspace:</strong> <?php echo htmlspecialchars($workspaceName); ?></p>
            <p><strong>Room:</strong> <?php echo htmlspecialchars($roomName); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($date); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($startTime); ?> to <?php echo htmlspecialchars($endTime); ?></p>
            <p><strong>Hours Booked:</strong> <?php echo number_format($hoursBooked, 1); ?> hours</p>
            <div class="total-amount">
                Total Amount: $<?php echo number_format($totalAmount, 2); ?>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="buttons">
            <form method="post" style="flex: 1;">
                <button type="submit" name="pay_online" class="pay-online">
                    <i class="fas fa-credit-card"></i> Pay Online
                </button>
            </form>
            <form method="post" style="flex: 1;">
                <button type="submit" name="pay_at_host" class="pay-at-host">
                    <i class="fas fa-building"></i> Pay at Host
                </button>
            </form>
        </div>
    </div>
    <?php if (isset($showSuccessPopup) && $showSuccessPopup): ?>
    <script>
        Swal.fire({
            title: 'Booking successful!',
            text: 'Redirecting to your bookings...',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            buttonsStyling: false
        }).then(() => {
            window.location.href = 'my_bookings.php';
        });
    </script>
    <?php endif; ?>
</body>
</html>
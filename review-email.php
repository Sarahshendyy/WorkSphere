<?php
include "mail.php";

// Fetch completed bookings that ended 1 day ago and have not been emailed yet
$query = "SELECT `bookings`.`booking_id`, `bookings`.`user_id`, `users`.`email`, `users`.`name` 
          FROM `bookings` 
          JOIN `users` ON `bookings`.`user_id` = `users`.`user_id` 
          WHERE `bookings`.`status` = 'completed' 
          AND `bookings`.`review-email` = 0";
$result = mysqli_query($connect, $query);
$num_rows = mysqli_num_rows($result);

echo "Number of completed bookings found: $num_rows<br>";

while ($row = mysqli_fetch_assoc($result)) {
    $booking_id = $row['booking_id'];
    $email = $row['email'];
    $user_name = $row['name'];

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
        echo "Email sent to $email for booking ID $booking_id.<br>";
        
        // Update the email_sent flag
        $update_query = "UPDATE `bookings` SET `review-email` = 1 WHERE `booking_id` = $booking_id";
        if (mysqli_query($connect, $update_query)) {
            echo "Email sent flag updated for booking ID $booking_id.<br>";
        } else {
            echo "Failed to update email sent flag for booking ID $booking_id. Error: " . mysqli_error($connect) . "<br>";
        }
    } else {
        echo "Failed to send email to $email for booking ID $booking_id. Error: " . $mail->ErrorInfo . "<br>";
    }
}
?>
<?php
include "mail.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    echo "<script>alert('Access Denied!'); window.location.href='indexx.php';</script>";
    exit();
}

$workspaces_query = "SELECT * FROM workspaces WHERE Availability = 1"; 
$result = mysqli_query($connect, $workspaces_query);

if (isset($_POST['approve'])) {
    $workspace_id = $_POST['workspace_id'];

    $owner_query = "SELECT users.email, users.name, users.user_id FROM workspaces 
                    JOIN users ON workspaces.user_id = users.user_id 
                    WHERE workspaces.workspace_id = '$workspace_id'";
    $owner_result = mysqli_query($connect, $owner_query);
    $owner = mysqli_fetch_assoc($owner_result);

    if ($owner) {
        $user_email = $owner['email'];
        $user_name = $owner['name'];
        $user_id = $owner['user_id'];

        // Update the user's role
        $update_role_query = "UPDATE users SET role_id = 3 WHERE user_id = '$user_id'";
        $update_workspace_query = "UPDATE workspaces SET Availability = 0 WHERE workspace_id = '$workspace_id'";

        if (mysqli_query($connect, $update_role_query) && mysqli_query($connect, $update_workspace_query)) {
            $subject = "🎉 Welcome On Board! Ready to Activate Your Workspace";
            $message = "
                <body style='font-family: Arial, sans-serif; background-color: #fffffa; color: #00000a;'>
                    <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                        <h1>Welcome to Deskify!</h1>
                    </div>
                    <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                        <p>Dear <strong>$user_name</strong>,</p>
                        <p>Congratulations! Your workspace listing has been approved. 🎉</p>
                        <p>To activate your workspace and start receiving bookings, please complete your payment.</p>
                        <p><a href='http://localhost/graduation/workspace_payment.php?user_id=$user_id' style='display: inline-block; padding: 10px 15px; background-color: #0a7273; color: white; text-decoration: none; border-radius: 5px;'>Proceed to Payment</a></p>
                        <p>Best regards,<br>The Deskify Team</p>
                    </div>
                </body>
            ";
            sendEmail($user_email, $subject, $message);
            header("Location: workspace_approval.php");
            exit();
        } else {
            echo "Error updating database: " . mysqli_error($connect);
        }
    } else {
        echo "Error fetching workspace owner: " . mysqli_error($connect);
    }
}



if (isset($_POST['decline'])) {
    $workspace_id = $_POST['workspace_id'];

    $owner_query = "SELECT users.email, users.name FROM workspaces 
                    JOIN users ON workspaces.user_id = users.user_id 
                    WHERE workspaces.workspace_id = '$workspace_id'";
    $owner_result = mysqli_query($connect, $owner_query);
    $owner = mysqli_fetch_assoc($owner_result);

    if ($owner) {
        $user_email = $owner['email'];
        $user_name = $owner['name'];

        $delete_query = "DELETE FROM workspaces WHERE workspace_id = '$workspace_id'";
        if (mysqli_query($connect, $delete_query)) {
            $subject = "⚠️ Workspace Request Declined";
            $message = "
                <body style='font-family: Arial, sans-serif; background-color: #fffffa; color: #00000a;'>
                    <div style='background-color: #dc3545; padding: 20px; text-align: center; color: #fffffa;'>
                        <h1>Workspace Request Declined</h1>
                    </div>
                    <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                        <p>Dear <strong>$user_name</strong>,</p>
                        <p>We regret to inform you that your workspace listing request has been declined.</p>
                        <p>If you have any questions or need further clarification, please <a href='mailto:deskify0@gmail.com' style='color: #dc3545;'>contact us</a>.</p>
                        <p>Best regards,<br>The Deskify Team</p>
                    </div>
                </body>
            ";
            sendEmail($user_email, $subject, $message);
            header("Location: workspace_approval.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    }
}

function sendEmail($to, $subject, $body) {
    global $mail;
    $mail->setFrom('deskify0@gmail.com', 'Deskify');
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Approve Workspaces</title>
    <link rel="stylesheet" href="./css/workspace-approval.css">
    <script>
        function confirmDelete(workspaceId) {
            if (window.confirm("⚠️ Are you sure you want to DECLINE this workspace to be DELETED permanently?")) {
                document.getElementById('decline-form-' + workspaceId).submit();
            }
        }
    </script>
</head>
<body>
    <h2>Pending Workspaces</h2>
    <?php while ($workspace = mysqli_fetch_assoc($result)): ?>
        <div class="workspace-container">
            <p><strong><?php echo htmlspecialchars($workspace['name']); ?></strong></p>
            <p>📍 Location: <?php echo htmlspecialchars($workspace['location']); ?></p>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="workspace_id" value="<?php echo $workspace['workspace_id']; ?>">
                <button type="submit" name="approve" class="approve-btn">✅ Approve</button>
            </form>

            <form method="POST" id="decline-form-<?php echo $workspace['workspace_id']; ?>" style="display:inline;">
                <input type="hidden" name="workspace_id" value="<?php echo $workspace['workspace_id']; ?>">
                <button type="button" onclick="confirmDelete(<?php echo $workspace['workspace_id']; ?>)" class="decline-btn">❌ Decline</button>
                <input type="hidden" name="decline">
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>
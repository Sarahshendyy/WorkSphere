<?php
include "../mail.php";


$workspaces_query = "SELECT 
        w.workspace_id, w.name, w.location, w.description, w.`price/hr`, w.latitude, w.longitude, w.created_at,
        z.zone_name,
        u.name AS owner_name, u.email AS owner_email, u.user_id AS owner_id
    FROM workspaces w
    JOIN users u ON w.user_id = u.user_id
    JOIN zone z ON w.zone_id = z.zone_id
    WHERE w.Availability = 1
";
$result = mysqli_query($connect, $workspaces_query);


if (isset($_GET['fetch_rooms']) && isset($_GET['workspace_id'])) {
    $workspace_id = intval($_GET['workspace_id']);
    $rooms_query = "SELECT room_name, seats, type_id, room_status, `images`, `p/hr`, `p/m` 
                    FROM rooms WHERE workspace_id = $workspace_id";
    $rooms_result = mysqli_query($connect, $rooms_query);
    $rooms = [];

    while ($room = mysqli_fetch_assoc($rooms_result)) {
        $rooms[] = $room;
    }

    header('Content-Type: application/json');
    echo json_encode($rooms);
    exit();
}


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

        $update_role_query = "UPDATE users SET role_id = 3 WHERE user_id = '$user_id'";
        $update_workspace_query = "UPDATE workspaces SET Availability = 0 WHERE workspace_id = '$workspace_id'";

        if (mysqli_query($connect, $update_role_query) && mysqli_query($connect, $update_workspace_query)) {
            $subject = "🎉 Welcome On Board! Ready to Activate Your Workspace";
            $message = "
                <body style='font-family: Arial, sans-serif; background-color: #fffffa; color: #00000a;'>
                    <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                        <h1>Welcome to WorkSphere!</h1>
                    </div>
                    <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                        <p>Dear <strong>$user_name</strong>,</p>
                        <p>Congratulations! Your workspace listing has been approved. 🎉</p>
                        <p>To activate your workspace and start receiving bookings, please complete your payment.</p>
                        <p><a href='http://localhost/graduation/workspace_payment.php?user_id=$user_id' style='display: inline-block; padding: 10px 15px; background-color: #0a7273; color: white; text-decoration: none; border-radius: 5px;'>Proceed to Payment</a></p>
                        <p>Best regards,<br>The WorkSphere Team</p>
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
                        <p>If you have any questions or need further clarification, please <a href='mailto:worksphere04@gmail.com' style='color: #dc3545;'>contact us</a>.</p>
                        <p>Best regards,<br>The WorkSphere Team</p>
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
    $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Approve Workspaces</title>
    <link rel="stylesheet" href="./css/workspace_approval.css" />
    <script>
        function confirmDelete(workspaceId) {
            if (window.confirm("⚠️ Are you sure you want to DECLINE this workspace to be DELETED permanently?")) {
                document.getElementById('decline-form-' + workspaceId).submit();
            }
        }

        function showRoomDetails(workspaceId) {
            fetch(`workspace_approval.php?fetch_rooms=1&workspace_id=${workspaceId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('roomDetails');
                    container.innerHTML = "";

                    if (data.length === 0) {
                        container.innerHTML = "<p>No rooms found for this workspace.</p>";
                    } else {
                        data.forEach(room => {
                            container.innerHTML += `
                                <div class="room-card">
                                    <p><strong>Name:</strong> ${room.room_name}</p>
                                    <p><strong>Seats:</strong> ${room.seats ?? 'N/A'}</p>
                                    <p><strong>Type ID:</strong> ${room.type_id ?? 'N/A'}</p>
                                    <p><strong>Status:</strong> ${room.room_status ?? 'N/A'}</p>
                                    <p><strong>Price/hr:</strong> $${room["p/hr"]}</p>
                                    <p><strong>Price/month:</strong> $${room["p/m"]}</p>
                                    ${room.images ? `<img src="../${room.images}" alt="Room Image" class="room-image" />` : ''}
                                </div>
                            `;
                        });
                    }

                    document.getElementById('roomPopup').style.display = 'flex';
                });
        }

        function closePopup() {
            document.getElementById('roomPopup').style.display = 'none';
        }
    </script>
</head>
<body>
    <h2>Pending Workspaces</h2>
    <?php while ($workspace = mysqli_fetch_assoc($result)): ?>
        <div class="workspace-container">
            <h3><?php echo htmlspecialchars($workspace['name']); ?></h3>
            <p><strong>Owner:</strong> <?php echo htmlspecialchars($workspace['owner_name']); ?> (<?php echo htmlspecialchars($workspace['owner_email']); ?>)</p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($workspace['location']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($workspace['description'])); ?></p>
            <p><strong>Zone:</strong> <?php echo htmlspecialchars($workspace['zone_name']); ?></p>
            <p><strong>Price per Hour:</strong> $<?php echo number_format($workspace['price/hr'], 2); ?></p>
            <p><strong>Coordinates:</strong> Lat: <?php echo htmlspecialchars($workspace['latitude']); ?>, Long: <?php echo htmlspecialchars($workspace['longitude']); ?></p>
            <p><strong>Listed At:</strong> <?php echo date("Y-m-d H:i", strtotime($workspace['created_at'])); ?></p>
            
            <button type="button" onclick="showRoomDetails(<?php echo $workspace['workspace_id']; ?>)" class="view-btn">📋 View Room Details</button>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="workspace_id" value="<?php echo $workspace['workspace_id']; ?>" />
                <button type="submit" name="approve" class="approve-btn">✅ Approve</button>
            </form>

            <form method="POST" id="decline-form-<?php echo $workspace['workspace_id']; ?>" style="display:inline;">
                <input type="hidden" name="workspace_id" value="<?php echo $workspace['workspace_id']; ?>" />
                <button type="button" onclick="confirmDelete(<?php echo $workspace['workspace_id']; ?>)" class="decline-btn">❌ Decline</button>
                <input type="hidden" name="decline" />
            </form>
        </div>
    <?php endwhile; ?>

    <div id="roomPopup" class="popup-overlay" style="display:none;">
        <div class="popup-content">
            <a href="#" class="close-btn" onclick="closePopup()">×</a>
            <h2>Room Details</h2>
            <div id="roomDetails"></div>
        </div>
    </div>
</body>
</html>

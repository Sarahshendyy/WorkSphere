<?php
include "connection.php"; 

if (!isset($_SESSION['user_id'])) {
    die("Session not set! <script>window.location.href='login.php';</script>");
}

if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']); 
    
    // First check if the room belongs to the user's workspace
    $check_query = "SELECT w.user_id 
                    FROM rooms r
                    JOIN workspaces w ON r.workspace_id = w.workspace_id
                    WHERE r.room_id = ?";
    $stmt = mysqli_prepare($connect, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['user_id'] != $_SESSION['user_id']) {
            // Room doesn't belong to this user
            die("<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'You are not authorized to delete this room.'
                    }).then(() => {
                        window.location.href='rooms_table.php';
                    });
                 </script>");
        }
    }
    
    // Check for bookings
    $booking_check = mysqli_query($connect, "SELECT COUNT(*) as cnt FROM bookings WHERE room_id = '$room_id'");
    $has_booking = mysqli_fetch_assoc($booking_check)['cnt'] > 0;
    
    if ($has_booking) {
        die("<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Cannot delete a room with active bookings.'
                }).then(() => {
                    window.location.href='rooms_table.php';
                });
             </script>");
    }

    // Proceed with deletion
    $delete_query = "DELETE FROM rooms WHERE room_id = ?";
    $stmt = mysqli_prepare($connect, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $room_id);

    if (mysqli_stmt_execute($stmt)) {
        // Also delete associated amenities
        $delete_amenities = mysqli_query($connect, "DELETE FROM amenities WHERE room_id = '$room_id'");
        
        header("Location: rooms_table.php?deleted=1");
        exit();
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error deleting room.'
                }).then(() => {
                    window.location.href='rooms_table.php';
                });
              </script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid request.'
            }).then(() => {
                window.location.href='rooms_table.php';
            });
          </script>";
}
?>
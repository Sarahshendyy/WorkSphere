<?php
include "connection.php"; 


if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']); 

  
    $delete_query = "DELETE FROM rooms WHERE room_id = ?";
    $stmt = mysqli_prepare($connect, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $room_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Room deleted successfully!'); window.location.href='rooms_table.php';</script>";
    } else {
        echo "<script>alert('Error deleting room.'); window.location.href='rooms_table.php';</script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Invalid request.'); window.location.href='rooms_table.php';</script>";
}
?>

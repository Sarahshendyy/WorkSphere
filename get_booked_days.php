<?php
include("connection.php");

$room_id = mysqli_real_escape_string($connect, $_GET['room_id']);
$start_date = mysqli_real_escape_string($connect, $_GET['start']);
$end_date = mysqli_real_escape_string($connect, $_GET['end']);

$query = "SELECT DISTINCT date FROM bookings 
          WHERE room_id = ? 
          AND date BETWEEN ? AND ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("iss", $room_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$booked_days = [];
while ($row = $result->fetch_assoc()) {
    $booked_days[] = $row['date'];
}

header('Content-Type: application/json');
echo json_encode(['booked_days' => $booked_days]);
?>
<?php
include("connection.php"); // Ensure this file defines $connect as a valid connection

$date = $_GET['date'] ?? '';
$roomId = $_GET['room_id'] ?? '';

if (empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Date is required.']);
    exit;
}

// Fetch booked time slots for the selected date and room
$query = "SELECT start_time, end_time FROM bookings WHERE room_id = ? AND date = ?";
$stmt = $connect->prepare($query);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $connect->error]);
    exit;
}

$stmt->bind_param("is", $roomId, $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedSlots = [];
while ($row = $result->fetch_assoc()) {
    $bookedSlots[] = $row;
}

echo json_encode(['status' => 'success', 'bookedSlots' => $bookedSlots]);
?>
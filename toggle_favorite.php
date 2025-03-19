<?php
include "connection.php";


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$workspace_id = $_POST['workspace_id'];

// Check if this workspace is already a favorite for this user
$check_query = "SELECT * FROM favourite WHERE user_id = '$user_id' AND workspace_id = '$workspace_id'";
$check_result = mysqli_query($connect, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // If already a favorite, remove it
    $delete_query = "DELETE FROM favourite WHERE user_id = '$user_id' AND workspace_id = '$workspace_id'";
    $result = mysqli_query($connect, $delete_query);
    $action = 'removed';
} else {
    // If not a favorite, add it
    $insert_query = "INSERT INTO favourite (user_id, workspace_id) VALUES ('$user_id', '$workspace_id')";
    $result = mysqli_query($connect, $insert_query);
    $action = 'added';
}

if ($result) {
    echo json_encode(['status' => 'success', 'action' => $action]);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
}
?>
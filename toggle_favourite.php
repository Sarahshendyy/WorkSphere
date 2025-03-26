<?php

include "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to favorite workspaces']);
    exit;
}

if (!isset($_POST['workspace_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Workspace ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$workspace_id = (int)$_POST['workspace_id'];

// Check if already favorited
$check_query = "SELECT * FROM favourite WHERE user_id = $user_id AND workspace_id = $workspace_id";
$check_result = mysqli_query($connect, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Remove from favorites
    $delete_query = "DELETE FROM favourite WHERE user_id = $user_id AND workspace_id = $workspace_id";
    if (mysqli_query($connect, $delete_query)) {
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove favorite']);
    }
} else {
    // Add to favorites
    $insert_query = "INSERT INTO favourite (user_id, workspace_id) VALUES ($user_id, $workspace_id)";
    if (mysqli_query($connect, $insert_query)) {
        echo json_encode(['status' => 'success', 'action' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add favorite']);
    }
}
?>
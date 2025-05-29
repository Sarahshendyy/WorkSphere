<?php
include '../../connection.php'; // Update path as needed

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    if (!isset($_POST['chat_id']) || !isset($_POST['new_message'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    $chat_id = $_POST['chat_id'];
    $new_message = $_POST['new_message'];

    try {
        // Update message
        $sql = "UPDATE chat SET message = ?, edited = 1 WHERE chat_id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$new_message, $chat_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
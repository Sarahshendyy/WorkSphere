<?php
include '../../connection.php';

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['message']) && isset($_POST['to_user'])) {
        // Initialize variables
        $message = mysqli_real_escape_string($connect, $_POST['message']);
        $to_id = (int)$_POST['to_user'];
        $from_id = (int)$_SESSION['user_id'];
        $date = date("Y-m-d H:i:s");
        $message_file = NULL;
        $upload_success = false;
        $file_name = '';

        // File upload handling
        if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // File upload directory - changed to 'files' folder
            $upload_dir = '../../../files/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Get file info
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_size = $_FILES['file']['size'];
            $file_type = $_FILES['file']['type'];
            
            // Sanitize file name
            $file_name = preg_replace("/[^A-Za-z0-9_.\-]/", '', $file_name);
            
            // Generate unique filename to prevent overwrites
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = uniqid('chat_', true) . '.' . $file_ext;
            $destination = $upload_dir . $unique_name;
            
            // Validate file size (max 5MB)
            $max_size = 5 * 1024 * 1024; // 5MB
            if ($file_size > $max_size) {
                die(json_encode(['error' => 'File size exceeds maximum limit of 5MB']));
            }
            
            // Validate file type
            $allowed_types = [
                'image/jpeg', 'image/png', 'image/gif',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain', 'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            
            if (!in_array($file_type, $allowed_types)) {
                die(json_encode(['error' => 'File type not allowed']));
            }
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $destination)) {
                $message_file = $unique_name;
                $upload_success = true;
            } else {
                die(json_encode(['error' => 'Failed to move uploaded file']));
            }
        }

        // Insert message into the chats table using prepared statement
        $sql = "INSERT INTO chat (from_user, to_user, message, opened, created_at,star, edited, file) 
                VALUES (?, ?, ?, 0, ?, 0,0, ?)";
        
        $stmt = mysqli_prepare($connect, $sql);
        $null = NULL;
        $file_param = $upload_success ? $message_file : $null;
        mysqli_stmt_bind_param($stmt, "iisss", $from_id, $to_id, $message, $date, $file_param);
        $res = mysqli_stmt_execute($stmt);
        
        if ($res) {
            // Check if this is the first conversation between them
            $sql2 = "SELECT * FROM `conversation` 
                    WHERE (user_1 = ? AND user_2 = ?) 
                    OR (user_2 = ? AND user_1 = ?)";
            
            $stmt2 = mysqli_prepare($connect, $sql2);
            mysqli_stmt_bind_param($stmt2, "iiii", $from_id, $to_id, $from_id, $to_id);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);

            // Setting up the time Zone
            define('TIMEZONE', 'Africa/Cairo');
            date_default_timezone_set(TIMEZONE);
            $time = date("h:i:s a");

            if (mysqli_num_rows($result2) == 0) {
                // Insert into conversations table
                $sql3 = "INSERT INTO `conversation` (user_1, user_2) VALUES (?, ?)";
                $stmt3 = mysqli_prepare($connect, $sql3);
                mysqli_stmt_bind_param($stmt3, "ii", $from_id, $to_id);
                mysqli_stmt_execute($stmt3);
            }
            
            // Return the chat message HTML
            ob_start();
            ?>
            <p class="rtext align-self-end border rounded p-2 mb-1">
                <?= htmlspecialchars($message) ?>
                <?php if ($upload_success): ?>
                    <br>
                    <a href="/files/chat_attachments/<?= htmlspecialchars($message_file) ?>" 
                       target="_blank" 
                       class="file-link">
                        <i class="fas fa-paperclip"></i> <?= htmlspecialchars($file_name) ?>
                    </a>
                <?php endif; ?>
                <small class="d-block"><?= htmlspecialchars($time) ?></small>          
            </p>
            <?php
            echo ob_get_clean();
        } else {
            // Error handling
            error_log("Database error: " . mysqli_error($connect));
            die(json_encode(['error' => 'Error sending message. Please try again.']));
        }
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>
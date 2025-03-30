<?php 

# Database connection file
include '../../connection.php';

# Check if the user is logged in
if (isset($_SESSION['user_id'])) {

    if (isset($_POST['id_2'])) {
    

        $id_1 = $_SESSION['user_id'];
        $id_2 = $_POST['id_2']; 
        $opend = 0;

        $sql = "SELECT * FROM chat
                WHERE to_user = $id_1
                AND from_user = $id_2
                ORDER BY chat_id ASC";
        $result = mysqli_query($connect, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($chat = mysqli_fetch_assoc($result)) {
                if ($chat['opened'] == 0) {
                    
                    $opened = 1;
                    $chat_id = $chat['chat_id'];

                    // Update chat status to opened
                    $sql2 = "UPDATE chat
                             SET opened = 1
                             WHERE chat_id = $chat_id";
                    mysqli_query($connect, $sql2);

                    ?>
                    <p class="ltext border 
                              rounded p-2 mb-1">
                        <?= htmlspecialchars($chat['message']) ?> 
                        <small class="d-block">
                            <?= htmlspecialchars($chat['created_at']) ?>
                        </small>       
                    </p>        
                    <?php
                }
            }
        }

    }

} else {
    header("Location: ../../login.php");
    exit;
}
?>

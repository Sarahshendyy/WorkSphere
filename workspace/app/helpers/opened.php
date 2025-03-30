<?php 

function opened($id_1, $connect, $chats){
    // Ensure $chats is an array, even if it's empty
    if (!is_array($chats)) {
        $chats = [];
    }

    foreach ($chats as $chat) {
        if ($chat['opened'] == 0) {
            $chat_id = $chat['chat_id'];

            $update_open = "UPDATE chat
                            SET opened = 1
                            WHERE from_user = $id_1 
                            AND chat_id = $chat_id";
            $run_open = mysqli_query($connect, $update_open);

            // Optionally check for errors
            if (!$run_open) {
                echo "Error updating chat: " . mysqli_error($connect);
            }
        }
    }
}

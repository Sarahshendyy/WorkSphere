<?php 

function getChats($id_1, $id_2, $connect){
    // Prepare the SQL query
    $select_chats = "SELECT * FROM chat
            -- WHERE (from_user = $id_1 AND to_user = $id_2)
            -- OR    (to_user = $id_1 AND from_user = $id_2)
            ORDER BY chat_id ASC";

    $result = mysqli_query($connect, $select_chats);

    if (mysqli_num_rows($result) > 0) {
        $chats = [];

        // Fetch all rows and store them in $chats array
        while($row = mysqli_fetch_assoc($result)) {
            $chats[] = $row;
        }

        return $chats; 
    }
}

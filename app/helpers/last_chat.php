<?php 

function lastChat($id_1, $id_2, $connect) {
    $sql = "SELECT * FROM chat
            WHERE (from_user=$id_1 AND to_user=$id_2)
            OR    (to_user=$id_1 AND from_user=$id_2)
            ORDER BY chat_id DESC LIMIT 1";

    $result = mysqli_query($connect, $sql);

    if (mysqli_num_rows($result) > 0) {
        $chat = mysqli_fetch_assoc($result);
        return $chat['message'];
    }
}
<?php 
$user_id = $_SESSION['user_id'];

function getConversation($user_id, $connect) {
    /**
      Getting all the conversations 
      for current (logged in) user
    **/
    $select_conversation = "SELECT * FROM `conversation`
            -- WHERE user_1='$user_id' OR user_2='$user_id'
            ORDER BY conversation_id DESC";

    $result = mysqli_query($connect,$select_conversation);

    if (mysqli_num_rows($result) > 0) {
        $conversations = mysqli_fetch_all($result, MYSQLI_ASSOC);

        /**
          creating empty array to 
          store the user conversation
        **/
        $user_data = [];

        foreach ($conversations as $conversation) {
            if ($conversation['user_1'] == $user_id) {
                $sql2  = "SELECT * FROM users WHERE user_id=" . $conversation['user_2'];
            } else {
                $sql2  = "SELECT * FROM users WHERE user_id=" . $conversation['user_1'];
            }

            $result2 = mysqli_query($connect, $sql2);
            
            if (mysqli_num_rows($result2) > 0) {
                $allConversations = mysqli_fetch_assoc($result2);
                array_push($user_data, $allConversations);
            }
        }

        return $user_data;

    }
}

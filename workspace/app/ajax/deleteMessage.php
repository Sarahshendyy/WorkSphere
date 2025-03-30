<?php
# database connection file
include '../../connection.php';

if(isset($_POST['chat_id'])){
    $chat_id = $_POST['chat_id'];

    $delete_message= "DELETE FROM `chat` WHERE `chat_id` = '$chat_id'";
    $run_delete_messgae = mysqli_query($connect,$delete_message);
    if($run_delete_messgae){ 
        echo 'success';  // Return success response for AJAX
    }
}
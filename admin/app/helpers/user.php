<?php  
$user_id = $_SESSION['user_id'];

function getUser($user_id, $connect) {
  // Ensure $user_id is sanitized
  $user_id = mysqli_real_escape_string($connect, $user_id);

  $select_user_data = "SELECT * FROM `users` WHERE `user_id` = $user_id";
  $run_select_user_data = mysqli_query($connect, $select_user_data);

  if (mysqli_num_rows($run_select_user_data) === 1) {
    $user = mysqli_fetch_assoc($run_select_user_data);
    return $user;
  }
}

<?php
include "connection.php";
header('Content-Type: application/json');

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $query = "SELECT * FROM `users` WHERE `email` = '$email'";
    $result = mysqli_query($connect, $query);
    
    echo json_encode([
        'exists' => mysqli_num_rows($result) > 0
    ]);
}
?>
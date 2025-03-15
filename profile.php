<?php
include "connection.php";


$user_id = $_SESSION['user_id'];


if (isset($_POST['list_workspace'])) {
    $update_role_query = "UPDATE users SET role_id = 3 WHERE user_id = $user_id";
    $RUN = mysqli_query($connect, $update_role_query);
    
    header("Location: listing_workspaces.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <h2>User Profile</h2>
    <form method="POST">
        <button type="submit" name="list_workspace">List a Workspace</button>
    </form>
</body>
</html>

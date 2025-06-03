<?php
include "connection.php";
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($_SESSION['user_id'])){
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='signup&login.php';</script>";
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<form method="post">
    <button class="" type="submit" name="logout">Logout</button>
</form>
</body>
</html>

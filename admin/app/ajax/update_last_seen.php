<?php

# database connection file
include '../../connection.php';

# check if the user is logged in
if (isset($_SESSION['user_id'])) {

	# get the logged in user's username from SESSION
	$id = $_SESSION['user_id'];

	$update_lastseen = "UPDATE users
	        SET last_seen = NOW() 
	        WHERE user_id = '$id' ";
	$run_update_lastseen = mysqli_query($connect,$update_lastseen);

}else {
	header("Location: ../../login.php");
	exit;
}
<?php
include "connection.php";
$user_id = $_SESSION['user_id'];
// display image
$select= "SELECT * FROM `users` WHERE `user_id` = $user_id";
$result = mysqli_query($connect, $select);
$fetch = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
	<!-- My CSS -->
	 
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/nav.css">

    
    <title>WorkSphere</title>
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="landing.php" class="brand">
			<i class='bx bxl-slack'></i>
			<span class="text">WorkSphere</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="profile.php">
					<i class='bx bxs-user-detail'></i>
					<span class="text">Profile</span>
				</a>
			</li>
			<li>
				<a href="chat.php">
					<i class='bx bx-chat'></i>
					<span class="text">Chat</span>
				</a>
			</li>
			<li>
				<a href="calendar.php">
				<i class='bx bxs-calendar' ></i>
					<span class="text">Calendar</span>
				</a>
			</li>
			<li>
				<a href="my_bookings.php">
					<i class='bx bxs-contact'></i>
					<span class="text">My Bookings</span>
				</a>
			</li>
			<li>
				<a href="community.php">
					<i class='bx bx-globe'></i>
					<span class="text">Our Community</span>
				</a>
			</li>

			<li>
				<a href="workspaces_list.php">
					<i class='bx bx-buildings' ></i>
					<span class="text">All Workspaces</span>

				</a>
			</li>
	</section>
	<!-- SIDEBAR -->
	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="landing.php" class="nav-link">Home</a>
			<input type="checkbox" id="switch-mode" hidden>
			<a href="profile.php" class="profile">
			
				<img src="./img/<?php echo $fetch['image'] ?>">
			</a>
		</nav>
<!-- <part of contant>	 -->

<main>
</main>
	    <!-- bootstrap js link -->
		<script src="js/bootstrap.min.js"></script>

	<script src="./js/script.js"></script>
</body>
</html>

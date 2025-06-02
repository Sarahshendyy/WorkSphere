<?php
include "nav.php";
$user_id = $_SESSION['user_id'];

// Function to display status
function displayStatus($status) {
    if ($status == 'upcoming') {
        return '<i class="fa-solid fa-circle"></i> Upcoming';
    } elseif ($status == 'ongoing') {
        return '<i class="fa-solid fa-arrows-rotate"></i> Ongoing';
    } elseif ($status == 'canceled') {
        return '<i class="fa-solid fa-circle-xmark"></i> Canceled';
    }elseif ($status == 'completed') {
        return '<i class="fa-solid fa-circle-check"></i> Completed';
    }
}
// Function to fetch bookings
function fetchBookings($connect, $user_id, $statusFilter) {
    $select_booking ="SELECT `bookings`.*, `rooms`.`room_name`, `rooms`.`workspace_id`, 
    `workspaces`.`name` AS `workspace_name`, `workspaces`.`location`
            FROM `bookings`
            JOIN `users` ON `bookings`.`user_id` = `users`.`user_id` 
            JOIN `rooms` ON `bookings`.`room_id` = `rooms`.`room_id`
            JOIN `workspaces` ON `rooms`.`workspace_id` = `workspaces`.`workspace_id`
            WHERE `bookings`.`user_id` = $user_id AND $statusFilter";
    $result_booking = mysqli_query($connect, $select_booking);
    return mysqli_fetch_all($result_booking, MYSQLI_ASSOC);
}

// Function to fetch room image
function fetchRoomImage($connect, $workspace_id) {
    $select_img = "SELECT `images`,`room_id` FROM `rooms` 
    WHERE `workspace_id` = $workspace_id ORDER BY `room_id` DESC LIMIT 1";
    $result_img = mysqli_query($connect, $select_img);
    return mysqli_fetch_assoc($result_img); 
}

// Handle Cancellation Request
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Update the status to 'Canceled'
    $update_query = "UPDATE bookings SET `status` = 'canceled' 
    WHERE `booking_id` = $booking_id AND `user_id` = $user_id";
    if (mysqli_query($connect, $update_query)) {
        echo "<script>alert('Booking canceled successfully.');</script>";
        // Refresh the page to reflect the changes
        echo "<script>window.location.href = 'my_bookings.php';</script>";
    } else {
        echo "<script>alert('Error canceling booking.');</script>";
    }
}

// Filter handling
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$statusFilter = "";
if ($filter == 'upcoming') {
    $statusFilter = "bookings.status = 'Upcoming'";
} elseif ($filter == 'ongoing') {
    $statusFilter = "bookings.status = 'Ongoing'";
} elseif ($filter == 'history') {
    $statusFilter = "bookings.status IN ('Canceled', 'Completed')";
} elseif ($filter == 'all') {
    $statusFilter = "bookings.status IN ('Upcoming', 'Ongoing', 'Canceled', 'Completed')";
}
$bookings = fetchBookings($connect, $user_id, $statusFilter);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <!-- links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./css/my_bookings.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script defer src="script.js"></script>
    <script>
        function validateCancellation(startTime, date, event) {
    const bookingDateTime = new Date(`${date} ${startTime}`).getTime();
    const currentDateTime = new Date().getTime();
    const timeDifference = bookingDateTime - currentDateTime;
    const fortyEightHoursInMs = 48 * 60 * 60 * 1000;

    if (timeDifference <= fortyEightHoursInMs) {
        alert("Cancellation is not permitted as the booking start time is within 48 hours. Please contact support for further assistance.");
        event.preventDefault(); // Prevent form submission
    } else {
        if (!confirm("Are you sure you want to cancel this booking?")) {
            event.preventDefault(); // Prevent form submission if user cancels the confirmation
        }
    }
}
    </script>
</head>

<body>
    <!-- filter -->
    <div class="booking-header">
        <ul>
            <li><a href="my_bookings.php?filter=all" class="<?php echo ($filter == 'all') ? 'active' : ''; ?>">All</a>
            </li>
            <li><a href="my_bookings.php?filter=upcoming"
                    class="<?php echo ($filter == 'upcoming') ? 'active' : ''; ?>">Upcoming</a></li>
            <li><a href="my_bookings.php?filter=ongoing"
                    class="<?php echo ($filter == 'ongoing') ? 'active' : ''; ?>">Ongoing</a></li>
            <li><a href="my_bookings.php?filter=history"
                    class="<?php echo ($filter == 'history') ? 'active' : ''; ?>">History</a></li>
        </ul>
    </div>

    <div class="booking-container">
        <?php 
    if (!empty($bookings)) {
        foreach ($bookings as $data) { 
            $workspace_id = $data['workspace_id'];
            $fetch_image = fetchRoomImage($connect, $workspace_id);
    ?>
        <!-- Booking Card -->
        <div class="booking-card">
            <!-- Booking Status -->
            <span class="status <?php echo strtolower($data['status']); ?>">
                <?php echo displayStatus($data['status']); ?>
            </span>

            <!-- Room Image and Details -->
            <div class="booking-details">
                <img src="./img/<?php echo $fetch_image['images']; ?>" alt="Room Image" class="room-image">
                <div class="room-info">
                    <a href="workspace_details.php?ws_id=<?php echo $data['workspace_id'];?>">
                        <h1 class="cssanimation typing " id="spicialstyle"><?php echo $data['workspace_name']; ?> </h1>
                    </a><br>


                    <a href="room_details.php?r_id=<?php echo $data['room_id'];?>">
                        <h2 class="cssanimation typing " id="spicialstyle"><?php echo $data['room_name']; ?></h2>
                    </a>
                    <p class="location"><i class="fa-solid fa-location-dot"></i> <?php echo $data['location']; ?>
                    </p>
                </div>
            </div>

            <!-- Time and Price Section -->
            <div class="time-price-container">
                <!-- Time Section -->
                <div class="time-section">
                    <p class="start-time">
                        <span class="dot blue"></span>
                        <?php echo date("D, d M Y h:i A", strtotime($data['date'] . " " . $data['start_time'])); ?>
                    </p>
                    <div class="dashed-line"></div>
                    <p class="end-time">
                        <span class="dot red"></span>
                        <?php echo date("D, d M Y h:i A", strtotime($data['date'] . " " . $data['end_time'])); ?>
                    </p>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <p class="users"><i class="fa-solid fa-users"></i> <?php echo $data['num_people']; ?></p>
                    <p class="price"><i class="fa-solid fa-coins"></i>
                        <?php echo number_format($data['total_price']); ?> <span>EGP</span></p>
                </div>
            </div>
            <!-- Cancel Button for Upcoming Bookings -->
            <?php if ($data['status'] == 'upcoming') { ?>
                <form method="POST" action="" onsubmit="validateCancellation('<?php echo $data['start_time']; ?>',
                 '<?php echo $data['date']; ?>', event);">
                    <input type="hidden" name="booking_id" value="<?php echo $data['booking_id']; ?>">
                    <button type="submit" name="cancel_booking" class="cancel-button">Cancel Booking</button>
                </form>
            <?php } ?>
        </div>
        <!-- Booking Card -->
        <?php 
            } 
        } else { 
            echo "<p>No bookings found.</p>";
        } 
        ?>
    </div>
   
</body>

</html>


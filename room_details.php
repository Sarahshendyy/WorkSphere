<?php
include "connection.php";

$room_id = mysqli_real_escape_string($connect, $_GET['r_id']);

$select_room = "SELECT rooms.*, GROUP_CONCAT(images SEPARATOR ', ') AS images,
                     workspaces.name AS workspace_name, 
                     workspaces.location, 
                     workspaces.description AS workspace_description
              FROM `rooms` 
              LEFT JOIN `workspaces` ON `rooms`.`workspace_id` = `workspaces`.`workspace_id`
              WHERE `rooms`.`room_id` = $room_id";

$run_select_room = mysqli_query($connect, $select_room);
$room = mysqli_fetch_assoc($run_select_room); // Fetch room details

$amenities = "SELECT * FROM `amenities` WHERE `room_id`=$room_id";
$run_am = mysqli_query($connect, $amenities);

$hourly_cost = $room['p/hr'] * 160;
$savings = round(($hourly_cost - $room['p/m']) / $hourly_cost * 100);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['room_name']; ?> - Room Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background-color: #f5f5f5;
        }

        .room-details-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;
            /* For positioning the close icon */
        }

        .close-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .close-icon:hover {
            color: #333;
        }

        .room-details-container h1 {
            font-size: 28px;
            font-weight: bold;
            color: #222;
            margin-bottom: 15px;
        }

        .room-content {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 20px;
        }

        .carousel-container {
            flex: 1;
            max-width: 55%;
        }

        .carousel-item img {
            width: 100%;
            height: 350px;
            /* Increased image height */
            object-fit: cover;
            border-radius: 10px;
        }

        .room-info {
            flex: 1;
            max-width: 40%;
        }

        .room-info h2 {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .room-info p {
            font-size: 16px;
            color: #555;
            margin: 8px 0;
        }

        .room-info strong {
            font-weight: bold;
            color: #222;
        }

        .amenities-list {
            margin-top: 15px;
        }

        .amenities-list p {
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        .amenities-list ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .amenities-list ul li {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #eef3f7;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            color: #333;
        }

        /* Updated Price Section Styles */
        .price-button-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 25px;
        }

        .price-wrapper {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .price-option {
            text-align: center;
        }

        .price-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .price {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .hour-price {
            color: #6c757d;
            /* Slightly muted for hourly */
        }

        .month-price {
            color: #28a745;
            /* Green for monthly to highlight better value */
            font-size: 24px;
            /* Slightly larger */
        }

     

        .location-icon {
            margin-right: 8px;
            color: #555;
        }

        .savings-badge {
            background: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            display: inline-block;
        }

        /* Updated Booking Buttons Styles */
        .booking-buttons {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .booking-button {
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .hourly-button {
            background-color: #6c757d;
            color: white;
        }

        .hourly-button:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .monthly-button {
            background-color: #28a745;
            color: white;
        }

        .monthly-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .savings-badge {
            background: #ffc107;
            color: #333;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            display: inline-block;
        }

        .price-option {
            position: relative;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
        }

        .price-option:nth-child(2) {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

    <div class="room-details-container">
        <!-- Close Icon -->
        <div class="close-icon" onclick="window.history.back()">
            <i class="fa-solid fa-xmark"></i>
        </div>

        <h1><?php echo $room['room_name']; ?></h1>

        <div class="room-content">
            <!-- Image Carousel -->
            <div class="carousel-container">
                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $images = explode(',', $room['images']);
                        foreach ($images as $index => $image) {
                            $activeClass = $index === 0 ? 'active' : '';
                            echo '<div class="carousel-item ' . $activeClass . '">
                                    <img src="./workspace/img/' . trim($image) . '" alt="Room Image">
                                  </div>';
                        }
                        ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>

            <!-- Room Information -->
            <div class="room-info">
                <h2>Workspace: <?php echo $room['workspace_name']; ?></h2>
                <p><i class="fa-solid fa-location-dot location-icon"></i><?php echo $room['location']; ?></p>
                <p><i class="fa-solid fa-users"></i> <strong>Seats:</strong> <?php echo $room['seats']; ?></p>
                <p><strong>Working Times:</strong> 09:00 AM - 11:59 PM</p>

                <!-- Amenities Section -->
                <div class="amenities-list">
                    <p>Amenities</p>
                    <ul>
                        <?php
                        foreach ($run_am as $row) {
                            echo '<li><i class="fa-solid fa-check"></i> ' . $row['amenity'] . '</li>';
                        }
                        ?>
                    </ul>
                </div>

                <!-- Price and Book Now Button -->
                <!-- Price and Book Now Button -->
                <!-- Replace your current price-button-container section with this: -->
                <div class="price-button-container">
                    <div class="price-wrapper">
                        <div class="price-option">
                            <div class="price-label">Hourly Rate</div>
                            <div class="price hour-price"><?php echo $room['p/hr']; ?> EGP</div>
                        </div>
                        <div class="price-option">
                            <div class="price-label">Monthly Rate</div>
                            <div class="price month-price"><?php echo $room['p/m']; ?> EGP</div>
                            <div class="savings-badge">
                                Save <?php echo $savings; ?>%
                            </div>
                        </div>
                    </div>

                    <div class="booking-buttons">
                        <a href="book_now.php?r_id=<?php echo $room['room_id'] ?>&type=hourly" class="btn-hourly">
                            <button class="booking-button hourly-button">
                                <i class="fa-regular fa-clock"></i> Book Hourly
                            </button>
                        </a>
                        <a href="book_monthly.php?r_id=<?php echo $room['room_id'] ?>&type=monthly" class="btn-monthly">
                            <button class="booking-button monthly-button">
                                <i class="fa-solid fa-piggy-bank"></i> Save with Monthly Booking
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
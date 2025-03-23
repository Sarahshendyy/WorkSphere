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

        .price-button-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            align-content: center;
            justify-content: flex-start;
        }

        .price-container {
            margin-bottom: 20px;
        }

        .price-container .price {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .price-container .price-label {
            font-size: 16px;
            color: #666;
        }

        .book-now-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            max-width: 200px;
            margin-left: auto;
        }

        .book-now-button:hover {
            background-color: #0056b3;
        }

        .location-icon {
            margin-right: 8px;
            color: #555;
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
                                    <img src="' . trim($image) . '" alt="Room Image">
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
                <div class="price-button-container">
                    <div class="price-container">
                        <div class="price"><?php echo $room['p/hr']; ?> EGP</div>
                        <div class="price-label">Per Hour</div>
                    </div>
                    <a href="book_now.php?r_id=<?php echo $room['room_id']?>"><button class="book-now-button">Book Now</button></a> 
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

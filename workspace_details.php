<?php
include "connection.php";

$ws_id = mysqli_real_escape_string($connect, $_GET['ws_id']);

$select_room = "SELECT rooms.*, 
                     zone.zone_name, 
                     workspaces.name,
                     workspaces.description,
                     workspaces.location,  
                     COALESCE(AVG(reviews.rating), 0) AS avg_rating
              FROM `rooms` 
              LEFT JOIN `workspaces` ON `rooms`.`workspace_id` = `workspaces`.`workspace_id`
              LEFT JOIN `zone` ON `workspaces`.`zone_id` = `zone`.`zone_id`
              LEFT JOIN `bookings` ON `rooms`.`room_id` = `bookings`.`room_id`
              LEFT JOIN `reviews` ON `bookings`.`booking_id` = `reviews`.`booking_id`
              WHERE `workspaces`.`workspace_id`=$ws_id
              GROUP BY rooms.room_id";

$run_select_room = mysqli_query($connect, $select_room);
$ws = mysqli_fetch_assoc($run_select_room); // Fetch workspace details once
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ws['name']?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .workspace-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative; /* For positioning the close icon */
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

        .workspace-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .workspace-header p {
            margin: 5px 0;
            color: #666;
        }

        .workspace-listings {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .workspace-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .workspace-item:last-child {
            border-bottom: none;
        }

        .workspace-item .carousel-container {
            width: 150px; /* Adjust width as needed */
            height: 100px; /* Adjust height as needed */
            margin-right: 15px;
        }

        .workspace-item .carousel-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .workspace-item .details {
            flex: 1;
        }

        .workspace-item h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .workspace-item p {
            margin: 5px 0;
            color: #666;
        }

        .workspace-item .price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .workspace-item .favorite-icon {
            cursor: pointer;
            font-size: 20px;
            color: #ccc;
            margin-left: 15px;
        }

        .workspace-item .favorite-icon.active {
            color: #ff4757;
        }

        .workspace-item .view-details-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px; /* Add some space above the button */
        }

        .workspace-item .view-details-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <!-- Workspace Header -->
    <div class="workspace-header">
        <!-- Close Icon on the Right -->
        <!-- <div class="close-icon" onclick="window.history.back()">
            <i class="fa-solid fa-xmark"></i>
        </div> -->

        <h1><?php echo $ws['name']; ?></h1>
        <p><i class="fa-solid fa-location-dot"></i> <?php echo $ws['location']; ?></p>
        <p>0.6 KM away</p>
        <p><?php echo $ws['description']; ?></p>
    </div>

    <!-- Workspace Listings -->
    <div class="workspace-listings">
        <h2>Workspace Listings</h2>
        <?php foreach ($run_select_room as $row) { ?>
            <!-- Workspace Item -->
            <div class="workspace-item">
                <!-- Carousel for Room Images -->
                <div class="carousel-container">
                    <div id="carousel-<?php echo $row['room_id']; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            $images = explode(',', $row['images']);
                            foreach ($images as $index => $image) {
                                $activeClass = $index === 0 ? 'active' : '';
                                echo '<div class="carousel-item ' . $activeClass . '">
                                        <img src="./img/' . trim($image) . '" alt="Room Image">
                                      </div>';
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $row['room_id']; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $row['room_id']; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="details">
                    <h3><?php echo $row['room_name']; ?></h3>
                    <p><strong> Seats:</strong> <?php echo $row['seats']; ?></p>
                    <!-- View Details Button -->
                    <a href="room_details.php?r_id=<?php echo $row["room_id"];?>">
                    <button class="view-details-btn">
                        View Details
                    </button>
                    </a>
                </div>
                <div class="price">
                    <?php echo $row['p/hr']; ?> EGP/Hour
                </div>
                <div class="favorite-icon" onclick="toggleFavorite(this)">
                    <i class="fa-regular fa-heart"></i> <!-- Heart icon for favoriting -->
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to toggle favorite icon
        function toggleFavorite(icon) {
            icon.classList.toggle('active');
            const heartIcon = icon.querySelector('i');
            if (icon.classList.contains('active')) {
                heartIcon.classList.remove('fa-regular');
                heartIcon.classList.add('fa-solid');
            } else {
                heartIcon.classList.remove('fa-solid');
                heartIcon.classList.add('fa-regular');
            }
        }
    </script>
</body>

</html>
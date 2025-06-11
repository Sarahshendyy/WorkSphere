<?php
include "nav.php";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/indexx.css" rel="stylesheet">
    <link href="css/nav.css" rel="stylesheet">
</head>

<body>
    <main>
        <section class="section-bg section-padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="bg-white p-4 p-md-5 rounded-4 shadow-lg position-relative">
                            <!-- Close Icon -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <a href="javascript:window.history.back()"
                                    class="btn btn-light btn-sm border rounded-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                            <div class="row g-4 align-items-center">
                                <!-- Image Carousel -->
                                <div class="col-md-6">
                                    <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner rounded-4 overflow-hidden">
                                            <?php
                                            if (!empty($room['images'])) {
                                                $images = explode(',', $room['images']);
                                                foreach ($images as $index => $image) {
                                                    $activeClass = $index === 0 ? 'active' : '';
                                                    $imagePath = trim($image);
                                                    // If the image path doesn't start with http or /, add the img/ directory
                                                    if (!preg_match('/^(http|\/)/', $imagePath)) {
                                                        $imagePath = 'img/' . $imagePath;
                                                    }
                                                    echo '<div class="carousel-item ' . $activeClass . '">
                                                            <img src="' . $imagePath . '" alt="Room Image" class="d-block w-100" style="height:350px;object-fit:cover;">
                                                          </div>';
                                                }
                                            } else {
                                                // Display a default image if no images are available
                                                echo '<div class="carousel-item active">
                                                        <img src="img/default-room.jpg" alt="Default Room Image" class="d-block w-100" style="height:350px;object-fit:cover;">
                                                      </div>';
                                            }
                                            ?>
                                        </div>
                                        <?php if (!empty($room['images']) && count($images) > 1): ?>
                                            <button class="carousel-control-prev" type="button"
                                                data-bs-target="#roomCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon"></span>
                                            </button>
                                            <button class="carousel-control-next" type="button"
                                                data-bs-target="#roomCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Room Information Section -->
                                <div class="col-md-6">
                                    <h1 class="fw-bold mb-3 color"><?php echo $room['room_name']; ?></h1>
                                    <h2 class="fs-4 fw-bold mb-2 color2">Workspace:
                                        <?php echo $room['workspace_name']; ?>
                                    </h2>

                                    <!-- Location -->
                                    <p class="mb-2"><i class="fa-solid fa-location-dot me-2 color2"></i><span
                                            class="fw-medium color"><?php echo $room['location']; ?></span></p>

                                    <!-- Seats -->
                                    <p class="mb-2"><i class="fa-solid fa-users me-2 color2"></i><span
                                            class="fw-medium color"><strong>Seats:</strong>
                                            <?php echo $room['seats']; ?></span></p>

                                    <!-- Price/Hour - Updated to match style -->
                                    <p class="mb-2"><i class="fa-solid fa-money-bill-wave me-2 color2"></i><span
                                            class="fw-medium color"><strong>Price/Hour:</strong>
                                            <?php echo $room['p/hr']; ?> EGP</span></p>

                                    <!-- Working Times - Updated to match style -->
                                    <p class="mb-2"><i class="fa-solid fa-clock me-2 color2"></i><span
                                            class="fw-medium color"><strong>Working Times:</strong> 09:00 AM - 11:00
                                            PM</span></p>

                                    <!-- Amenities Section -->
                                    <!-- Amenities Section -->
                                    <div class="mb-2">
                                        <p class="fw-bold color">Amenities</p>
                                        <ul class="list-unstyled d-flex flex-wrap gap-2">
                                            <?php
                                            foreach ($run_am as $row) {
                                                echo '<li class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 mb-1" style="background-color: rgb(205, 213, 219);">
                    <i class="fa-solid fa-check color2"></i> ' . $row['amenity'] . '
                  </li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <!-- Booking Button -->
                                    <div class="mt-4">
                                        <div class="d-flex gap-3">
                                            <a href="book_now.php?r_id=<?php echo $room['room_id'] ?>&type=hourly"
                                                class="btn flex-fill d-flex align-items-center justify-content-center gap-2"
                                                style="background-color: var(--deep-navy); color: white; border: none; padding: 12px 20px; border-radius: 8px; transition: all 0.3s ease;">
                                                <i class="fa-regular fa-clock"></i> Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
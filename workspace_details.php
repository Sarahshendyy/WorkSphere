<?php
// include "connection.php";
include "nav.php";

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
$ws = mysqli_fetch_assoc($run_select_room);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $ws['name']; ?> - Workspace Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #071739;
      --secondary-color: #4B6382;
      --info-color: #A4B5C4;
      --light-color: #CDD5DB;
      --accent-warm: #A68868;
      --accent-light: #E3C39D;
      --radius: 14px;
      --shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
      --font-family: 'Poppins', sans-serif;
    }

    body {
      font-family: var(--font-family);
      background-color: var(--light-color);
      margin: 0;
      padding: 20px;
    }

    .workspace-header {
      background: #fff;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 30px;
      margin-bottom: 40px;
      text-align: center;
      margin-top: 40px;
    }

    .workspace-header h1 {
      font-size: 28px;
      color: var(--primary-color);
    }

    .workspace-header p {
      font-size: 15px;
      color: var(--secondary-color);
      margin-bottom: 5px;
    }

    .workspace-header .rating {
      color: gold;
      font-weight: 500;
      font-size: 16px;
    }

    .room-container {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
    }

    .room-card {
      width: 300px;
      background-color: #fff;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .room-card:hover {
      transform: translateY(-5px);
    }

    .carousel-inner img {
      height: 180px;
      object-fit: cover;
    }

    .room-body {
      padding: 20px;
      text-align: center;
    }

    .room-body h3 {
      font-size: 18px;
      color: var(--primary-color);
      margin-bottom: 10px;
    }

    .room-body p {
      font-size: 14px;
      color: var(--secondary-color);
      margin-bottom: 6px;
    }

    .price-block {
      font-size: 15px;
      font-weight: 600;
      color: var(--accent-warm);
    }

    .view-btn {
      display: inline-block;
      background-color: var(--primary-color);
      color: white;
      padding: 8px 18px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 14px;
      margin-top: 12px;
      transition: background-color 0.3s ease;
    }

    .view-btn:hover {
      background-color: var(--accent-warm);
    }
  </style>
</head>
<body>

<div class="workspace-header">
  <h1><?php echo $ws['name']; ?></h1>
  <p><i class="fa-solid fa-location-dot"></i> <?php echo $ws['location']; ?> | <?php echo $ws['zone_name']; ?></p>
  <p><?php echo $ws['description']; ?></p>
  <p class="rating"><i class="fa-solid fa-star"></i> <?php echo number_format($ws['avg_rating'], 1); ?> / 5</p>
</div>

<div class="room-container">
  <?php mysqli_data_seek($run_select_room, 0); foreach ($run_select_room as $room): ?>
    <div class="room-card">
      <div id="carousel-<?php echo $room['room_id']; ?>" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          $images = explode(',', $room['images']);
          foreach ($images as $i => $img) {
            $active = $i === 0 ? 'active' : '';
            echo "<div class='carousel-item $active'><img src='./workspace/img/" . trim($img) . "' class='d-block w-100'></div>";
          }
          ?>
        </div>
        <?php if (count($images) > 1): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $room['room_id']; ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $room['room_id']; ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        <?php endif; ?>
      </div>
      <div class="room-body">
        <h3><?php echo $room['room_name']; ?></h3>
        <p><strong>Seats:</strong> <?php echo $room['seats']; ?></p>
        <p class="price-block"><?php echo $room['p/hr']; ?> EGP / hour</p>
        <p class="price-block"><?php echo $room['p/m']; ?> EGP / month</p>
        <a href="room_details.php?r_id=<?php echo $room['room_id']; ?>" class="view-btn">View Details</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

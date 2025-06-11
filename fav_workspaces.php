<?php
include "nav.php";
if(!isset($_SESSION['user_id'])){
     header("location: signup&login.php");
}
$user_id = $_SESSION['user_id'];



$query = "SELECT workspaces.*, 
                 zone.zone_name, 
                 rooms.images, 
                 COALESCE(AVG(reviews.rating), 0) AS avg_rating,
                 MIN(rooms.`p/hr`) AS starting_price
          FROM favourite
          JOIN workspaces ON favourite.workspace_id = workspaces.workspace_id
          JOIN rooms ON workspaces.workspace_id = rooms.workspace_id
          LEFT JOIN zone ON workspaces.zone_id = zone.zone_id
          LEFT JOIN bookings ON rooms.room_id = bookings.room_id
          LEFT JOIN reviews ON bookings.booking_id = reviews.booking_id
          WHERE favourite.user_id = '$user_id'
          GROUP BY workspaces.workspace_id";

$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Favorites</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/favorites.css">
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
      padding: 0;
    }

 

    .container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      padding-bottom: 60px;
    }

    .workspace-card {
      width: 300px;
      background: #fff;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      transition: transform 0.3s;
      position: relative;
      margin-top: 50px;
    }

    .workspace-card:hover {
      transform: translateY(-6px);
    }

    .carousel-inner img {
      height: 200px;
      object-fit: cover;
      border-radius: var(--radius) var(--radius) 0 0;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      transition: transform 0.2s;
    }

    .carousel-control-prev:hover .carousel-control-prev-icon,
    .carousel-control-next:hover .carousel-control-next-icon {
      transform: scale(1.2);
    }

    .card-body {
      text-align: center;
      padding: 20px;
    }

    .card-body h2 {
      font-size: 18px;
      color: var(--primary-color);
      margin-bottom: 6px;
      text-transform: capitalize;
    }

    .card-body h3 {
      font-size: 15px;
      color: var(--secondary-color);
      margin-bottom: 6px;
    }

    .card-body h4 {
      font-size: 14px;
      color: var(--accent-warm);
      margin-bottom: 6px;
    }

    .card-body p.rating {
      font-size: 13px;
      color: #555;
      margin-bottom: 10px;
    }

    .card-body p.rating i {
      color: gold;
    }

    .view-details {
      background: var(--primary-color);
      color: white;
      padding: 8px 16px;
      border-radius: 10px;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    .view-details:hover {
      background: var(--accent-warm);
    }

    .favorite-icon {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #ffffff;
  border-radius: 50%;
  padding: 6px;
  font-size: 18px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  cursor: pointer;
  color: var(--primary-color); /* Darker color */
  transition: 0.2s ease;
  z-index: 10;
}

.favorite-icon.active i {
  color: #ff4757;
}

.favorite-icon:hover {
  background-color: var(--accent-light);
  color: #ff4757;
}
  </style>
</head>
<body>
<div class="container">
<?php
if ($result && mysqli_num_rows($result) > 0) {
    foreach ($result as $index => $row) {
        $carouselId = "carousel" . $index;
?>
  <div class="workspace-card">
    <div class="favorite-icon active" onclick="toggleFavorite(this, <?php echo $row['workspace_id']; ?>)">
      <i class="fa-solid fa-heart"></i>
    </div>
    <div id="<?php echo $carouselId; ?>" class="carousel slide">
      <div class="carousel-inner">
        <?php
        $img_query = "SELECT `images` FROM `rooms` WHERE `workspace_id` = '{$row['workspace_id']}'";
        $run_img = mysqli_query($connect, $img_query);
        $first = true;
        while ($imag = mysqli_fetch_assoc($run_img)) {
            foreach (explode(',', $imag['images']) as $image) {
                echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">
                        <img src="./img/' . trim($image) . '" class="d-block w-100" alt="">
                      </div>';
                $first = false;
            }
        }
        ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
    <div class="card-body">
      <h2><?php echo htmlspecialchars($row['name']); ?></h2>
      <h3><?php echo htmlspecialchars($row['zone_name']); ?></h3>
      <h4>From <?php echo htmlspecialchars($row['starting_price']); ?> EGP/hour</h4>
      <p class="rating"><i class="fas fa-star"></i> <?php echo number_format($row['avg_rating'], 1); ?> / 5</p>
      <a href="workspace_details.php?ws_id=<?php echo $row['workspace_id']; ?>" class="view-details">View Details</a>
    </div>
  </div>
<?php }} else echo "<p class='text-center'>You have no favorite workspaces yet.</p>"; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
function toggleFavorite(icon, workspaceId) {
    $.post("toggle_favorite.php", { workspace_id: workspaceId }, function(response) {
        if (response.status === 'success') {
            icon.closest(".workspace-card").remove();
        } else {
            alert(response.message);
        }
    }, 'json');
}
</script>
</body>
</html>

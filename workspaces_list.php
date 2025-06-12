<?php
include "nav.php";

$order_by = "workspaces.workspace_id";
if (!empty($_POST['sort'])) {
  $sort_option = $_POST['sort'];
  if ($sort_option == 'highest_price')
    $order_by = "starting_price DESC";
  elseif ($sort_option == 'lowest_price')
    $order_by = "starting_price ASC";
  elseif ($sort_option == 'highest_rating')
    $order_by = "avg_rating DESC";
}

$select_ws = "SELECT workspaces.*, 
                     zone.zone_name, 
                     rooms.images, 
                     COALESCE(AVG(reviews.rating), 0) AS avg_rating,
                     MIN(rooms.`p/hr`) AS starting_price
              FROM `workspaces` 
              JOIN `rooms` ON `workspaces`.`workspace_id` = `rooms`.`workspace_id`
              LEFT JOIN `zone` ON `workspaces`.`zone_id` = `zone`.`zone_id`
              LEFT JOIN `bookings` ON `rooms`.`room_id` = `bookings`.`room_id`
              LEFT JOIN `reviews` ON `bookings`.`booking_id` = `reviews`.`booking_id`
              WHERE `Availability`=2
              GROUP BY workspaces.workspace_id
              ORDER BY $order_by";
$run_select_ws = mysqli_query($connect, $select_ws);

$run_select_search = null;
if (isset($_POST['search']) && !empty($_POST['text'])) {
  $text = mysqli_real_escape_string($connect, $_POST['text']);
  $select_search = "SELECT workspaces.*, 
                          zone.zone_name, 
                          rooms.images, 
                          COALESCE(AVG(reviews.rating), 0) AS avg_rating,
                          MIN(rooms.`p/hr`) AS starting_price
                     FROM `workspaces` 
                     JOIN `rooms` ON `workspaces`.`workspace_id` = `rooms`.`workspace_id`
                     LEFT JOIN `zone` ON `workspaces`.`zone_id` = `zone`.`zone_id`
                     LEFT JOIN `bookings` ON `rooms`.`room_id` = `bookings`.`room_id`
                     LEFT JOIN `reviews` ON `bookings`.`booking_id` = `reviews`.`booking_id`
                     WHERE (`workspaces`.`name` LIKE '%$text%' 
                        OR `workspaces`.`location` LIKE '%$text%' 
                        OR `zone`.`zone_name` LIKE '%$text%')
                        AND `Availability`=2
                     GROUP BY workspaces.workspace_id
                     ORDER BY $order_by";
  $run_select_search = mysqli_query($connect, $select_search);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Workspace Listings</title>
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
      padding: 0;
    }

    .search-sort-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
      padding: 20px;
      margin: 30px auto;
      max-width: 1000px;
    }

    .search-sort-container form {
      flex: 1;
      min-width: 220px;
    }

    .search-sort-container input,
    .search-sort-container select {
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid var(--info-color);
      width: 100%;
      background-color: #fff;
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
    }

    .workspace-card:hover {
      transform: translateY(-6px);
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
      color: var(--primary-color);
      transition: 0.2s ease;
      z-index: 10;
    }

    .favorite-icon.active {
      color: #ff4757;
    }

    .favorite-icon:hover {
      background-color: var(--accent-light);
      color: #ff4757;
    }

    .distance {
      font-size: 14px;
      color: var(--accent-warm);
      margin-top: 10px;
    }
  </style>
</head>

<body>

  <div class="search-sort-container">
    <form method="post">
      <input type="text" id="searchText" name="text" class="form-control" placeholder="Search by name or zone">
    </form>
    <form method="post">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="">Sort By</option>
        <option value="highest_price" <?= (!empty($_POST['sort']) && $_POST['sort'] == 'highest_price') ? 'selected' : '' ?>>Highest Price</option>
        <option value="lowest_price" <?= (!empty($_POST['sort']) && $_POST['sort'] == 'lowest_price') ? 'selected' : '' ?>>Lowest Price</option>
        <option value="highest_rating" <?= (!empty($_POST['sort']) && $_POST['sort'] == 'highest_rating') ? 'selected' : '' ?>>Highest Rating</option>
      </select>
    </form>
  </div>

  <div class="container">
    <?php
    $result_set = isset($_POST['search']) ? $run_select_search : $run_select_ws;
    if ($result_set && mysqli_num_rows($result_set) > 0) {
      foreach ($result_set as $index => $row) {
        $carouselId = "carousel" . $index;
        $is_favorite = false;
        if (isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $workspace_id = $row['workspace_id'];
          $check_query = "SELECT * FROM favourite WHERE user_id = '$user_id' AND workspace_id = '$workspace_id'";
          $check_result = mysqli_query($connect, $check_query);
          $is_favorite = mysqli_num_rows($check_result) > 0;
        }
        ?>
        <div class="workspace-card" data-latitude="<?php echo $row['latitude']; ?>"
          data-longitude="<?php echo $row['longitude']; ?>">
          <div class="favorite-icon <?= $is_favorite ? 'active' : '' ?>" onclick="toggleFavorite(this, <?= $row['workspace_id'] ?>)">
            <i class="<?= $is_favorite ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
          </div>
          <div id="<?= $carouselId ?>" class="carousel slide">
            <div class="carousel-inner">
              <?php
              $img_query = "SELECT `images` FROM `rooms` WHERE `workspace_id` = '{$row['workspace_id']}'";
              $run_img = mysqli_query($connect, $img_query);
              $first = true;
              while ($imag = mysqli_fetch_assoc($run_img)) {
                foreach (explode(',', $imag['images']) as $image) {
                  echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">
                        <img src="./workspace/img/' . trim($image) . '" class="d-block w-100" alt="">
                      </div>';
                  $first = false;
                }
              }
              ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>"
              data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>"
              data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
          <div class="card-body">
            <h2><?= htmlspecialchars($row['name']) ?></h2>
            <h3><?= htmlspecialchars($row['zone_name']) ?></h3>
            <h4>From <?= htmlspecialchars($row['starting_price']) ?> EGP/hour</h4>
            <p class="rating"><i class="fas fa-star"></i> <?= number_format($row['avg_rating'], 1) ?> / 5</p>
            <p class="distance"></p>
            <a href="workspace_details.php?ws_id=<?= $row['workspace_id'] ?>" class="view-details">View Details</a>
          </div>
        </div>
      <?php }
    } else {
      echo "<p class='text-center'>No workspaces found.</p>";
    }
    ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
    function toggleFavorite(icon, workspaceId) {
      <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = 'login.php';
        return;
      <?php endif; ?>
      
      $.ajax({
        url: "toggle_favorite.php",
        type: "POST",
        data: { workspace_id: workspaceId },
        dataType: "json",
        success: function(response) {
          if (response.status === 'success') {
            $(icon).toggleClass('active');
            var iconElement = $(icon).find('i');
            iconElement.toggleClass('fa-solid fa-regular');
          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert('Error processing your request');
        }
      });
    }

    $(document).ready(function () {
      $("#searchText").on("input", function () {
        var searchText = $(this).val();
        if (searchText === "") {
          location.reload();
          return;
        }

        $.ajax({
          url: "workspaces_list.php",
          type: "POST",
          data: { text: searchText, search: true },
          success: function (data) {
            var results = $(data).find('.workspace-card');
            $('.container').html(results);
          }
        });
      });

      // Get user location and calculate distances
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          var userLat = position.coords.latitude;
          var userLon = position.coords.longitude;

          $(".workspace-card").each(function () {
            var workspaceLat = $(this).data('latitude');
            var workspaceLon = $(this).data('longitude');

            var distance = haversine(userLat, userLon, workspaceLat, workspaceLon);
            $(this).find('.distance').text(distance + ' km away');
          });
        }, function () {
          console.log('Unable to retrieve your location.');
        });
      }

      function haversine(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = toRad(lat2 - lat1);
        var dLon = toRad(lon2 - lon1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c).toFixed(2);
      }

      function toRad(deg) {
        return deg * (Math.PI / 180);
      }
    });
  </script>
</body>
</html>
<?php
include "connection.php";

// $select_ws= " SELECT * FROM `workspaces` 
// LEFT JOIN `rooms` ON `workspaces`.`workspace_id` = `rooms`.`workspace_id`
// LEFT JOIN `bookings` ON `workspaces`.`workspace_id` = `bookings`.`workspace_id`
// LEFT JOIN `reviews` ON `bookings`.`booking_id` = `reviews`.`booking_id` ";

$select_ws = "SELECT workspaces.*, 
                     COALESCE(AVG(reviews.rating), 0) AS avg_rating
              FROM `workspaces` 
              LEFT JOIN `rooms` ON `workspaces`.`workspace_id` = `rooms`.`workspace_id`
              LEFT JOIN `bookings` ON `workspaces`.`workspace_id` = `bookings`.`workspace_id`
              LEFT JOIN `reviews` ON `bookings`.`booking_id` = `reviews`.`booking_id`
              GROUP BY workspaces.workspace_id";
$run_select_ws = mysqli_query($connect, $select_ws);
$fetch = mysqli_fetch_assoc($run_select_ws);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/workspaces.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


    


</head>
<body>
<?php 
foreach ($run_select_ws as $row) { ?>
  <div class="workspace-card">
  <div class="owl-carousel testimonial-carousel">
    <img src="./img/test.jpeg">
    <img src="./img/test1.png">
    <img src="./img/2.jpg">
    <img src="./img/3.jpg">
</div>
    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
    <h3><?php echo htmlspecialchars($row['zone']); ?></h3>
    <h3>Price/hr:</h3>
    <p><?php echo htmlspecialchars($row['price/hr']); ?> EGP</p>
    <h3>Rating:</h3>
    <p class="rating"><?php echo number_format($row['avg_rating'], 1); ?> / 5</p>
  </div>
<?php } ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>

<script>
   $(document).ready(function(){
    $(".testimonial-carousel").owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive:{
            0:{ items: 1 },
            600:{ items: 2 },
            1000:{ items: 3 }
        }
    });
});
$(window).on("load", function() {
    $(".testimonial-carousel").owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive:{
            0:{ items: 1 },
            600:{ items: 2 },
            1000:{ items: 3 }
        }
    });
});

</script>

</body>
</html>
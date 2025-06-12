<?php include 'connection.php';
?>

<!-- Top Header -->
 <!DOCTYPE html>
 <html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- links -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lancelot&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="./css/nav.css" rel="stylesheet">

 </head>
 <body>
  
 </body>
 </html>
<div class="top-header">
  <div class="contact d-flex align-items-center">
    <i class="fas fa-phone me-2"></i>010-967-74388
  </div>
  <div class="top-actions">
    <a href="about_us.php" class="angled-section get-touch">
      <i class="fas fa-comments me-2"></i> About Us
    </a>

    <?php if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3): ?>
      <a href="workspaces_list.php" class="angled-section book-tour">
        <i class="fas fa-calendar-alt me-2"></i> Book a Workspace
      </a>
    <?php endif; ?>


  </div>
  
</div>

<!-- Logo & Navigation bottom-->
<div class="logo-bar">
  <div class="logo">
    <a href="indexx.php">
      <img src="./img/logo.png" alt="Logo">
    </a>
    <!-- <p>WorkSohere</p> -->
  </div>
  <nav class="navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 bottom-nav">
       <?php
if (isset($_SESSION['role_id'])) {
    if ($_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4) {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : './indexx.php';
        echo '<li class="nav-item">
                <a class="nav-link" href="' . htmlspecialchars($referer) . '">
                    Return to My Interface
                </a>
              </li>';
    }
}
    ?>
        <li class="nav-item"><a class="nav-link" href="indexx.php">Home</a></li>

        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3): ?>
          <li class="nav-item"><a class="nav-link" href="./workspace/workspaces_dashboard.php">My Dashboard</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="workspaces_list.php">Workspaces</a></li>
        <li class="nav-item"><a class="nav-link" href="community.php">Community</a></li>

        <li class="nav-item dropdown">
          <span id="moreDropdown" class="nav-link more-link" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            More
          </span>
          <ul class="dropdown-menu more-dropdown-menu" aria-labelledby="moreDropdown">
            <li><a class="dropdown-item" href="chat.php">Chat</a></li>
            <li><a class="dropdown-item" href="calendar.php">Calendar</a></li>
           <li><a class="dropdown-item" href="fav_workspaces.php">Favourites</a></li>
          </ul>
        </li>
        <?php if(isset($_SESSION['user_id'])) { ?>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <i class="fas fa-user profile-icon"></i>
          </a>
        </li>
        <?php } else { ?>
        <li class="nav-item">
          <a class="nav-link" href="signup&login.php">Login</a>
        </li>
        <?php } ?>
        
      </ul>
    </div>
  </nav>
  <div id="scroll-line"></div>
  <script src="./js/nav.js"></script>
</div>

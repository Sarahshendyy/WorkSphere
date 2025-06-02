<?php include './connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- links -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="./css/nav.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Top Header -->
<div class="top-header">
  <div class="contact d-flex align-items-center">
    <i class="fas fa-phone me-2"></i>012-201-77444
  </div>
  <div class="top-actions">
    <a href="about_us.php" class="angled-section get-touch">
      <i class="fas fa-comments me-2"></i> About Us
    </a>
    <a href="workspaces_list.php" class="angled-section book-tour">
      <i class="fas fa-calendar-alt me-2"></i> Book a Workspace
    </a>
  </div>
  <div class="social-icons">
    <a href="#"><i class="fab fa-facebook-f"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-linkedin-in"></i></a>
  </div>
</div>

<!-- Logo & Navigation bottom-->
<div class="logo-bar">
  <div class="logo">
    <img src="./img/SCCI_Logo.png" alt="Logo">
    <p>WORKSPHERE</p>
  </div>
  <nav class="navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 bottom-nav">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
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

        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <i class="fas fa-user profile-icon"></i>
          </a>
        </li>
      </ul>
    </div>
  </nav>
  <div id="scroll-line"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/nav.js"></script>

 
</body>
</html>

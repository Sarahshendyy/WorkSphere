<?php 
include("nav.php");

$select_workspaces = "SELECT * FROM `workspaces`";
$run_workspaces = mysqli_query($connect, $select_workspaces);
$num_workspaces = mysqli_num_rows($run_workspaces);

$select_users = "SELECT * FROM `users`";
$run_users = mysqli_query($connect, $select_users);
$num_users = mysqli_num_rows($run_users);

$select_bookings = "SELECT * FROM `bookings`";
$run_bookings = mysqli_query($connect, $select_bookings);
$num_bookings = mysqli_num_rows($run_bookings);


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>WorkSphere</title>
  <!-- fontawesome link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!--  -->

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
<!--<link rel="icon" href="img/card-favorite.svg">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/css/boxicons.min.css" integrity="sha512-pVCM5+SN2+qwj36KonHToF2p1oIvoU3bsqxphdOIWMYmgr4ZqD3t5DjKvvetKhXGc/ZG5REYTT6ltKfExEei/Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap"
        rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="css/indexx.css" rel="stylesheet">


</head>

<body>

    <main>
        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">

            <div class="section-overlay"></div>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#071739" fill-opacity="1"
                    d="M0,224L34.3,192C68.6,160,137,96,206,90.7C274.3,85,343,139,411,144C480,149,549,107,617,122.7C685.7,139,754,213,823,240C891.4,267,960,245,1029,224C1097.1,203,1166,181,1234,160C1302.9,139,1371,117,1406,106.7L1440,96L1440,0L1405.7,0C1371.4,0,1303,0,1234,0C1165.7,0,1097,0,1029,0C960,0,891,0,823,0C754.3,0,686,0,617,0C548.6,0,480,0,411,0C342.9,0,274,0,206,0C137.1,0,69,0,34,0L0,0Z">
                </path>
            </svg>

            <div class="container my-5">
                <div class="row my-5">

                    <div class="col-lg-6  col-12  mb-lg-0 my-5">
                        <h2 class="text-white">Welcome </h2>

                       <h1 class="cd-headline rotate-1 text-white mb-4 pb-2">
    <span>WorkSphere is</span>
    <span class="cd-words-wrapper">
        <b class="is-visible">Flexible</b>
        <b>Efficient</b>
        <b>Innovative</b>
    </span>
</h1>
                        <?php if(!isset($_SESSION['user_id'])){ ?>
                        <div class="custom-btn-group">
                            <a href="./signup&login.php" class="btn custom-btn smoothscroll me-3">START</a>
                            <a href="./signup&login.php" class="link smoothscroll">Become a member</a>
                        </div>
                        <?php }else{ ?>
                        <div class="custom-btn-group">
                            <a href="./workspaces_list.php" class="btn custom-btn smoothscroll me-3">START</a>
                            <!-- <a href="./userprof.php" class="link smoothscroll">Become a member</a> -->
                        </div>
                        <?php } ?>
                    </div>

                    <div class="col-lg-6 col-12 mb-5 my-5">
                        <div class="ratio ratio-16x9">
                            <img src="./img/footer-graphic.png" alt="">
                        </div>
                    </div>

                </div>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#ffffff" fill-opacity="1"
                    d="M0,224L34.3,192C68.6,160,137,96,206,90.7C274.3,85,343,139,411,144C480,149,549,107,617,122.7C685.7,139,754,213,823,240C891.4,267,960,245,1029,224C1097.1,203,1166,181,1234,160C1302.9,139,1371,117,1406,106.7L1440,96L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                </path>
            </svg>
        </section>
<br> <br>
        <!-- beg of 1st features -->
     <section class="sec1 hiddensec mt-5 container mb-8 mb-lg-13" id="about">
  <div class="row align-items-center">
    <div class="col-12 col-lg-6 col-xl-7">
      <img class="img-fluid" src="img/g.jpeg" alt="Workspace Illustration" />
    </div>
    <div class="col-12 col-lg-6 col-xl-5">
      <div class="row justify-content-center justify-content-lg-start">
        <div class="col-sm-10 col-md-8 col-lg-12">
          <h2 class="fs-4 fs-lg-3 fw-bold mb-2 text-lg-start">
            Book Workspaces Anytime, Anywhere
          </h2>
          <p class="fs-8 mb-4 mb-lg-5 lh-lg text-lg-start fw-normal">
            Find and book coworking spaces, offices, and meeting rooms—fast and easy.
          </p>
        </div>
        <div class="col-sm-10 col-md-8 col-lg-12">
          <div class="mb-x1 mb-lg-3">
            <h5 class="fs-8 fw-bold lh-lg mb-1">Flexible Options</h5>
            <p class="mb-0 lh-xl">
              Choose from coworking spaces or meeting rooms—whatever fits your needs.
            </p>
          </div>
          <div>
            <h5 class="fs-8 fw-bold lh-lg mb-1">Affordable & Easy</h5>
            <p class="lh-xl mb-0">
              No contracts. No hassle. Just quick, affordable workspace bookings.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

        <!-- end of 1st features -->
        <section class="bg-color">
            <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <path fill="rgba(255, 255, 255, 1)" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z"
                    stroke-width="0"></path>
                <path fill="rgba(255, 255, 255, 1)" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z"
                    stroke-width="0"></path>
                <path fill="rgba(255, 255, 255, 1)" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z"
                    stroke-width="0"></path>
                <path fill="rgba(255, 255, 255, 1)"
                    d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
                <path fill="rgba(255, 255, 255, 1)" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z"
                    stroke-width="0"></path>
            </svg>


            <!-- beg of 2nd features -->
            <div class="container2">
                <section class=" container hiddensec mb-8 mb-lg-13">
                    <div class="row align-items-center">

                        <div class="col-12 col-lg-6 col-xl-5 order-lg-1 ">
                            <img class=" imgfeat2 img-fluid widthimg ms-auto" src=src="./img/work.jpg"
                                alt="" />
                        </div>

                        <div class="col-12 col-lg-6 col-xl-7">
    <div class="row justify-content-center justify-content-lg-start">
        <div class="col-sm-10 col-md-8 col-lg-11">
            <h2 class="fs-4 fs-lg-3 fw-bold mb-2 text-center text-white text-lg-start">Find and Book Workspaces in Seconds</h2>
            <p class="fs-8 mb-4 mb-lg-5 lh-lg text-center text-white text-lg-start fw-normal">Discover flexible workspaces, from coworking spaces to private offices, and book them with just a few clicks.</p>
        </div>
        <div class="col-sm-10 col-md-8 col-lg-12">
            <div class="mb-x1 mb-lg-3">
                <h5 class="fs-8 fw-bold text-white lh-lg mb-1">Access Workspaces Anytime, Anywhere</h5>
                <p class="b-0 lh-xl text-white">Book spaces that match your schedule and location, with no long-term commitments.</p>
            </div>
            <div>
                <h5 class="fs-8 fw-bold lh-lg mb-1 text-white">Easy Booking and Payment</h5>
                <p class="lh-xl mb-0 text-white">Our platform streamlines the booking process, making it simple and fast to reserve a workspace and handle payments.</p>
            </div>
        </div>
    </div>
</div>

                    </div>
                </section>
            </div>
            <!-- end of 2nd features -->



        </section>

        <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path fill="#071739" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
            <path fill="#071739" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0">
            </path>
            <path fill="#071739" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0">
            </path>
            <path fill="#071739" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z"
                stroke-width="0"></path>
            <path fill="#071739" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0">
            </path>
        </svg>




        <!-- how does it work -->
        <section class="container hiddensec">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h3 class="fs-1 fs-lg-3 fw-bold text-center mb-2 mb-lg-x1"> How does <span class="text-nowrap">WorkSphere work?</span></h3>
            <p class="fs-5 mb-7 mb-lg-8 text-center lh-lg">Follow these simple steps to find, book, and manage your workspace seamlessly on WorkSphere.</p>
        </div>
        <div class="col-12">
            <div class="row g-sm-2 g-lg-3 align-items-center timeline">
                <div class="col-12 col-lg-4 d-flex flex-row flex-lg-column justify-content-center gap-2 gap-sm-x1 gap-md-4 gap-lg-0 align-items-center">
                    <div class="timeline-step-1 w-25 w-lg-100 mb-4 mb-lg-5 mb-xl-6">
                        <div class="timeline-item d-flex justify-content-center">
                            <div style="width: 50%; border-radius: 50%;" class="timeline-icon bg-primary rounded-circle d-flex justify-content-center align-items-center">
                                <span class="fs-3 fs-lg-5 fs-xl-4 text-white"> 1</span>
                            </div>
                        </div>
                    </div>
                    <div class="py-1 py-lg-0 px-lg-5 w-75 w-sm-50 w-lg-100 timeline-content">
                        <h6 class="fs-3 fw-bold text-lg-center lh-lg mb-2 text-nowrap">Sign Up on WorkSphere</h6>
                        <p class="text-lg-center lh-xl mb-0">Create an account and gain instant access to a wide range of workspaces.</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex flex-row flex-lg-column justify-content-center gap-2 gap-sm-x1 gap-md-4 gap-lg-0 align-items-center">
                    <div class="timeline-step-2 w-25 w-lg-100 mb-4 mb-lg-5 mb-xl-6">
                        <div class="timeline-item d-flex justify-content-center">
                            <div style="width: 50%; border-radius: 50%;" class="timeline-icon bg-success rounded-circle d-flex justify-content-center align-items-center">
                                <span class="fs-3 fs-lg-5 fs-xl-4 text-white"> 2</span>
                            </div>
                        </div>
                    </div>
                    <div class="py-1 py-lg-0 px-lg-5 w-75 w-sm-50 w-lg-100 timeline-content">
                        <h6 class="fs-3 fw-bold text-lg-center text-nowrap">Browse Available Spaces</h6>
                        <p class="text-lg-center lh-xl mb-0">Explore a variety of flexible workspaces that fit your needs, whether it’s a desk, private office, or meeting room.</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex flex-row flex-lg-column justify-content-center gap-2 gap-sm-x1 gap-md-4 gap-lg-0 align-items-center">
                    <div class="timeline-step-3 position-relative z-1 overflow-hidden w-25 w-lg-100 mb-4 mb-lg-5 mb-xl-6">
                        <div class="timeline-item d-flex justify-content-center">
                            <div style="width: 50%; border-radius: 50%;" class="timeline-icon bg-color rounded-circle d-flex justify-content-center align-items-center">
                                <span class="fs-3 fs-lg-5 fs-xl-4 text-white"> 3</span>
                            </div>
                        </div>
                    </div>
                    <div class="py-1 py-lg-0 px-lg-5 w-75 w-sm-50 w-lg-100 timeline-content">
                        <h6 class="fs-3 fw-bold text-lg-center lh-lg mb-2">Book Your Space</h6>
                        <p class="text-lg-center lh-xl mb-0">Reserve your chosen workspace with ease and enjoy the flexibility of booking for the exact time you need.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <!-- END OF HOW IT WORK -->


        <!-- BEG OF WHY  -->

        <section class="experience hiddensec position-relative overflow-hidden bg-color" id="service">
    <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path fill="rgba(255, 255, 255, 1)" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
        <path fill="rgba(255, 255, 255, 1)" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0"></path>
        <path fill="rgba(255, 255, 255, 1)" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0"></path>
        <path fill="rgba(255, 255, 255, 1)" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
        <path fill="rgba(255, 255, 255, 1)" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0"></path>
    </svg>
    <div class="container container3">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="position-relative z-1 text-center mb-8 mb-lg-9 video-player-paused" data-video-player-container="data-video-player-container">
                    <div class="overlay rounded-4 bg-1100 object-cover" data-overlay="data-overlay"> 
                        <img class="pause-icon w-100 h-100" src="img/Screenshot 2024-08-04 060742.png" alt="" />
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-lg-7">
                <h2 class="fs-3 fs-lg-3 fw-bold text-center text-white mb-5 mb-lg-9 lh-sm">We created WorkSphere to make workspace booking simple.</h2>
            </div>
            <div class="col-12">
                <div class="row gy-4 g-md-3 pb-8 pb-lg-11 px-1">
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Access Flexible Workspaces</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Choose from coworking spaces, private offices, or meeting rooms that fit your needs.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Easy Workspace Management</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Manage and book your workspaces with ease, ensuring productivity without hassle.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Instant Booking</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Book available spaces instantly, with no need for long-term commitments.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Affordable Pricing</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Find cost-effective workspaces that match your budget and business needs.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Seamless Payment System</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Easily pay for your workspace bookings and generate invoices with a few clicks.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex align-items-start gap-2">
                        <div>
                            <h5 class="fs-8 text-white lh-lg fw-bold">Track Your Workspace Usage</h5>
                            <p class="text-white text-opacity-50 lh-xl mb-0">Keep track of your workspace bookings and time spent to ensure maximum productivity.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <!-- end of why -->



        <section class="section">

    <!-- Start Fun-facts -->
    <div id="fun-facts" class="fun-facts section overlay bg-color">
        <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path fill="rgba(255, 255, 255, 1)" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
            <path fill="rgba(255, 255, 255, 1)" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0"></path>
            <path fill="rgba(255, 255, 255, 1)" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0"></path>
            <path fill="rgba(255, 255, 255, 1)" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
            <path fill="rgba(255, 255, 255, 1)" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0"></path>
        </svg>

        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 ">
                    <!-- Start Single Fun -->
                    <div class="single-fun">
                        <div class="content">
                            <i class="fa fa-solid fa-building"></i>
                            <span class="counter"><?php echo $num_workspaces ?></span>
                            <p>Workspaces Booked</p>
                        </div>
                    </div>
                    <!-- End Single Fun -->
                </div>

                <div class="col-lg-4 col-md-6 col-12">
                    <!-- Start Single Fun -->
                    <div class="single-fun">
                        <div class="content">
                            <i class="fa fa-solid fa-users"></i>
                            <span class="counter"><?php echo $num_users ?></span>
                            <p>Active Users</p>
                        </div>
                    </div>
                    <!-- End Single Fun -->
                </div>

                <div class="col-lg-4 col-md-6 col-12">
                    <!-- Start Single Fun -->
                    <div class="single-fun">
                        <div class="content">
                            <i class="fa fa-solid fa-calendar-check"></i>
                            <span class="counter"><?php echo $num_bookings ?></span>
                            <p>Bookings Made</p>
                        </div>
                    </div>
                    <!-- End Single Fun -->
                </div>
            </div>
        </div>
    </div>
    <!--/ End Fun-facts -->

    <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path fill="#071739" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
        <path fill="#071739" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0"></path>
        <path fill="#071739" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0"></path>
        <path fill="#071739" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
        <path fill="#071739" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0"></path>
    </svg>
</section>
        <div>
            <!-- beg of questions -->
            <section class="">
    <div class="container">
        <div class="row py-8 py-md-10 py-lg-11">
            <div class="col-lg-6">
                <div class="row justify-content-center justify-content-lg-start">
                    <div class="where col-md-8 col-lg-12 col-xl-11">
                        <h2 class="color fs-3 fs-lg-3 lh-sm mb-2 text-lg-start fw-bold">We are here to help you find the perfect workspace</h2>
                        <p class="fs-8 color text-opacity-65 mb-4 mb-md-6 mb-lg-7 lh-lg mb-6 mb-lg-7 text-lg-start">We provide seamless solutions for finding and booking flexible workspaces anytime, anywhere.</p>
                    </div>
                    <div class="col-lg-10">
                        <div class="d-flex gap-2 gap-lg-x1 mb-4 mb-lg-5">
                            <div>
                                <div class="check-icon bg-success mb-1 rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="uil uil-check color"></span>
                                </div>
                            </div>
                            <div>
                                <h5 class="fs-4 fw-bold lh-lg mb-1">Noise-Free Workspace</h5>
                                <p class="lh-xl color text-opacity-70 mb-0">We ensure your workspace is quiet and conducive for maximum productivity.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 gap-lg-x1 mb-4 mb-lg-5">
                            <div>
                                <div class="check-icon bg-success mb-1 rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="uil uil-check color"></span>
                                </div>
                            </div>
                            <div>
                                <h5 class="fs-4 fw-bold lh-lg mb-1 color">24/7 Customer Support</h5>
                                <p class="lh-xl color text-opacity-70 mb-0">Our support team is available around the clock to assist with your workspace needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="accordion mt-lg-4 ps-3 pe-x1" id="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button fs-4 lh-lg fw-bold pt-x1 pb-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expand="true" aria-controls="collapse1" data-accordion-button="data-accordion-button">How can I find and book a workspace?</button>
                        </h2>
                        <div class="accordion-collapse collapse show" id="collapse1" data-bs-parent="#accordion">
                            <div class="accordion-body lh-xl pt-0 pb-x1">Simply browse available spaces by location and type, and book the workspace that fits your needs. You can filter options to find coworking spaces, private offices, or meeting rooms.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button fs-4 lh-lg fw-bold pt-x1 pb-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expand="false" aria-controls="collapse2" data-accordion-button="data-accordion-button">How do I make changes to my booking?</button>
                        </h2>
                        <div class="accordion-collapse collapse" id="collapse2" data-bs-parent="#accordion">
                            <div class="accordion-body lh-xl pt-0 pb-x1">To make changes, visit the 'My Bookings' section on your dashboard, select the booking you want to modify, and make necessary adjustments.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button fs-4 lh-lg fw-bold pt-x1 pb-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expand="false" aria-controls="collapse3" data-accordion-button="data-accordion-button">Can I work remotely from multiple locations?</button>
                        </h2>
                        <div class="accordion-collapse collapse" id="collapse3" data-bs-parent="#accordion">
                            <div class="accordion-body lh-xl pt-0 pb-x1">Yes, WorkSphere provides access to a wide variety of workspaces in multiple cities. You can book a workspace in your current city or while traveling.</div>
                        </div>
                    </div>
                   <div class="accordion-item">
    <h2 class="accordion-header" id="heading4">
        <button class="accordion-button fs-4 lh-lg fw-bold pt-x1 pb-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expand="false" aria-controls="collapse4" data-accordion-button="data-accordion-button">Can I communicate with the workspace provider?</button>
    </h2>
    <div class="accordion-collapse collapse" id="collapse4" data-bs-parent="#accordion">
        <div class="accordion-body lh-xl pt-0 pb-x1">You will communicate with the workspace provider in person when you check in at the location. All details and questions should be clarified at that time, ensuring everything meets your expectations.</div>
    </div>
</div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button fs-4 lh-lg fw-bold pt-x1 pb-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expand="false" aria-controls="collapse5" data-accordion-button="data-accordion-button">How do I contact customer support?</button>
                        </h2>
                        <div class="accordion-collapse collapse" id="collapse5" data-bs-parent="#accordion">
                            <div class="accordion-body lh-xl pt-0 pb-x1">For any support inquiries, you can email our team at worksphere04@gmail.com, or chat with us and we'll get back to you as soon as possible.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        </div>



    </main>

    <!-- FOOTER -->
      <?php include("footer.php"); ?>


    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <!-- <script src="js/click-scroll.js"></script> -->
    <script src="js/animated-headline.js"></script>
    <script src="js/modernizr.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/main.js"></script>

    <!-- new  -->

    <!-- jquery Migrate JS -->
    <script src="js/jquery-migrate-3.0.0.js"></script>

    <!-- Slicknav JS -->
    <script src="js/slicknav.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="js/owl-carousel.js"></script>

    <!-- counterup JS -->
    <script src="js/jquery.counterup.min.js"></script>

    <!-- Counter Up CDN JS -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>


</body>

</html>

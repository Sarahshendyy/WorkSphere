<?php 
include("nav.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="About WorkSphere - The leading digital marketplace for flexible workspace solutions">
    <meta name="author" content="WorkSphere Team">

    <title>About WorkSphere | Flexible Workspace Solutions</title>
    
    <!-- fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/css/boxicons.min.css" integrity="sha512-pVCM5+SN2+qwj36KonHToF2p1oIvoU3bsqxphdOIWMYmgr4ZqD3t5DjKvvetKhXGc/ZG5REYTT6ltKfExEei/Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/indexx.css" rel="stylesheet">
    <style>
        .value-icon {
            background-color: #E3C39D;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .market-card {
            border-left: 4px solid #4B6382;
            transition: all 0.3s ease;
        }
        .market-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .segment-icon {
            color: #4B6382;
            font-size: 2rem;
            margin-bottom: 15px;
        }
        /* Container styling for the entire section */
.container.py-5 {
    padding-top: 4rem !important;
    padding-bottom: 4rem !important;
}

/* Image styling */
.col-lg-6 img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(7, 23, 57, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    object-fit: cover;
    max-height: 500px; /* Adjust based on your preference */
}

/* Hover effect for image */
.col-lg-6 img:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(7, 23, 57, 0.2);
}

/* Text content container */
.col-lg-6.mb-4.mb-lg-0 {
    padding-right: 2.5rem; /* Creates space between text and image */
}

/* Market cards styling */
.market-card {
    border-left: 4px solid #E3C39D;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    background-color: white !important;
}

.market-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(74, 99, 130, 0.1);
}

/* Headings styling */
h2[style*="color: #071739"] {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 2rem !important;
    position: relative;
}

/* h2[style*="color: #071739"]:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -10px;
    width: 60px;
    height: 4px;
    background: #E3C39D;
} */

h5[style*="color: #4B6382"] {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .col-lg-6.mb-4.mb-lg-0 {
        padding-right: 0;
        margin-bottom: 3rem;
    }
    
    .col-lg-6 img {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .container.py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    h2[style*="color: #071739"] {
        font-size: 1.8rem;
    }
}
    </style>
</head>

<body>

    <main>
        <!-- Hero Section -->
        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1" style="background-image: url('./img/ww.png'); background-size: cover; background-position: center; height: 100vh;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-12 mx-auto text-center">
                        <h1 class="text-white mb-4">Redefining Workspace Solutions</h1>
                        <p class="text-white lead">Connecting professionals with flexible work environments through innovative technology</p>
                    </div>
                </div>
            </div>
            
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#ffffff" fill-opacity="1" d="M0,224L34.3,192C68.6,160,137,96,206,90.7C274.3,85,343,139,411,144C480,149,549,107,617,122.7C685.7,139,754,213,823,240C891.4,267,960,245,1029,224C1097.1,203,1166,181,1234,160C1302.9,139,1371,117,1406,106.7L1440,96L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
            </svg>
        </section>

        <!-- Value Proposition Section -->
        <section class="container my-5 py-5">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-3" style="color: #4B6382;">Our Value Proposition</h2>
                    <p class="lead">WorkSphere bridges the gap between workspace providers and professionals seeking flexible work environments through our innovative digital marketplace.</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="value-icon mx-auto">
                            <i class="fa fa-bolt fa-2x text-white"></i>
                        </div>
                        <h4 style="color: #4B6382;">Instant Access</h4>
                        <p>Real-time booking for coworking spaces, private offices, and meeting rooms across multiple locations</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="value-icon mx-auto">
                            <i class="fa fa-money fa-2x text-white"></i>
                        </div>
                        <h4 style="color: #4B6382;">Cost-Effective</h4>
                        <p>Flexible payment options without long-term commitments, perfect for businesses of all sizes</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="value-icon mx-auto">
                            <i class="fa fa-users fa-2x text-white"></i>
                        </div>
                        <h4 style="color: #4B6382;">Community Network</h4>
                        <p>Connect with other professionals and businesses through our integrated networking features</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Market Opportunity Section -->
        <section class="" style="background-color: #A4B5C4;">
            <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0"></path>
            </svg>
            
            <div class="container ">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="mb-4" style="color: #071739;">Addressing Market Needs</h2>
                        <div class="market-card p-4 bg-white mb-3 rounded">
                            <h5 style="color: #4B6382;">Growing Remote Workforce</h5>
                            <p>With over 70% of workers expected to engage in remote or hybrid work by 2025, WorkSphere provides essential infrastructure for this new way of working.</p>
                        </div>
                        <div class="market-card p-4 bg-white mb-3 rounded">
                            <h5 style="color: #4B6382;">Flexibility Demands</h5>
                            <p>Traditional office leases no longer meet the needs of modern professionals who require adaptable workspace solutions.</p>
                        </div>
                        <div class="market-card p-4 bg-white rounded">
                            <h5 style="color: #4B6382;">Cost Efficiency</h5>
                            <p>Startups and small businesses benefit from our affordable alternatives to conventional office space.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="./img/market.png" class="img-fluid rounded shadow" alt="Market Trends">
                    </div>
                </div>
            </div>
            
            <!-- <svg viewBox="0 0 1265 144" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M 0 40 C 164 40 164 20 328 20 L 328 20 L 328 0 L 0 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 327 20 C 445.5 20 445.5 89 564 89 L 564 89 L 564 0 L 327 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 563 89 C 724.5 89 724.5 48 886 48 L 886 48 L 886 0 L 563 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 885 48 C 1006.5 48 1006.5 67 1128 67 L 1128 67 L 1128 0 L 885 0 Z" stroke-width="0"></path>
                <path fill="#ffffff" d="M 1127 67 C 1196 67 1196 0 1265 0 L 1265 0 L 1265 0 L 1127 0 Z" stroke-width="0"></path>
            </svg> -->
        </section>

        <!-- Customer Segments Section -->
        <section class="container my-5 ">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-3" style="color: #4B6382;">Who We Serve</h2>
                    <p class="lead">WorkSphere caters to diverse professionals and businesses seeking flexible workspace solutions</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-laptop segment-icon"></i>
                        <h4 style="color: #4B6382;">Freelancers & Remote Workers</h4>
                        <p>Professionals needing temporary workspaces without long-term commitments</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-rocket segment-icon"></i>
                        <h4 style="color: #4B6382;">Startups & Small Businesses</h4>
                        <p>Growing companies requiring flexible, cost-effective office solutions</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-building segment-icon"></i>
                        <h4 style="color: #4B6382;">Corporations</h4>
                        <p>Enterprises needing satellite offices and meeting rooms for distributed teams</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-briefcase segment-icon"></i>
                        <h4 style="color: #4B6382;">Business Travelers</h4>
                        <p>Professionals requiring quality workspaces while traveling</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-calendar segment-icon"></i>
                        <h4 style="color: #4B6382;">Event Organizers</h4>
                        <p>Those needing spaces for workshops, meetings, or corporate events</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center p-4 h-100">
                        <i class="fa fa-home segment-icon"></i>
                        <h4 style="color: #4B6382;">Coworking Space Providers</h4>
                        <p>Workspace owners looking to maximize their occupancy and visibility</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Competitive Advantage Section -->
        <section class="py-5" style="background-color: #CDD5DB;">
            <div class="container py-5">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="mb-3" style="color: #071739;">Our Competitive Edge</h2>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded h-100">
                            <h4 style="color: #4B6382;"><i class="fa fa-check-circle me-2" style="color: #E3C39D;"></i> Comprehensive Solution</h4>
                            <p>Unlike competitors focusing only on coworking spaces, we offer a full range including private offices, meeting rooms, and event spaces.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded h-100">
                            <h4 style="color: #4B6382;"><i class="fa fa-check-circle me-2" style="color: #E3C39D;"></i> True Flexibility</h4>
                            <p>Hourly, daily, and monthly booking options that genuinely meet the needs of modern professionals.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded h-100">
                            <h4 style="color: #4B6382;"><i class="fa fa-check-circle me-2" style="color: #E3C39D;"></i> Local Market Expertise</h4>
                            <p>Deep understanding of regional workspace needs and pricing structures.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded h-100">
                            <h4 style="color: #4B6382;"><i class="fa fa-check-circle me-2" style="color: #E3C39D;"></i> Integrated Networking</h4>
                            <p>Unique company profiles and collaboration features that foster business connections.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section class="container my-5 py-5">
<div class="row align-items-center">
    <div class="col-lg-6 mb-5 mb-lg-0">
        <div class="p-4 rounded" style="background-color: #071739; color: white;">
            <h3 class="mb-4"><span style="color: #fff;">Our Vision</span></h3>
            <p class="lead" style="color: #F8F9FA;">To become the leading digital platform for flexible workspace solutions worldwide, empowering professionals and businesses with seamless access to workspaces anytime, anywhere.</p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="p-4 rounded" style="background-color:rgb(66, 87, 114); color: white;">
            <h3 class="mb-4"><span style="color: #fff;">Our Mission</span></h3>
            <p class="lead" style="color: #F8F9FA;">To revolutionize workspace accessibility through technology, providing convenient, affordable, and flexible solutions that adapt to the evolving needs of the modern workforce.</p>
        </div>
    </div>
</div>
        </section>

        <!-- Call to Action -->
        <section class="py-5 text-center" style="background-color: #E3C39D;">
            <div class="container py-4">
                <h2 class="mb-4" style="color: #071739;">Ready to Transform Your Work Experience?</h2>
                <a href="./signup&login.php" class="btn btn-lg px-5" style="background-color: #4B6382; color: white;">Join WorkSphere Today</a>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <?php include("footer.php"); ?>

    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/animated-headline.js"></script>
    <script src="js/modernizr.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/main.js"></script>
    <script src="js/jquery-migrate-3.0.0.js"></script>
    <script src="js/slicknav.min.js"></script>
    <script src="js/owl-carousel.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
</body>
</html>
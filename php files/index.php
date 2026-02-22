<?php session_start();?>
<html>
<head>
    <title>Computer Garage - Your Tech Destination</title>
    <link rel="stylesheet" href="css/bootstrap.css"> <!-- Ensure Bootstrap is loaded -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <!-- Styling Files -->
    <?php
    require_once("extfiles.php"); // Should link to any other global CSS
    ?>
    <style>
        /* --- Custom Professional Styles - Applied from the intro section downwards --- */
        body { /* Global body styles for consistency */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa; /* Light gray background */
            color: #333;
            line-height: 1.6;
            margin: 0;
        }

        /* Styles for the original hero section are INLINE in the HTML as per your provided code */
        /* No new CSS for .sectionimages, .partright, .partleft here */


        /* General Section Styling (for content below hero) */
        .section-intro, .section-categories, .section-other-products {
            padding: 40px 15px; /* Reduced top padding slightly */
        }
        @media (min-width: 768px) {
            .section-intro, .section-categories, .section-other-products {
                padding: 60px 0;
            }
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 40px;
            text-align: center;
            color: #343a40;
        }
        .section-title .underlined-title {
            display: inline-block;
            padding-bottom: 10px;
            border-bottom: 3px solid #007bff;
        }

        /* Intro Text (replaces the original #one styling) */
        .intro-text-container {
            background-color: white; /* Retaining white background for this section */
            padding-bottom: 30px; /* Spacing after intro text */
            /* position:relative; -- from original .section-1, may not be needed */
        }
        .intro-text {
            font-size: 1.2rem;
            color: #555;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 0 auto; /* Removed bottom margin, handled by container padding */
        }

        /* Product Category Cards */
        .product-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 70%;
            height: auto;
            margin: 0 auto 20px auto;
            object-fit: contain;
            max-height: 160px;
        }
        .product-card .card-title { /* Replaces .lead.text-center for category titles */
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 15px;
        }
        .product-card .card-text {
            font-size: 0.95rem;
            color: #666;
            flex-grow: 1;
            margin-bottom: 20px;
        }
        .product-card .btn-visit { /* Replaces .buylinks */
            background-color: transparent;
            color: #007bff;
            border: 1px solid #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: inline-block;
            margin-top: auto;
        }
        .product-card .btn-visit:hover {
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
        }

        /* View More Button */
        .view-more-container {
            text-align: center;
            margin-top: 20px; /* Reduced from 40px to account for br tags */
            margin-bottom: 40px; /* Reduced from 60px */
        }
        .btn-view-all { /* Replaces .viewmorebtn */
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-view-all:hover {
            background-color: #1e7e34;
            color: white;
            text-decoration: none;
        }
        hr.section-divider {
            border-top: 1px solid #ddd;
            margin-top: 30px; /* Adjusted */
            margin-bottom: 50px;
        }

        /* Original .underlined style was simple, if you had specific CSS for it add here */
        /* This is for the new section titles, not the span.underlined in original cards */
        .underlined-title {
            /* Defined above within .section-title */
        }
        /* Style for the span.underlined within the product cards if needed, */
        /* but the card-title class now handles the category names. */
        /* If you still use span.underlined for product names inside cards, style it: */
        .product-card span.underlined {
            /* Example: border-bottom: 1px solid #ccc; padding-bottom: 2px; */
            font-weight: normal; /* Override if it was bold from .lead */
        }

        /* Recently Viewed Products - Elegant Horizontal Strip */
        .recently-viewed-strip {
            background: #fff;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
            padding: 25px 0;
            margin: 40px 0;
        }

        .strip-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .strip-title i {
            color: #6c757d;
            margin-right: 8px;
        }

        .clear-all-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .clear-all-btn:hover {
            background: #c82333;
        }

        .products-scroll-container {
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 10px;
        }

        .products-scroll {
            display: flex;
            gap: 15px;
            min-width: min-content;
        }

        .recently-viewed-card {
            flex: 0 0 180px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .recently-viewed-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            border-color: #007bff;
        }

        .recently-viewed-card img {
            width: 100%;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .recently-viewed-card .product-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .recently-viewed-card .product-price {
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .recently-viewed-card .original-price {
            color: #6c757d;
            text-decoration: line-through;
            font-size: 0.8rem;
        }

        .recently-viewed-card .current-price {
            color: #28a745;
            font-weight: 600;
            margin-left: 5px;
        }

        .recently-viewed-card .discount-badge {
            color: #dc3545;
            font-size: 0.75rem;
            margin-left: 5px;
        }

        .recently-viewed-card .view-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .recently-viewed-card .view-btn:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }

        .recently-viewed-remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 20px;
            height: 20px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
            z-index: 10;
        }

        .recently-viewed-remove-btn:hover {
            background: rgba(220, 53, 69, 1);
        }

        /* Scrollbar styling for webkit browsers */
        .products-scroll-container::-webkit-scrollbar {
            height: 6px;
        }

        .products-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .products-scroll-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .products-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .recently-viewed-card {
                flex: 0 0 150px;
            }

            .strip-title {
                font-size: 1.1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .clear-all-btn {
                align-self: flex-end;
            }
        }

    </style>
</head>
<body>

<?php require_once("header.php"); // Your main site navigation bar ?>
 <!-- ORIGINAL HERO SECTION - UNCHANGED -->
<!-- ORIGINAL HERO SECTION - WITH REVISED TEXT -->
<div class="sectionimages">
    <div class="partright" id="pr">
        <p class="lead " style="font-size:10vh;"><b>
                MacBook Air</b></p>
        <p class="lead " style="font-size:6vh;">
            Power. Performance. Perfectly Portable. <!-- << NEW TAGLINE -->
            <br><br>
        <p class="lead " style="font-size:6vh;">
            Available Now
            <!-- << NEW TAGLINE -->
            <br><br>
            <a href="showcat.php" style="font-size:4vh;" class="maclink">
                Discover the Range ></a> <!-- << NEW BUTTON TEXT -->
        </p>
    </div>
    <div class="partleft">
        <img src="assets/sc11.jpg" style="height:100vh;object-fit:cover;width:100%;" id="img1" alt="MacBook Air promotion">
        <img src="assets/sc9.jpg" style="height:100vh;object-fit:cover;width:100%;" id="img2" alt="MacBook Air lifestyle">
        <img src="assets/sc7.jpg" style="height:100vh;object-fit:cover;width:100%;" id="img3" alt="MacBook Air detail">
        <img src="assets/sc7.jpg" style="height:100vh;object-fit:cover;width:100%;" id="img4" alt="MacBook Air feature">
        <img src="assets/sc6.jpg" style="height:100vh;object-fit:cover;width:100%;" id="img5" alt="MacBook Air product shot">
    </div>
</div>
<!-- END ORIGINAL HERO SECTION -->

<br><br><br><br> <!-- Original spacing -->

<!-- REDESIGNED CONTENT STARTS HERE -->
<div class="intro-text-container section-intro"> <!-- Was .section-1 -->
    <div class="container"> <!-- Was #one -->
        <br><br> <!-- Original spacing -->
        <p class="intro-text">
            Explore a World of Technology at Computer Garage. We're dedicated to providing a comprehensive range of high-quality computers, powerful laptops, responsive keyboards, immersive speakers, and many other essential tech products to customers globally.
        </p>
    </div>
</div>

<div class="section-categories">
    <div class="container">
        <!-- No main title here, as per original structure, categories start directly -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Desktops</h3> <!-- Was .lead.text-center with span.underlined -->
                        <img src="assets/pc.png" alt="Desktop Computers" class="img-fluid">
                        <p class="card-text">
                            Unleash robust performance for work or play. Our desktops, featuring advanced processors and high-speed RAM, deliver a seamless experience on stunning, highly-configured displays.
                        </p>
                    </div>
                    <a href="showsubcat.php?catid=18" class="btn-visit">Explore Desktops ></a>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Laptops</h3>
                        <img src="assets/laptop.png" alt="Laptops" class="img-fluid">
                        <p class="card-text">
                            Stay productive and connected wherever you are. From essential work-from-home tools to powerful student companions, our laptops are designed for modern demands.
                        </p>
                    </div>
                    <a href="showsubcat.php?catid=19" class="btn-visit">Shop Laptops ></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <hr class="section-divider"> <!-- Was hr width="100%" color="black" -->
</div>


<div class="section-other-products">
    <div class="container"> <!-- Was .container.lead.text-center#section2 -->
        <h2 class="section-title"><span class="underlined-title">Essential Peripherals & Components</span></h2> <!-- New title for this section -->
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4"> <!-- Was .col-sm -->
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Keyboards</h3>
                        <img src="assets/keyboard1.png" alt="Keyboards" class="img-fluid">
                        <p class="card-text">Find your perfect typing experience. Browse our selection and get your ideal keyboard delivered hassle-free.</p>
                    </div>
                    <a href="showsubcat.php?catid=20" class="btn-visit">Browse Keyboards ></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Mice</h3>
                        <img src="assets/mouse.png" alt="Computer Mice" class="img-fluid">
                        <p class="card-text">Discover superior control with our range of quality wired and wireless mice from top brands at great prices.</p>
                    </div>
                    <a href="showsubcat.php?catid=21" class="btn-visit">Discover Mice ></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Headphones</h3>
                        <img src="assets/headphones.png" alt="Headphones" class="img-fluid">
                        <p class="card-text">Immerse yourself in sound. Experience music and audio with the clarity and depth it deserves.</p>
                    </div>
                    <a href="showsubcat.php?catid=23" class="btn-visit">Find Headphones ></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Speakers</h3>
                        <img src="assets/speaker.png" alt="Speakers" class="img-fluid">
                        <p class="card-text">Elevate your audio with powerful speakers. Top brands and solid sound guaranteed for every setup.</p>
                    </div>
                    <a href="showsubcat.php?catid=22" class="btn-visit">Shop Speakers ></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">SSDs</h3>
                        <img src="assets/ssd.png" alt="Solid State Drives" class="img-fluid">
                        <p class="card-text">Experience lightning-fast load times with our reliable SSDs, backed by a one-year-plus guarantee.</p>
                    </div>
                    <a href="showproduct.php?subcatid=28" class="btn-visit">Explore SSDs ></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div>
                        <h3 class="card-title">Cables and Adapters</h3>
                        <img src="assets/Cable&Adapter.jpg" alt="Cables and Adapters" class="img-fluid">
                        <p class="card-text">Stay connected with our wide selection of cables and adapters — from HDMI to USB-C, network to power cables — designed to keep your setup seamless and efficient.</p>
                    </div>
                    <a href="showsubcat.php?catid=25" class="btn-visit">View Cables and Adapters></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="view-more-container"> <!-- Was div align="center" -->
    <a class="btn-view-all" href="showcat.php">Explore All Products ></a> <!-- Was .viewmorebtn -->
</div>

<!-- Recently Viewed Products Section -->
<div id="recently-viewed-section" class="recently-viewed-strip" style="display: none;">
    <div class="container">
        <h4 class="strip-title">
            <span><i class="fas fa-history"></i> Recently Viewed</span>
            <button class="clear-all-btn" onclick="recentlyViewedManager.clearRecentlyViewed()">Clear All</button>
        </h4>
        <div class="products-scroll-container">
            <div id="recently-viewed-products" class="products-scroll">
                <!-- Products will be loaded here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<br><br><br> <!-- Original spacing -->

<?php require_once("footer.php"); ?>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>

<!-- If you have custom JS for your original hero slideshow, it should still work. -->
<!-- No new slideshow JS is added here for the hero section. -->

</body>
</html>
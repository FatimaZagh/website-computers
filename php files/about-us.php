<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Computer Garage</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <?php require_once("extfiles.php"); ?>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0f;
            color: #ffffff;
            line-height: 1.6;
            margin: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        /* Modern Dark Hero Section */
        .about-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            padding: 120px 0;
            text-align: center;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255, 119, 198, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .about-hero h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 2;
        }

        .about-hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.8;
            position: relative;
            z-index: 2;
            font-weight: 300;
        }

        /* Floating Cards Section */
        .about-section {
            padding: 80px 0;
            background: #0a0a0f;
            position: relative;
        }

        .floating-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .about-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .about-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .about-card:hover::before {
            opacity: 1;
        }

        .about-card:hover {
            transform: translateY(-10px);
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
        }

        .about-card h3 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .about-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            position: relative;
            z-index: 2;
        }

        /* Stats Section with Glassmorphism */
        .stats-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 70% 30%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .stat-item {
            text-align: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px 20px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        /* Team Section */
        .team-section {
            background: #0a0a0f;
            padding: 100px 0;
            position: relative;
        }

        .team-card {
            text-align: center;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .team-card:hover::before {
            opacity: 1;
        }

        .team-card:hover {
            transform: translateY(-10px);
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
        }

        .team-card h4 {
            color: #ffffff;
            font-weight: 700;
            margin: 20px 0 10px;
            position: relative;
            z-index: 2;
        }

        .team-card p {
            color: rgba(255, 255, 255, 0.7);
            position: relative;
            z-index: 2;
        }

        .team-card .text-muted {
            color: #667eea !important;
            font-weight: 600;
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            font-weight: 600;
            position: relative;
            z-index: 2;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title.white {
            color: white;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Values Section */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .value-item {
            text-align: center;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .value-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .value-item:hover::before {
            opacity: 1;
        }

        .value-item:hover {
            transform: translateY(-8px);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .value-item h4 {
            color: #ffffff;
            font-weight: 700;
            margin: 25px 0 15px;
            position: relative;
            z-index: 2;
        }

        .value-item p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .value-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2.2rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        /* Button Hover Effects */
        .btn:hover {
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }

            .about-hero p {
                font-size: 1.1rem;
            }

            .floating-cards {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .about-card {
                padding: 30px 25px;
            }

            .values-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .stat-number {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 480px) {
            .about-hero {
                padding: 80px 0;
            }

            .about-hero h1 {
                font-size: 2rem;
            }

            .about-section {
                padding: 60px 0;
            }

            .stats-section, .team-section {
                padding: 80px 0;
            }
        }
    </style>
</head>
<body>

<?php require_once("header.php"); ?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <h1>About Computer Garage</h1>
        <p>Your trusted partner in building powerful computing solutions for work, gaming, and creativity</p>
    </div>
</section>

<!-- Main About Section -->
<section class="about-section">
    <div class="container">
        <div class="floating-cards">
            <div class="about-card">
                <h3>Our Story</h3>
                <p>Founded by passionate tech enthusiasts, Computer Garage began as a small venture with a big vision: to make high-performance computing accessible to everyone. What started in a garage has grown into a trusted destination for PC builders, gamers, and professionals.</p>
                <p>We understand that every customer has unique needs, whether you're building your first gaming rig, upgrading your workstation, or looking for the perfect laptop for school or work.</p>
            </div>
            <div class="about-card">
                <h3>Our Mission</h3>
                <p>To provide top-quality computer hardware, components, and accessories while delivering exceptional customer service and expert guidance. We believe technology should empower, not intimidate.</p>
                <p>Every product we sell is carefully selected for quality, performance, and value. Our team stays up-to-date with the latest technology trends to help you make informed decisions.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <h2 class="section-title white">Our Impact</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <div class="stat-label">Happy Customers</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <div class="stat-label">Products Available</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <span class="stat-number">2+</span>
                    <div class="stat-label">Years of Experience</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <div class="stat-label">Customer Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Values</h2>
        <div class="values-grid">
            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h4>Quality First</h4>
                <p>We only stock products from trusted brands that meet our high standards for performance and reliability.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Customer Focus</h4>
                <p>Your satisfaction is our priority. We provide personalized service and support throughout your journey.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4>Innovation</h4>
                <p>We stay ahead of technology trends to bring you the latest and greatest in computing solutions.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4>Trust</h4>
                <p>We build lasting relationships through honest advice, fair pricing, and reliable service.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <h2 class="section-title">Meet Our Team</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">FZ</div>
                    <h4>Fatima Zaghlol</h4>
                    <p class="text-muted">Founder & CEO</p>
                    <p>Tech enthusiast and visionary behind Computer Garage. Passionate about making technology accessible to everyone.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">TS</div>
                    <h4>Tech Support Team</h4>
                    <p class="text-muted">Customer Support</p>
                    <p>Our dedicated support team is here to help you with product selection, technical questions, and after-sales service.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">PS</div>
                    <h4>Product Specialists</h4>
                    <p class="text-muted">Product Experts</p>
                    <p>Our product specialists stay up-to-date with the latest technology to provide you with expert recommendations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="about-section" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 50% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 70%); pointer-events: none;"></div>
    <div class="container text-center" style="position: relative; z-index: 2;">
        <h2 class="section-title white">Ready to Build Your Dream Setup?</h2>
        <p class="lead mb-4" style="color: rgba(255, 255, 255, 0.8); font-size: 1.3rem; max-width: 600px; margin: 0 auto 40px;">Explore our wide range of products and let us help you create the perfect computing solution.</p>
        <a href="showcat.php" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 18px 40px; border-radius: 50px; font-weight: 600; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
            Shop Now
        </a>
        <a href="contactus.php" class="btn btn-outline-primary btn-lg ml-3" style="border: 2px solid rgba(102, 126, 234, 0.8); color: #667eea; padding: 16px 40px; border-radius: 50px; font-weight: 600; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); transition: all 0.3s ease;">
            Contact Us
        </a>
    </div>
</section>

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

<?php require_once("footer.php"); ?>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>

</body>
</html>

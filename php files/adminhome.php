<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php
    // Assuming extfiles.php includes Bootstrap and potentially jQuery
    require_once("extfiles.php");
    ?>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-bg: #12141a; /* Slightly darker base */
            --secondary-bg-rgb: 37, 40, 48; /* For rgba card backgrounds */
            --accent-color: #00aaff;
            --accent-color-rgb: 0, 170, 255;
            --text-color: #e0e0e0;
            --text-color-darker: #b0b0b0;
            --card-hover-bg-rgb: 48, 52, 61;
            --border-color: rgba(255, 255, 255, 0.08);
            --shadow-color: rgba(0, 0, 0, 0.5);
            --glow-color: rgba(var(--accent-color-rgb), 0.3);
            --card-border-radius: 12px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden; /* Prevent horizontal scroll from pseudo-elements */
            position: relative; /* For pseudo-elements */
        }

        /* Dynamic Animated Background Blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            width: 80vmax; /* Large size, viewport relative */
            height: 80vmax;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.1) 0%, transparent 60%);
            z-index: -2; /* Deepest layer */
            animation: blobMove 30s infinite alternate ease-in-out;
            will-change: transform; /* Performance hint */
        }

        body::after {
            width: 60vmax;
            height: 60vmax;
            background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.05) 0%, transparent 50%);
            animation-name: blobMove2;
            animation-duration: 40s;
            animation-delay: -10s; /* Stagger animations */
        }

        @keyframes gradientBG { /* Kept from original, can be primary or layered */
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes blobMove {
            0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
            100% { transform: translate(-40%, -60%) scale(1.3) rotate(180deg); }
        }
        @keyframes blobMove2 {
            0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
            100% { transform: translate(-60%, -40%) scale(1.1) rotate(-120deg); }
        }

        /* Base background gradient if blobs are too subtle or not desired */
        /*
       body {
            background-image: linear-gradient(135deg, #1f2023 0%, #2c3e50 50%, #1f2023 100%);
            background-size: 400% 400%;
            animation: gradientBG 25s ease infinite;
       }
       */


        /* Navbar Styling (Assuming Bootstrap structure) */
        .navbar {
            background-color: rgba(var(--secondary-bg-rgb), 0.5) !important; /* Semi-transparent */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); /* Safari */
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000; /* Ensure navbar stays on top */
            padding: 0.75rem 1rem;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            transition: color var(--transition-speed) ease;
        }
        .navbar .nav-link:hover, .navbar .navbar-brand:hover {
            color: var(--accent-color) !important;
            text-shadow: 0 0 8px var(--glow-color);
        }
        #acc:hover {
            color: var(--accent-color) !important;
            text-shadow: 0 0 8px var(--glow-color);
        }

        .admin-main-content {
            flex-grow: 1;
            padding-top: 40px; /* Reduced due to potentially thinner sticky navbar */
            padding-bottom: 60px;
            position: relative; /* Stacking context for its children */
            z-index: 1; /* Above background pseudo-elements */
        }

        .admin-welcome-card {
            background-color: rgba(var(--secondary-bg-rgb), 0.6); /* Glassmorphism */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--card-border-radius);
            padding: 35px 45px;
            margin-bottom: 50px;
            box-shadow: 0 12px 35px var(--shadow-color);
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .admin-welcome-card h1 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 2.8rem; /* Slightly larger */
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .admin-welcome-card p.lead {
            color: var(--text-color-darker);
            font-size: 1.1rem;
        }

        .admin-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px; /* Increased gap */
            margin-top: 30px;
        }

        .action-card {
            background-color: rgba(var(--secondary-bg-rgb), 0.5); /* Glassmorphism */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: var(--card-border-radius);
            padding: 30px 25px;
            text-align: center;
            color: var(--text-color);
            text-decoration: none;
            transition: transform var(--transition-speed) ease,
            background-color var(--transition-speed) ease,
            box-shadow var(--transition-speed) ease,
            border-color var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden; /* For potential pseudo-element effects on hover */
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .action-card:hover {
            background-color: rgba(var(--card-hover-bg-rgb), 0.7);
            transform: translateY(-10px) scale(1.03);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 15px 30px var(--shadow-color), 0 0 20px var(--glow-color);
            border-color: rgba(var(--accent-color-rgb), 0.5);
        }

        .action-card i {
            font-size: 2.8rem; /* Slightly larger */
            margin-bottom: 20px;
            color: var(--accent-color);
            transition: color var(--transition-speed) ease, transform var(--transition-speed) ease;
            text-shadow: 0 0 10px rgba(var(--accent-color-rgb),0.4);
        }

        .action-card:hover i {
            color: #fff;
            transform: scale(1.1) rotate(-5deg);
        }

        .action-card-title {
            font-size: 1.15rem; /* Slightly larger */
            font-weight: 500;
        }

        /* Footer Styling */
        .footer {
            background-color: rgba(var(--secondary-bg-rgb), 0.3); /* Subtle glass effect */
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: var(--text-color-darker);
            padding: 25px 0;
            text-align: center;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
            position: relative; /* Stacking context */
            z-index: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) { /* Adjust breakpoint for better card fit */
            .admin-actions-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .admin-welcome-card h1 {
                font-size: 2.2rem;
            }
            .admin-welcome-card {
                padding: 25px 20px;
            }
            .admin-actions-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .action-card i {
                font-size: 2.2rem;
            }
            .action-card-title {
                font-size: 1.05rem;
            }
            body::before, body::after { /* Reduce blob size on mobile for performance */
                width: 120vmax;
                height: 120vmax;
            }
        }
    </style>
</head>
<body>

<?php
// Ensure adminnavbar.php is styled or adapts well
require_once("adminnavbar.php");
?>

<div class="admin-main-content container">
    <div class="admin-welcome-card">
        <h1 class="display-4">Welcome, Administrator!</h1>
        <p class="lead">Efficiently manage all aspects of the website from this central dashboard.</p>
    </div>

    <div class="admin-actions-grid">
        <a href="orders.php" class="action-card">
            <i class="fas fa-receipt"></i>
            <span class="action-card-title">View Orders</span>
        </a>
        <a href="membman.php" class="action-card">
            <i class="fas fa-users-cog"></i>
            <span class="action-card-title">Manage Members</span>
        </a>
        <a href="admin_view_feedback.php" class="action-card">
            <i class="fas fa-comments"></i>
            <span class="action-card-title">View Feedback</span>
        </a>
        <a href="admin_sales_report.php" class="action-card">
            <i class="fas fa-chart-line"></i>
            <span class="action-card-title">Sales Reports</span>
        </a>
        <a href="discount_codes.php" class="action-card">
            <i class="fas fa-tags"></i>
            <span class="action-card-title">Manage Discount Codes</span>
        </a>

        <!-- Example: Add more admin actions if needed -->
        <!--
        <a href="settings.php" class="action-card">
            <i class="fas fa-cogs"></i>
            <span class="action-card-title">Site Settings</span>
        </a>
        <a href="content_management.php" class="action-card">
            <i class="fas fa-file-alt"></i>
            <span class="action-card-title">Manage Content</span>
        </a>
        -->
    </div>
</div>

<?php
// Ensure footer.php is styled or adapts well
require_once("footer.php");
?>

</body>
</html>
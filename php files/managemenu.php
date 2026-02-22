<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products & Categories</title>
    <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-bg: #12141a;
            --secondary-bg-rgb: 37, 40, 48;
            --accent-color: #00aaff;
            --accent-color-rgb: 0, 170, 255;
            --text-color: #e0e0e0;
            --text-color-darker: #b0b0b0;
            --card-hover-bg-rgb: 48, 52, 61; /* For action cards */
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
            overflow-x: hidden;
            position: relative;
        }

        body::before, body::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            width: 80vmax;
            height: 80vmax;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.1) 0%, transparent 60%);
            z-index: -2;
            animation: blobMove 30s infinite alternate ease-in-out;
            will-change: transform;
        }

        body::after {
            width: 60vmax;
            height: 60vmax;
            background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.05) 0%, transparent 50%);
            animation-name: blobMove2;
            animation-duration: 40s;
            animation-delay: -10s;
        }

        @keyframes blobMove {
            0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
            100% { transform: translate(-40%, -60%) scale(1.3) rotate(180deg); }
        }
        @keyframes blobMove2 {
            0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
            100% { transform: translate(-60%, -40%) scale(1.1) rotate(-120deg); }
        }

        /* Navbar Styling */
        .navbar {
            background-color: rgba(var(--secondary-bg-rgb), 0.5) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
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
        /* Your existing #acc style from original code, adapted */
        #acc:hover {
            color: var(--accent-color) !important; /* Changed from white to accent for consistency */
            text-shadow: 0 0 8px var(--glow-color);
        }

        .page-main-content { /* Generic name for main content wrapper */
            flex-grow: 1;
            padding-top: 40px; /* Adjust if navbar height changes */
            padding-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .welcome-card { /* Re-using admin-welcome-card style from adminhome */
            background-color: rgba(var(--secondary-bg-rgb), 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--card-border-radius);
            padding: 35px 45px;
            margin-bottom: 50px;
            box-shadow: 0 12px 35px var(--shadow-color);
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .welcome-card h1 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 2.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .welcome-card p.lead-text { /* More specific class for the descriptive text */
            color: var(--text-color-darker);
            font-size: 1.1rem;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .actions-grid { /* Re-using admin-actions-grid style from adminhome */
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .action-card-link { /* Replacing .lnkbtn with a more descriptive name */
            background-color: rgba(var(--secondary-bg-rgb), 0.5);
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
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .action-card-link:hover {
            background-color: rgba(var(--card-hover-bg-rgb), 0.7);
            transform: translateY(-10px) scale(1.03);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 15px 30px var(--shadow-color), 0 0 20px var(--glow-color);
            border-color: rgba(var(--accent-color-rgb), 0.5);
        }

        .action-card-link i {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: var(--accent-color);
            transition: color var(--transition-speed) ease, transform var(--transition-speed) ease;
            text-shadow: 0 0 10px rgba(var(--accent-color-rgb),0.4);
        }

        .action-card-link:hover i {
            color: #fff;
            transform: scale(1.1) rotate(-5deg);
        }

        .action-card-title {
            font-size: 1.15rem;
            font-weight: 500;
        }

        /* Footer Styling */
        .footer {
            background-color: rgba(var(--secondary-bg-rgb), 0.3);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: var(--text-color-darker);
            padding: 25px 0;
            text-align: center;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
            position: relative;
            z-index: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .actions-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .welcome-card h1 {
                font-size: 2.2rem;
            }
            .welcome-card {
                padding: 25px 20px;
            }
            .actions-grid {
                grid-template-columns: 1fr; /* Stack cards */
                gap: 20px;
            }
            .action-card-link i {
                font-size: 2.2rem;
            }
            .action-card-title {
                font-size: 1.05rem;
            }
            body::before, body::after {
                width: 120vmax;
                height: 120vmax;
            }
        }
    </style>
</head>
<body>

<?php require_once("adminnavbar.php"); ?>

<div class="container page-main-content">
    <div class="welcome-card"> <!-- Using the styled welcome card -->
        <h1 class="display-4">Manage Products & Categories</h1>
        <p class="lead-text">Admin can manage all product and category aspects from here.</p>
    </div>

    <div class="actions-grid"> <!-- Using the grid for action links -->
        <a href="catmng.php" class="action-card-link">
            <i class="fas fa-tags"></i>
            <span class="action-card-title">Manage Categories</span>
        </a>
        <a href="subcatmang.php" class="action-card-link">
            <i class="fas fa-sitemap"></i>
            <span class="action-card-title">Manage Sub-Categories</span>
        </a>
        <a href="manageproducts.php" class="action-card-link"> <!-- Changed order based on typical workflow -->
            <i class="fas fa-plus-circle"></i>
            <span class="action-card-title">Add New Products</span>
        </a>
        <a href="viewproducts.php" class="action-card-link">
            <i class="fas fa-boxes"></i>
            <span class="action-card-title">View/Edit Products</span>
        </a>
    </div>
</div>

<br><br><br> <!-- Keep some spacing before footer -->
<?php require_once("footer.php"); ?>

</body>
</html>
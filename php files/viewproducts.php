<?php
session_start();
ob_start(); // Start output buffering

// Admin security checks
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit;
}
if ($_SESSION["usertype"] !== "admin") {
    header("location:login.php");
    exit;
}

require_once("vars.php"); // For db credentials
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View All Products</title>
        <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --product-card-bg-rgb: var(--secondary-bg-rgb); /* Can be same or slightly different */
                --product-card-hover-bg-rgb: 48, 52, 61;
            }

            body {
                font-family: 'Poppins', sans-serif; background-color: var(--primary-bg); color: var(--text-color);
                margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh;
                overflow-x: hidden; position: relative;
            }
            body::before, body::after {
                content: ''; position: fixed; top: 50%; left: 50%; width: 80vmax; height: 80vmax;
                border-radius: 50%; background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.1) 0%, transparent 60%);
                z-index: -2; animation: blobMove 30s infinite alternate ease-in-out; will-change: transform;
            }
            body::after {
                width: 60vmax; height: 60vmax; background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.05) 0%, transparent 50%);
                animation-name: blobMove2; animation-duration: 40s; animation-delay: -10s;
            }
            @keyframes blobMove { 0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); } 100% { transform: translate(-40%, -60%) scale(1.3) rotate(180deg); } }
            @keyframes blobMove2 { 0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); } 100% { transform: translate(-60%, -40%) scale(1.1) rotate(-120deg); } }

            .navbar {
                background-color: rgba(var(--secondary-bg-rgb), 0.5) !important; backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color);
                position: sticky; top: 0; z-index: 1000; padding: 0.75rem 1rem;
            }
            .navbar .navbar-brand, .navbar .nav-link { color: var(--text-color) !important; font-weight: 500; transition: color var(--transition-speed) ease; }
            .navbar .nav-link:hover, .navbar .navbar-brand:hover { color: var(--accent-color) !important; text-shadow: 0 0 8px var(--glow-color); }
            #acc:hover { color: var(--accent-color) !important; text-shadow: 0 0 8px var(--glow-color); }

            .page-main-content { flex-grow: 1; padding-top: 40px; padding-bottom: 60px; position: relative; z-index: 1; }
            .page-title {
                color: #fff; font-weight: 600; margin-bottom: 40px; padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color); text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block;
            }
            .text-center .page-title { display: block; width: fit-content; margin-left: auto; margin-right: auto; }

            .category-header {
                font-size: 2rem; font-weight: 600; color: #fff;
                margin-top: 50px; margin-bottom: 20px;
                padding-bottom: 10px; border-bottom: 2px solid var(--accent-color);
                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }
            .category-header:first-of-type { margin-top: 0; } /* No top margin for the very first category */

            .subcategory-header {
                font-size: 1.4rem; font-weight: 500; color: var(--text-color);
                margin-top: 30px; margin-bottom: 25px;
                padding-left: 10px; border-left: 3px solid var(--accent-color);
            }

            .product-card { /* Replaces .product-item */
                background-color: rgba(var(--product-card-bg-rgb), 0.5);
                backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
                border: 1px solid var(--border-color);
                border-radius: var(--card-border-radius);
                padding: 20px;
                text-align: center;
                color: var(--text-color);
                text-decoration: none;
                transition: transform var(--transition-speed) ease,
                background-color var(--transition-speed) ease,
                box-shadow var(--transition-speed) ease,
                border-color var(--transition-speed) ease;
                display: flex;
                flex-direction: column;
                justify-content: space-between; /* Pushes name to bottom if image isn't filling height */
                height: 100%; /* Ensure card takes full height of its grid cell */
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
                overflow: hidden; /* Prevent content from spilling on transform */
            }
            .product-card:hover {
                background-color: rgba(var(--product-card-hover-bg-rgb), 0.7);
                transform: translateY(-8px) scale(1.02);
                color: #fff;
                text-decoration: none;
                box-shadow: 0 12px 30px var(--shadow-color), 0 0 15px var(--glow-color);
                border-color: rgba(var(--accent-color-rgb), 0.5);
            }
            .product-card-link { /* The <a> tag */
                text-decoration: none;
                color: inherit; /* Inherit color from .product-card */
                display: flex;
                flex-direction: column;
                height: 100%;
            }
            .product-card-link:hover {
                text-decoration: none;
                color: #fff; /* Ensure text stays white on hover if not default */
            }

            .product-image-wrapper {
                height: 200px; /* Fixed height for image container */
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(var(--primary-bg),0.3); /* Subtle background for image area */
                border-radius: calc(var(--card-border-radius) - 6px); /* Slightly smaller radius */
                padding:10px;
            }
            .product-image {
                display: block;
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
            .product-name {
                font-size: 1.1rem;
                font-weight: 500;
                margin-top: auto; /* Pushes name to bottom */
                line-height: 1.3;
                min-height: 2.6em; /* Reserve space for two lines of text */
                display: -webkit-box; /* For ellipsis */
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .product-card:hover .product-name { color: #fff; }


            .alert-info {
                background-color: rgba(var(--secondary-bg-rgb), 0.7); border: 1px solid var(--border-color);
                color: var(--text-color); border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                border-left: 5px solid var(--accent-color) !important; text-align: center;
            }

            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3); backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px); color: var(--text-color-darker);
                padding: 25px 0; text-align: center; border-top: 1px solid var(--border-color);
                margin-top: auto; position: relative; z-index: 1;
            }

            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .category-header { font-size: 1.6rem; }
                .subcategory-header { font-size: 1.2rem; }
                .product-card { padding: 15px; }
                .product-image-wrapper { height: 160px; }
                .product-name { font-size: 1rem; }
            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container-fluid page-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5"><i class="fas fa-boxes me-2"></i>View All Products</h1>
        </div>

        <div class="container"> <!-- Inner container for content -->
            <?php
            $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());
            $q = "SELECT
                    mp.productid, mp.productname, mp.productpic,
                    mc.catname, sc.subcatname,
                    COUNT(pc.id) as color_count
                  FROM manageproduct mp
                  INNER JOIN managecat mc ON mp.catid = mc.catid
                  INNER JOIN subcat sc ON mp.subcatid = sc.subcatid
                  LEFT JOIN product_colors pc ON mp.productid = pc.product_id
                  GROUP BY mp.productid, mp.productname, mp.productpic, mc.catname, sc.subcatname
                  ORDER BY mc.catname ASC, sc.subcatname ASC, mp.productname ASC";
            $res = mysqli_query($connection, $q) or die("Error in query: " . mysqli_error($connection));
            $rowcount = mysqli_num_rows($res);

            if ($rowcount == 0) {
                echo "<div class='alert alert-info mt-4'>No products found. Add products via the 'Manage Products' section.</div>";
            } else {
                $current_category = "";
                $current_subcategory = "";
                $first_category = true;
                $first_subcategory_in_category = true;

                echo "<div class='row g-4'>"; // Start the main product grid row with gutters

                while ($product = mysqli_fetch_assoc($res)) {
                    // New Category
                    if ($current_category != $product['catname']) {
                        if (!$first_category) {
                            // echo "</div>"; // Close previous subcategory's product row if any logic depended on it
                        }
                        $current_category = $product['catname'];
                        $current_subcategory = ""; // Reset subcategory
                        $first_category = false;
                        $first_subcategory_in_category = true;
                        // Category header will span full width above its products
                        echo "<div class='col-12'><h2 class='category-header'>" . htmlspecialchars($current_category) . "</h2></div>";
                    }

                    // New Subcategory
                    if ($current_subcategory != $product['subcatname']) {
                        if (!$first_subcategory_in_category) {
                            // echo "</div>"; // Close previous product row for the subcategory
                        }
                        $current_subcategory = $product['subcatname'];
                        $first_subcategory_in_category = false;
                        // Subcategory header will span full width above its products
                        echo "<div class='col-12'><h4 class='subcategory-header'>" . htmlspecialchars($current_subcategory) . "</h4></div>";
                    }

                    // Display product card
                    // Using Bootstrap's responsive columns: lg-3 (4 per row on large), md-4 (3 per row on medium), sm-6 (2 per row on small)
                    echo "<div class='col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch'>";
                    echo "  <div class='product-card'>"; // product-card takes full height of its parent (d-flex and align-items-stretch on col)
                    echo "    <a href='updateproduct.php?pid=" . htmlspecialchars($product['productid']) . "' class='product-card-link'>";
                    echo "      <div class='product-image-wrapper'>";
                    echo "          <img src='uploads/" . htmlspecialchars($product['productpic']) . "' class='product-image' alt='" . htmlspecialchars($product['productname']) . "'>";
                    echo "      </div>";
                    echo "      <p class='product-name'>" . htmlspecialchars($product['productname']) . "</p>";

                    // Minimal color indicator
                    if ($product['color_count'] > 0) {
                        echo "      <small style='color: #00aaff; font-size: 0.8em; margin-top: 5px;'>";
                        echo "        <i class='fas fa-palette'></i> {$product['color_count']} colors";
                        echo "      </small>";
                    }
                    echo "    </a>";
                    echo "  </div>";
                    echo "</div>";
                }
                echo "</div>"; // Close the main product grid row
            }
            mysqli_close($connection);
            ?>
        </div> <!-- /.inner container -->
    </div> <!-- /.page-main-content -->

    <br><br>
    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); ?>
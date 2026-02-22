<?php session_start(); ?>
<html>
<head>
    <title>
        Subcategories - Computer Garage <!-- Dynamic title would be better -->
    </title>

    <?php require_once("extfiles.php"); // Includes CSS, JS, etc. ?>
    <style>
        /* Your existing CSS styles for .col-sm, .prodlinks, etc. */
        body {
            background-color: white;
            color: black;
        }
        .col-sm {
            margin: 10px;
            background-color: white;
            border-style: solid;
            border-color: white;
            padding: 10px;
            border-radius: 4px;
            color: black;
        }
        .col-sm:hover > .prodlinks {
            color: white;
        }
        .col-sm:hover {
            border-style: solid;
            border-color: black;
            background-color: black;
            border-radius: 4px;
        }
        .prodlinks {
            text-decoration: none;
            color: black;
        }
        .prodlinks:hover {
            text-decoration: none;
            color: black;
        }
        .col-sm:hover > a > img {
            transform: scale(1.08);
            transition: transform .2s;
        }
        @media (max-width: 940px) {
            .col-sm:hover > a > img {
                transform: scale(1);
            }
        }
        /* Added simple styling for the page heading and no-subcategories message for clarity */
        .page-heading {
            margin-top: 80px; /* Adjusted from 100px for visual balance */
            margin-bottom: 40px;
            text-align: center;
            font-size: 2.8rem; /* Using Bootstrap's display-4 class might be too large */
            font-weight: 600;
        }
        .no-subcategories-message {
            font-size: 1.2rem;
            color: #555;
        }
        .subcategories-container {
            margin-top: 40px; /* Adjusted from 100px */
            margin-bottom: 60px;
        }
    </style>
</head>
<body>
<?php require_once("header.php"); // Includes the site navigation bar ?>

<?php
// --- STEP 1: VALIDATE AND GET THE CATEGORY ID ---
// Check if 'catid' is present in the URL (e.g., showsubcat.php?catid=20)
// Also check if it's a numeric value.
if (!isset($_GET["catid"]) || !is_numeric($_GET["catid"])) {
    // If 'catid' is missing or not a number, display an error message and stop.
    print "<h1 class='page-heading'>Invalid Category ID Provided.</h1>";
    print "<p class='text-center'>Please select a category from the main products page.</p>";
    require_once("footer.php"); // Include the site footer
    exit; // Terminate script execution
}

// Sanitize the catid by casting it to an integer.
// This helps prevent SQL injection for this specific parameter.
$catid = (int)$_GET["catid"];

// --- STEP 2: DATABASE CONNECTION AND FETCHING MAIN CATEGORY NAME ---
require_once("vars.php"); // Contains database credentials (dbhost, dbuname, dbpass, dbname)

// Establish a connection to the MySQL database.
$connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
// Check if the connection was successful.
if (!$connection) {
    // If connection fails, display a generic error and stop.
    // In a production environment, you might log this error instead of printing details.
    die("<h1 class='page-heading'>Database Connection Error</h1><p class='text-center'>We are experiencing technical difficulties. Please try again later.</p>");
}

// Prepare to fetch the name of the main category.
$category_name_display = "Subcategories"; // A default name if something goes wrong.

// SQL query to select the category name from the 'managecat' table
// where 'catid' matches the one from the URL.
// Using a prepared statement for security.
$q_cat_name = "SELECT catname FROM managecat WHERE catid = ?";
$stmt_cat_name = mysqli_prepare($connection, $q_cat_name);

if ($stmt_cat_name) {
    // Bind the integer $catid to the placeholder '?' in the SQL query.
    mysqli_stmt_bind_param($stmt_cat_name, "i", $catid);
    // Execute the prepared statement.
    mysqli_stmt_execute($stmt_cat_name);
    // Get the result set from the executed statement.
    $res_cat_name = mysqli_stmt_get_result($stmt_cat_name);
//microsoft
    // Fetch the result as an associative array.
    if ($res_cat_arr = mysqli_fetch_assoc($res_cat_name)) { // Changed to fetch_assoc for clarity
        // If a category name is found, sanitize it for HTML display and assign it.
        $category_name_display = htmlspecialchars($res_cat_arr['catname']);
    } else {
        // If no category with the given 'catid' is found in 'managecat'.
        print "<h1 class='page-heading'>Category Not Found</h1>";
        print "<p class='text-center'>The category you are looking for (ID: $catid) does not exist.</p>";
        mysqli_stmt_close($stmt_cat_name); // Close the statement
        mysqli_close($connection);         // Close the database connection
        require_once("footer.php");       // Include the footer
        exit;                             // Terminate script execution
    }
    mysqli_stmt_close($stmt_cat_name); // Close the statement after use.
} else {
    // If preparing the statement failed (e.g., SQL syntax error in $q_cat_name)
    print "<h1 class='page-heading'>Error Fetching Category Name</h1>";
    // Log mysqli_error($connection) in a real application
    mysqli_close($connection);
    require_once("footer.php");
    exit;
}
?>

<!-- Display the main category name as the page heading -->
<h1 class="page-heading">
    <?php print $category_name_display; ?>
</h1>

<div class="container subcategories-container">
    <div class="row">
        <?php
        // --- STEP 3: FETCH AND DISPLAY SUBCATEGORIES ---
        // SQL query to select subcategory details from the 'subcat' table
        // where 'catid' matches the main category ID.
        // Selecting specific columns is better than 'SELECT *'.
        $q_subcat = "SELECT subcatid, subcatname, subcatpic FROM subcat WHERE catid = ?";
        $stmt_subcat = mysqli_prepare($connection, $q_subcat);

        if ($stmt_subcat) {
            // Bind the integer $catid to the placeholder.
            mysqli_stmt_bind_param($stmt_subcat, "i", $catid);
            // Execute the statement.
            mysqli_stmt_execute($stmt_subcat);
            // Get the result set.
            $res_subcat = mysqli_stmt_get_result($stmt_subcat);
            // Get the number of rows (subcategories) found.
            $rowcount_subcat = mysqli_num_rows($res_subcat);

            if ($rowcount_subcat == 0) {
                // If no subcategories are found for this main category.
                print "<div class='col-12 text-center no-subcategories-message'><p>No subcategories currently available for " . $category_name_display . ".</p><p>Please check back later!</p></div>";
            } else {
                // If subcategories are found, loop through each one.
                while ($resarr_subcat = mysqli_fetch_assoc($res_subcat)) { // Changed to fetch_assoc
                    // Sanitize data for HTML output to prevent XSS.
                    $subcat_id_display = htmlspecialchars($resarr_subcat['subcatid']);
                    $subcat_name_to_display = htmlspecialchars($resarr_subcat['subcatname']);
                    $subcat_pic_filename = htmlspecialchars($resarr_subcat['subcatpic']);

                    // Print the HTML structure for each subcategory item.
                    // This uses your existing .col-sm and .prodlinks styling.
                    print "<div class='col-md-4 col-sm-6 mb-4'> <!-- Using Bootstrap grid for responsiveness -->
                                <div class='col-sm'> <!-- Your original styling wrapper -->
                                    <a href='showproduct.php?subcatid=$subcat_id_display' class='prodlinks'>
                                        <img src='uploads/$subcat_pic_filename' style='width:100%; max-width:250px; height:200px; object-fit:contain; margin-bottom:15px;' alt='" . $subcat_name_to_display . "'>
                                        <span class='lead' style='display:block; font-size: 1.1rem;'>$subcat_name_to_display</span>
                                    </a>
                                </div>
                              </div>";
                }
            }
            mysqli_stmt_close($stmt_subcat); // Close the subcategory statement.
        } else {
            // If preparing the statement for subcategories failed.
            print "<div class='col-12 text-center'><p class='text-danger'>Error fetching subcategories.</p></div>";
            // Log mysqli_error($connection) in a real application
        }

        mysqli_close($connection); // Close the database connection finally.
        ?>
    </div> <!-- /.row -->
</div> <!-- /.container -->

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

<?php require_once("footer.php"); // Include the site footer ?>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>
</body>
</html>
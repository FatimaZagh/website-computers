<?php
session_start();
ob_start(); // Start output buffering

// Admin security checks (assuming these are necessary)
if (!isset($_SESSION["pname"]) || $_SESSION["usertype"] !== "admin") {
    // header("location:admin_login.php");
    // exit;
}

require_once("vars.php"); // For db credentials

$add_message = ""; // To store success/error messages for adding product
$selected_cat_id = ""; // To store the selected category ID after "Show"
$subcategories_options = ""; // To store HTML options for subcategories

// Initialize database tables for color variants and image gallery if they don't exist
function initializeColorVariantTables() {
    $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);

    // Create product_colors table
    $create_colors_table = "
        CREATE TABLE IF NOT EXISTS product_colors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            color_name VARCHAR(100) NOT NULL,
            color_code VARCHAR(7) DEFAULT NULL,
            is_default TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    // Create product_images table
    $create_images_table = "
        CREATE TABLE IF NOT EXISTS product_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            color_id INT DEFAULT NULL,
            image_path VARCHAR(500) NOT NULL,
            image_order INT DEFAULT 0,
            is_primary TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_id (product_id),
            INDEX idx_color_id (color_id),
            FOREIGN KEY (color_id) REFERENCES product_colors(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    mysqli_query($connection, $create_colors_table);
    mysqli_query($connection, $create_images_table);
    mysqli_close($connection);
}

// Initialize tables on page load
initializeColorVariantTables();

// Handle "Show Subcategories" button
if (isset($_POST["showsubcat"]) && !empty($_POST["cat"])) {
    $selected_cat_id = $_POST["cat"];
}

// Handle "Add Product" button
if (isset($_POST["btn"])) {
    $cid = $_POST["cat"];
    $scid = $_POST["subcat"];
    $pname_raw = trim($_POST["productname"]);
    $rate_raw = trim($_POST["rate"]);
    $discount_raw = trim($_POST["discount"]);
    $description_raw = trim($_POST["description"]);
    $stock_raw = trim($_POST["stock"]);
    $productpic = "defaultpic.png";

    // Get color variants and images data
    $colors_data = isset($_POST['colors_data']) ? json_decode($_POST['colors_data'], true) : [];
    $image_color_assignments = isset($_POST['image_color_assignments']) ? json_decode($_POST['image_color_assignments'], true) : [];
    $uploaded_images = [];

    // Debug logging (remove in production)
    error_log("=== FORM SUBMISSION DEBUG ===");
    error_log("Colors data: " . print_r($colors_data, true));
    error_log("Image assignments: " . print_r($image_color_assignments, true));
    error_log("POST colors_data raw: " . ($_POST['colors_data'] ?? 'NOT SET'));
    error_log("POST image_color_assignments raw: " . ($_POST['image_color_assignments'] ?? 'NOT SET'));

    // Validation
    if (empty($cid)) {
        $add_message = "<p class='text-danger'>Please select a Category.</p>";
    } elseif (empty($scid)) {
        $add_message = "<p class='text-danger'>Please select a Sub-Category.</p>";
    } elseif (empty($pname_raw)) {
        $add_message = "<p class='text-danger'>Product Name cannot be empty.</p>";
    } elseif (!is_numeric($rate_raw) || $rate_raw < 0) {
        $add_message = "<p class='text-danger'>Rate must be a valid non-negative number.</p>";
    } elseif (!is_numeric($discount_raw) || $discount_raw < 0 || $discount_raw > 100) {
        $add_message = "<p class='text-danger'>Discount must be a number between 0 and 100.</p>";
    } elseif (empty($description_raw)) {
        $add_message = "<p class='text-danger'>Description cannot be empty.</p>";
    } elseif (!ctype_digit($stock_raw) || $stock_raw < 0) { // ctype_digit checks if all chars are digits
        $add_message = "<p class='text-danger'>Stock must be a valid non-negative integer.</p>";
    } else {
        // Process multiple image uploads
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $upload_dir = "uploads/";

        // Debug: Log file upload info
        error_log("=== IMAGE UPLOAD DEBUG ===");
        error_log("FILES array: " . print_r($_FILES, true));

        // Process main product images
        if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
            error_log("Processing main product images...");

            for ($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
                if (!empty($_FILES['product_images']['name'][$i]) && $_FILES['product_images']['error'][$i] == 0) {
                    error_log("Processing image $i: " . $_FILES['product_images']['name'][$i]);

                    if (in_array($_FILES['product_images']['type'][$i], $allowed_types)) {
                        $original_name = basename($_FILES['product_images']['name'][$i]);
                        $safe_filename = time() . "_" . $i . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $original_name);
                        $temp_name = $_FILES['product_images']['tmp_name'][$i];

                        if (move_uploaded_file($temp_name, $upload_dir . $safe_filename)) {
                            $uploaded_images[] = $safe_filename;
                            error_log("Successfully uploaded: $safe_filename");
                        } else {
                            error_log("Failed to move uploaded file: $original_name");
                        }
                    } else {
                        error_log("Invalid file type for: " . $_FILES['product_images']['name'][$i] . " (Type: " . $_FILES['product_images']['type'][$i] . ")");
                    }
                } else {
                    error_log("Skipping image $i - empty or error: " . ($_FILES['product_images']['error'][$i] ?? 'unknown error'));
                }
            }
        }

        // Process additional images (if any)
        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            error_log("Processing additional images...");

            for ($i = 0; $i < count($_FILES['additional_images']['name']); $i++) {
                if (!empty($_FILES['additional_images']['name'][$i]) && $_FILES['additional_images']['error'][$i] == 0) {
                    error_log("Processing additional image $i: " . $_FILES['additional_images']['name'][$i]);

                    if (in_array($_FILES['additional_images']['type'][$i], $allowed_types)) {
                        $original_name = basename($_FILES['additional_images']['name'][$i]);
                        $current_count = count($uploaded_images);
                        $safe_filename = time() . "_" . ($current_count + $i) . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $original_name);
                        $temp_name = $_FILES['additional_images']['tmp_name'][$i];

                        if (move_uploaded_file($temp_name, $upload_dir . $safe_filename)) {
                            $uploaded_images[] = $safe_filename;
                            error_log("Successfully uploaded additional: $safe_filename");
                        } else {
                            error_log("Failed to move additional uploaded file: $original_name");
                        }
                    } else {
                        error_log("Invalid file type for additional: " . $_FILES['additional_images']['name'][$i]);
                    }
                }
            }
        }

        error_log("Total uploaded images: " . count($uploaded_images));
        error_log("Uploaded images list: " . print_r($uploaded_images, true));

        // Set primary product image (first uploaded image or default)
        $productpic = !empty($uploaded_images) ? $uploaded_images[0] : "defaultpic.png";

        $conn_add = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection " . mysqli_connect_error());

        // Start transaction
        mysqli_autocommit($conn_add, false);

        try {
            $pname = mysqli_real_escape_string($conn_add, $pname_raw);
            $description = mysqli_real_escape_string($conn_add, $description_raw);
            $rate = (float)$rate_raw;
            $discount = (float)$discount_raw;
            $stock = (int)$stock_raw;

            // Step 1: Insert product first
            $q_insert = "INSERT INTO manageproduct (catid, subcatid, productname, rate, discount, description, stock, productpic)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn_add, $q_insert);
            mysqli_stmt_bind_param($stmt_insert, "iisddsis", $cid, $scid, $pname, $rate, $discount, $description, $stock, $productpic);

            if (!mysqli_stmt_execute($stmt_insert)) {
                throw new Exception("Failed to insert product: " . mysqli_stmt_error($stmt_insert));
            }

            $product_id = mysqli_insert_id($conn_add);
            mysqli_stmt_close($stmt_insert);

            $success_message = "Product added successfully!";

            // Step 2: Insert color variants if provided
            $color_ids = [];
            if (!empty($colors_data)) {
                foreach ($colors_data as $index => $color) {
                    if (!empty($color['name'])) {
                        $color_name = mysqli_real_escape_string($conn_add, $color['name']);
                        $color_code = mysqli_real_escape_string($conn_add, $color['code']);
                        $is_default = (isset($color['isDefault']) && $color['isDefault']) ? 1 : 0;
                        $color_stock = isset($color['stock']) ? intval($color['stock']) : 0;

                        // Check if stock_quantity column exists
                        $check_column = "SHOW COLUMNS FROM product_colors LIKE 'stock_quantity'";
                        $column_result = mysqli_query($conn_add, $check_column);
                        $has_stock_column = mysqli_num_rows($column_result) > 0;

                        if ($has_stock_column) {
                            // Insert color with stock quantity
                            $q_color = "INSERT INTO product_colors (product_id, color_name, color_code, is_default, stock_quantity) VALUES (?, ?, ?, ?, ?)";
                            $stmt_color = mysqli_prepare($conn_add, $q_color);
                            mysqli_stmt_bind_param($stmt_color, "issii", $product_id, $color_name, $color_code, $is_default, $color_stock);
                        } else {
                            // Insert color without stock quantity (backward compatibility)
                            $q_color = "INSERT INTO product_colors (product_id, color_name, color_code, is_default) VALUES (?, ?, ?, ?)";
                            $stmt_color = mysqli_prepare($conn_add, $q_color);
                            mysqli_stmt_bind_param($stmt_color, "issi", $product_id, $color_name, $color_code, $is_default);
                        }

                        if (!mysqli_stmt_execute($stmt_color)) {
                            throw new Exception("Failed to insert color: " . mysqli_stmt_error($stmt_color));
                        }

                        $color_ids[$color['name']] = mysqli_insert_id($conn_add);
                        mysqli_stmt_close($stmt_color);
                    }
                }
                $success_message .= " Color variants added.";
            }

            // Step 3: Insert images with color associations if provided
            if (!empty($uploaded_images)) {
                error_log("=== IMAGE INSERTION PROCESS ===");
                error_log("Total images to insert: " . count($uploaded_images));
                error_log("Uploaded images array: " . print_r($uploaded_images, true));

                foreach ($uploaded_images as $img_index => $image_path) {
                    error_log("--- Processing image $img_index: $image_path ---");
                    $color_assignment = null;

                    // Check if this image has a color assignment
                    if (!empty($image_color_assignments) && isset($image_color_assignments[$img_index])) {
                        $assigned_color_name = $image_color_assignments[$img_index];
                        error_log("Image $img_index assigned to color: $assigned_color_name");
                        if (isset($color_ids[$assigned_color_name])) {
                            $color_assignment = $color_ids[$assigned_color_name];
                            error_log("Color ID found: $color_assignment for color: $assigned_color_name");
                        } else {
                            error_log("ERROR: Color ID not found for color: $assigned_color_name");
                            error_log("Available color IDs: " . print_r($color_ids, true));
                        }
                    } else {
                        error_log("No color assignment for image $img_index");
                        error_log("Image assignments array: " . print_r($image_color_assignments, true));
                    }

                    $is_primary = ($img_index == 0) ? 1 : 0; // First image is primary

                    $q_image = "INSERT INTO product_images (product_id, color_id, image_path, image_order, is_primary) VALUES (?, ?, ?, ?, ?)";
                    $stmt_image = mysqli_prepare($conn_add, $q_image);

                    if (!$stmt_image) {
                        error_log("ERROR: Failed to prepare image statement: " . mysqli_error($conn_add));
                        throw new Exception("Failed to prepare image statement: " . mysqli_error($conn_add));
                    }

                    mysqli_stmt_bind_param($stmt_image, "iisii", $product_id, $color_assignment, $image_path, $img_index, $is_primary);
                    error_log("Attempting to insert: product_id=$product_id, color_id=$color_assignment, image_path=$image_path, image_order=$img_index, is_primary=$is_primary");

                    if (!mysqli_stmt_execute($stmt_image)) {
                        $error_msg = mysqli_stmt_error($stmt_image);
                        error_log("ERROR: Failed to insert image $img_index: $error_msg");
                        mysqli_stmt_close($stmt_image);
                        throw new Exception("Failed to insert image $img_index ($image_path): $error_msg");
                    } else {
                        $insert_id = mysqli_insert_id($conn_add);
                        error_log("SUCCESS: Image $img_index inserted with ID: $insert_id");
                    }
                    mysqli_stmt_close($stmt_image);
                }
                $success_message .= " Images uploaded and assigned.";
                error_log("=== IMAGE INSERTION COMPLETE ===");
            } else {
                error_log("WARNING: No uploaded images to process");
            }

            // Commit transaction
            mysqli_commit($conn_add);
            $add_message = "<p class='text-success'>$success_message</p>";
            $selected_cat_id = ""; // Clear selected category to reset the form

        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn_add);
            $add_message = "<p class='text-danger'>Error adding product: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        mysqli_autocommit($conn_add, true);
        mysqli_close($conn_add);
    }
    // Repopulate $selected_cat_id if "Add Product" failed, so subcategories can still be shown
    if (!empty($cid) && strpos($add_message, 'text-success') === false) {
        $selected_cat_id = $cid;
    }
}


// Populate subcategories if a category is selected (either by "Show" or after a failed "Add Product")
if (!empty($selected_cat_id)) {
    $conn_subcat_list = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection " . mysqli_connect_error());
    $stmt_subcat = mysqli_prepare($conn_subcat_list, "SELECT subcatid, subcatname FROM subcat WHERE catid = ? ORDER BY subcatname ASC");
    mysqli_stmt_bind_param($stmt_subcat, "i", $selected_cat_id);
    mysqli_stmt_execute($stmt_subcat);
    $res_subcat = mysqli_stmt_get_result($stmt_subcat);

    if (mysqli_num_rows($res_subcat) == 0) {
        $subcategories_options = "<option value='' disabled>No Sub-Categories found for this category</option>";
    } else {
        while ($row_subcat = mysqli_fetch_assoc($res_subcat)) {
            // If form was submitted with "Add Product", try to re-select the subcategory
            $selected_attr = (isset($_POST["subcat"]) && $_POST["subcat"] == $row_subcat['subcatid']) ? 'selected' : '';
            $subcategories_options .= "<option value='" . htmlspecialchars($row_subcat['subcatid']) . "' $selected_attr>" . htmlspecialchars($row_subcat['subcatname']) . "</option>";
        }
    }
    mysqli_stmt_close($stmt_subcat);
    mysqli_close($conn_subcat_list);
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Products</title>
        <?php require_once("extfiles.php"); ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --input-bg: rgba(var(--primary-bg), 0.7);
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
                color: #fff; font-weight: 600; margin-bottom: 30px; padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color); text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block;
            }
            .text-center .page-title { display: block; width: fit-content; margin-left: auto; margin-right: auto; }

            .form-section-wrapper {
                margin-bottom: 40px; padding: 30px; background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius); box-shadow: 0 8px 20px var(--shadow-color);
                border: 1px solid var(--border-color);
            }
            .section-heading {
                color: var(--text-color); font-size: 1.5rem; font-weight: 500; margin-bottom: 25px;
                padding-bottom: 10px; border-bottom: 1px solid var(--border-color);
            }
            .form-section-wrapper .form-label { color: var(--text-color-darker); margin-bottom: 0.5rem; font-weight:500;}
            .form-section-wrapper .form-control, .form-section-wrapper .form-select {
                background-color: var(--input-bg); color: var(--text-color);
                border: 1px solid var(--border-color); border-radius: 6px; padding: .5rem .75rem;
            }
            .form-section-wrapper .form-select option { background-color: var(--primary-bg); color: var(--text-color); }
            .form-section-wrapper .form-control::placeholder { color: var(--text-color-darker); }
            .form-section-wrapper .form-control:focus, .form-section-wrapper .form-select:focus {
                background-color: rgba(var(--primary-bg), 0.9); border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color); color: var(--text-color);
            }
            .form-section-wrapper .form-control[type="file"]::-webkit-file-upload-button {
                background: var(--accent-color); color: white; border: none; padding: 0.4rem 0.8rem;
                border-radius: 4px; cursor: pointer; transition: background-color var(--transition-speed);
            }
            .form-section-wrapper .form-control[type="file"]::-webkit-file-upload-button:hover { background: #0095e0; }

            .btn-show-subcat { /* Specific style for show button */
                background-color: var(--accent-color); border-color: var(--accent-color);
                color: #fff; font-weight: 500;
            }
            .btn-show-subcat:hover { background-color: #0095e0; border-color: #0088cc; }

            .btn-add-product { /* Specific style for add product button */
                background-color: var(--success-color); border-color: var(--success-color);
                color: #fff; font-weight: 500;
            }
            .btn-add-product:hover { background-color: #218838; border-color: #1e7e34; }

            .form-message { margin-top: 20px; text-align: center; font-weight: 500; }
            .form-message p { margin-bottom: 0.5rem; }
            .text-success { color: var(--success-color) !important; }
            .text-danger { color: var(--danger-color) !important; }
            .text-warning { color: var(--warning-color) !important; }

            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3); backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px); color: var(--text-color-darker);
                padding: 25px 0; text-align: center; border-top: 1px solid var(--border-color);
                margin-top: auto; position: relative; z-index: 1;
            }
            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .form-section-wrapper { padding: 20px; }
                .section-heading { font-size: 1.3rem; }
            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container page-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Add New Product</h1>
        </div>

        <div class="form-section-wrapper">
            <h2 class="section-heading"><i class="fas fa-box-open me-2"></i>Product Details</h2>
            <form name="addProductForm" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Category Selection -->
                    <div class="col-md-6">
                        <label for="cat" class="form-label">1. Select Category:</label>
                        <select name="cat" id="cat" class="form-select form-select-lg" required>
                            <option value="">-- Choose Category --</option>
                            <?php
                            $conn_cat_list = mysqli_connect(dbhost, dbuname, dbpass, dbname);
                            $q_cat = "SELECT catid, catname FROM managecat ORDER BY catname ASC";
                            $res_cat = mysqli_query($conn_cat_list, $q_cat);
                            if (mysqli_num_rows($res_cat) > 0) {
                                while ($row_cat = mysqli_fetch_assoc($res_cat)) {
                                    $selected_attr = ($selected_cat_id == $row_cat['catid']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($row_cat['catid']) . "' $selected_attr>" . htmlspecialchars($row_cat['catname']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No Categories Available</option>";
                            }
                            mysqli_close($conn_cat_list);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 align-self-end">
                        <button type="submit" name="showsubcat" class="btn btn-show-subcat btn-lg w-100"><i class="fas fa-list-ul me-2"></i>Show Sub-Categories</button>
                    </div>

                    <!-- Sub-Category Selection (conditionally displayed) -->
                    <?php if (!empty($selected_cat_id)): ?>
                        <div class="col-md-12 mt-4"> <hr style="border-color: var(--border-color);"> </div>
                        <div class="col-md-12">
                            <label for="subcat" class="form-label">2. Select Sub-Category:</label>
                            <select name="subcat" id="subcat" class="form-select form-select-lg" required>
                                <option value="">-- Choose Sub-Category --</option>
                                <?php echo $subcategories_options; // Populated by PHP above ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Product Details (conditionally displayed after subcategory is potentially available) -->
                    <?php if (!empty($selected_cat_id)): // Show these fields only if a category has been processed ?>
                        <div class="col-md-12 mt-4"> <hr style="border-color: var(--border-color);"> </div>
                        <div class="col-md-12">
                            <h3 class="section-heading fs-5 mt-3 mb-3"><i class="fas fa-info-circle me-2"></i>3. Enter Product Information</h3>
                        </div>

                        <div class="col-md-12">
                            <label for="productname" class="form-label">Product Name:</label>
                            <input type="text" name="productname" id="productname" placeholder="Enter product name" class="form-control form-control-lg" required value="<?php echo isset($_POST['productname']) ? htmlspecialchars($_POST['productname']) : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="rate" class="form-label">Rate (₪):</label>
                            <input type="number" name="rate" id="rate" placeholder="e.g., 499.99" class="form-control form-control-lg" step="0.01" min="0" required value="<?php echo isset($_POST['rate']) ? htmlspecialchars($_POST['rate']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="discount" class="form-label">Discount (%):</label>
                            <input type="number" name="discount" id="discount" placeholder="e.g., 10 for 10%" class="form-control form-control-lg" step="0.01" min="0" max="100" required value="<?php echo isset($_POST['discount']) ? htmlspecialchars($_POST['discount']) : '0'; ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description:</label>
                            <textarea name="description" id="description" class="form-control form-control-lg" placeholder="Detailed product description" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label">Stock Quantity:</label>
                            <input type="number" name="stock" id="stock" placeholder="e.g., 100" class="form-control form-control-lg" min="0" required value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>">
                        </div>

                        <!-- Image Gallery Section (moved before colors) -->
                        <div class="col-md-12 mt-4">
                            <hr style="border-color: var(--border-color);">
                            <h3 class="section-heading fs-5 mt-3 mb-3"><i class="fas fa-images me-2"></i>4. Product Images</h3>
                        </div>

                        <div class="col-md-12">
                            <div class="image-gallery-container" style="background: rgba(72, 187, 120, 0.05); padding: 25px; border-radius: 15px; border: 2px solid rgba(72, 187, 120, 0.2); box-shadow: 0 4px 15px rgba(72, 187, 120, 0.1);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <label for="product_images" class="form-label mb-1" style="font-weight: 600; color: #2d3748;">Upload Product Images:</label>
                                        <div style="font-size: 0.9em; color: #718096;">Select multiple images or add more later</div>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('additional_images').click()">
                                        <i class="fas fa-plus me-1"></i>Add More Images
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <input type="file" name="product_images[]" id="product_images" class="form-control form-control-lg"
                                           multiple accept="image/jpeg,image/png,image/gif,image/webp" onchange="handleImageUpload(this)">
                                    <input type="file" name="additional_images[]" id="additional_images" class="form-control form-control-lg"
                                           multiple accept="image/jpeg,image/png,image/gif,image/webp" onchange="handleAdditionalImages(this)" style="display: none;">
                                    <small class="form-text" style="color: var(--text-color-darker) !important;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Max 2MB each. JPG, PNG, GIF, WEBP. First image will be the primary image.
                                    </small>
                                </div>

                                <div id="image-preview-container" class="mt-3">
                                    <!-- Image previews will be shown here -->
                                </div>

                                <div class="mt-3 p-3" style="background: rgba(72, 187, 120, 0.15); border-radius: 8px; border-left: 4px solid #48bb78; border: 1px solid rgba(72, 187, 120, 0.3);">
                                    <small style="color: #1a202c; font-weight: 600; font-size: 0.9rem;">
                                        <i class="fas fa-lightbulb me-1" style="color: #48bb78;"></i>
                                        💡 Tip: Upload images first, then add color variants to assign images to specific colors.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Color Variants Section (moved after images) -->
                        <div class="col-md-12 mt-4">
                            <hr style="border-color: var(--border-color);">
                            <h3 class="section-heading fs-5 mt-3 mb-3"><i class="fas fa-palette me-2"></i>5. Color Variants</h3>
                        </div>

                        <div class="col-md-12">
                            <div class="color-variants-container" style="background: rgba(237, 137, 54, 0.05); padding: 25px; border-radius: 15px; border: 2px solid rgba(237, 137, 54, 0.2); box-shadow: 0 4px 15px rgba(237, 137, 54, 0.1);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <label class="form-label mb-1" style="font-weight: 600; color: #2d3748;">Product Colors:</label>
                                        <div style="font-size: 0.9em; color: #718096;">Optional - Add color variants for your product</div>
                                    </div>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="addColorVariant()">
                                        <i class="fas fa-plus me-1"></i>Add Color
                                    </button>
                                </div>

                                <div id="color-variants-list">
                                    <!-- Color variants will be added here dynamically -->
                                </div>

                                <div class="mt-3 p-3" style="background: rgba(237, 137, 54, 0.15); border-radius: 8px; border-left: 4px solid #ed8936; border: 1px solid rgba(237, 137, 54, 0.3);">
                                    <small style="color: #1a202c; font-weight: 600; font-size: 0.9rem;">
                                        <i class="fas fa-info-circle me-1" style="color: #ed8936;"></i>
                                        ℹ️ Color variants are optional. If added, you can assign uploaded images to specific colors.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields to store colors data and image assignments -->
                        <input type="hidden" name="colors_data" id="colors_data" value="">
                        <input type="hidden" name="image_color_assignments" id="image_color_assignments" value="">

                        <div class="col-12 text-center mt-4">
                            <button type="submit" name="btn" class="btn btn-add-product btn-lg px-5 py-2" onclick="return validateAndSubmitForm()">
                                <i class="fas fa-plus-circle me-2"></i>Add Product
                            </button>
                        </div>
                    <?php endif; ?>
                </div> <!-- .row -->

                <?php if (!empty($add_message)): ?>
                    <div class="form-message mt-4"><?php echo $add_message; ?></div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Placeholder for List Products Section - You would add this similar to catmng.php or subcatmang.php -->
        <!--
        <div class="table-section-wrapper mt-5">
            <h2 class="section-heading"><i class="fas fa-list-alt me-2"></i>Existing Products (To be implemented)</h2>
            <p class="text-center p-4" style="color:var(--text-color-darker);">Listing products table will go here.</p>
        </div>
        -->

    </div>

    <script>
        // Global variables for color variants and image management
        let colorVariants = [];
        let uploadedImages = [];
        let colorCounter = 0;

        // Add a new color variant with improved design
        function addColorVariant() {
            colorCounter++;
            const colorId = `color_${colorCounter}`;

            const colorHtml = `
                <div class="color-variant-item mb-4" id="${colorId}" style="background: white; border-radius: 12px; border: 2px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                    <div class="p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label" style="font-weight: 600; color: #2d3748;">Color Name:</label>
                                <input type="text" class="form-control color-name" placeholder="e.g., Midnight Black, Pearl White"
                                       style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; color: #1a202c !important; background: white;" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; color: #2d3748;">Color Preview:</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color color-code" value="#000000"
                                           style="width: 50px; height: 40px; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer;">
                                    <div class="color-preview-circle" style="width: 25px; height: 25px; border-radius: 50%; background: #000000; border: 2px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; color: #2d3748;">Stock Quantity:</label>
                                <input type="number" class="form-control color-stock" placeholder="0" min="0"
                                       style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; color: #1a202c !important; background: white;"
                                       onchange="validateColorStock(this)">
                                <small class="text-muted">Max: <span class="max-stock">0</span></small>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-4" style="background: rgba(102, 126, 234, 0.05); padding: 10px; border-radius: 8px;">
                                    <input type="checkbox" class="form-check-input default-color" id="default_${colorCounter}"
                                           ${colorCounter === 1 ? 'checked disabled' : ''} style="transform: scale(1.2);">
                                    <label class="form-check-label" for="default_${colorCounter}" style="font-weight: 500; color: #2d3748;">
                                        ${colorCounter === 1 ? '⭐ Default Color' : 'Set as Default'}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeColorVariant('${colorId}')"
                                        ${colorCounter === 1 ? 'style="display:none;"' : ''}
                                        style="border-radius: 8px; padding: 8px 12px;">
                                    <i class="fas fa-trash me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('color-variants-list').insertAdjacentHTML('beforeend', colorHtml);

            // Add event listener for color picker to update preview circle
            const newColorInput = document.querySelector(`#${colorId} .color-code`);
            const newPreviewCircle = document.querySelector(`#${colorId} .color-preview-circle`);

            newColorInput.addEventListener('input', function() {
                newPreviewCircle.style.background = this.value;
            });

            updateColorVariants();
            updateMaxStockForColors();
        }

        // Validate color stock against total stock
        function validateColorStock(input) {
            const totalStock = parseInt(document.getElementById('stock').value) || 0;
            const colorStock = parseInt(input.value) || 0;

            if (colorStock > totalStock) {
                input.value = totalStock;
                alert(`Color stock cannot exceed total stock (${totalStock})`);
            }

            // Update the total used stock
            updateStockDistribution();
        }

        // Update max stock display for all colors
        function updateMaxStockForColors() {
            const totalStock = parseInt(document.getElementById('stock').value) || 0;
            const maxStockSpans = document.querySelectorAll('.max-stock');

            maxStockSpans.forEach(span => {
                span.textContent = totalStock;
            });
        }

        // Update stock distribution
        function updateStockDistribution() {
            const totalStock = parseInt(document.getElementById('stock').value) || 0;
            const colorStockInputs = document.querySelectorAll('.color-stock');
            let usedStock = 0;

            colorStockInputs.forEach(input => {
                usedStock += parseInt(input.value) || 0;
            });

            // You could add a display showing remaining stock here if needed
            const remainingStock = totalStock - usedStock;

            // Optional: Show remaining stock somewhere
            // console.log(`Remaining stock: ${remainingStock}`);
        }

        // Remove a color variant
        function removeColorVariant(colorId) {
            const element = document.getElementById(colorId);
            if (element) {
                element.remove();
                updateColorVariants();
                updateImageColorOptions();
            }
        }

        // Update color variants data
        function updateColorVariants() {
            const colorItems = document.querySelectorAll('.color-variant-item');
            colorVariants = [];

            colorItems.forEach((item, index) => {
                const colorName = item.querySelector('.color-name').value;
                const colorCode = item.querySelector('.color-code').value;
                const isDefault = item.querySelector('.default-color').checked;
                const colorStock = parseInt(item.querySelector('.color-stock').value) || 0;

                if (colorName.trim()) {
                    colorVariants.push({
                        name: colorName.trim(),
                        code: colorCode,
                        isDefault: isDefault,
                        stock: colorStock,
                        images: []
                    });
                }
            });

            // Update hidden field
            document.getElementById('colors_data').value = JSON.stringify(colorVariants);
        }

        // Handle initial image upload
        function handleImageUpload(input) {
            const files = input.files;
            const previewContainer = document.getElementById('image-preview-container');
            previewContainer.innerHTML = '';
            uploadedImages = [];

            if (files.length === 0) return;

            processImageFiles(files, 0);
        }

        // Handle additional images
        function handleAdditionalImages(input) {
            const files = input.files;
            if (files.length === 0) return;

            const currentImageCount = uploadedImages.length;
            processImageFiles(files, currentImageCount);

            // Reset the hidden input
            input.value = '';
        }

        // Process image files (shared function)
        function processImageFiles(files, startIndex) {
            const previewContainer = document.getElementById('image-preview-container');

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    uploadedImages.push(file);
                    const actualIndex = startIndex + index;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imagePreview = `
                            <div class="image-preview-item d-inline-block m-2" style="width: 180px;">
                                <div class="card" style="border: 2px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                                    <div style="position: relative;">
                                        <img src="${e.target.result}" class="card-img-top" style="height: 140px; object-fit: cover;">
                                        ${actualIndex === 0 ? '<div style="position: absolute; top: 8px; left: 8px; background: #48bb78; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-star me-1"></i>Primary</div>' : ''}
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeImage(${actualIndex})"
                                                style="position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #2d3748; margin-bottom: 8px;">
                                            Image ${actualIndex + 1}
                                        </div>
                                        <select class="form-select form-select-sm image-color-select" data-image-index="${actualIndex}"
                                                style="border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem; color: #1a202c !important; background: white;">
                                            <option value="" style="color: #1a202c;">Assign to Color</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                        previewContainer.insertAdjacentHTML('beforeend', imagePreview);
                        updateImageColorOptions();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove an image
        function removeImage(imageIndex) {
            // Remove from uploadedImages array
            uploadedImages.splice(imageIndex, 1);

            // Refresh the preview container
            refreshImagePreviews();
        }

        // Refresh image previews after removal
        function refreshImagePreviews() {
            const previewContainer = document.getElementById('image-preview-container');
            previewContainer.innerHTML = '';

            uploadedImages.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = `
                        <div class="image-preview-item d-inline-block m-2" style="width: 180px;">
                            <div class="card" style="border: 2px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                                <div style="position: relative;">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 140px; object-fit: cover;">
                                    ${index === 0 ? '<div style="position: absolute; top: 8px; left: 8px; background: #48bb78; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-star me-1"></i>Primary</div>' : ''}
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage(${index})"
                                            style="position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #2d3748; margin-bottom: 8px;">
                                        Image ${index + 1}
                                    </div>
                                    <select class="form-select form-select-sm image-color-select" data-image-index="${index}"
                                            style="border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem; color: #1a202c !important; background: white;">
                                        <option value="" style="color: #1a202c;">Assign to Color</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    previewContainer.insertAdjacentHTML('beforeend', imagePreview);
                };
                reader.readAsDataURL(file);
            });

            // Update color options after refresh
            setTimeout(updateImageColorOptions, 100);
        }

        // Update color options in image dropdowns
        function updateImageColorOptions() {
            const selects = document.querySelectorAll('.image-color-select');

            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">Assign to Color</option>';

                colorVariants.forEach((color, index) => {
                    const option = document.createElement('option');
                    option.value = color.name; // Use color name as value instead of index
                    option.textContent = color.name;
                    if (currentValue == color.name) option.selected = true;
                    select.appendChild(option);
                });
            });
        }

        // Enhanced validation function with detailed debugging
        function validateAndSubmitForm() {
            console.log('=== FORM VALIDATION STARTED ===');

            // Update color variants first
            updateColorVariants();
            console.log('Updated color variants:', colorVariants);

            // Check if we have images
            console.log('Uploaded images count:', uploadedImages.length);

            // Find all image assignment dropdowns
            const imageSelects = document.querySelectorAll('.image-color-select');
            console.log('Found image assignment dropdowns:', imageSelects.length);

            // Collect image color assignments with detailed logging
            const imageColorAssignments = {};

            imageSelects.forEach((select, index) => {
                const imageIndex = parseInt(select.dataset.imageIndex);
                const selectedColorName = select.value;

                console.log(`Dropdown ${index}: Image ${imageIndex} → "${selectedColorName}"`);

                if (select.value !== '' && selectedColorName !== 'Assign to Color') {
                    imageColorAssignments[imageIndex] = selectedColorName;
                    console.log(`✅ Image ${imageIndex} assigned to color: ${selectedColorName}`);
                } else {
                    console.log(`⚠️ Image ${imageIndex} not assigned to any color`);
                }
            });

            // Update hidden fields
            document.getElementById('colors_data').value = JSON.stringify(colorVariants);
            document.getElementById('image_color_assignments').value = JSON.stringify(imageColorAssignments);

            // Final debug logging
            console.log('=== FINAL FORM DATA ===');
            console.log('Color Variants:', colorVariants);
            console.log('Image Color Assignments:', imageColorAssignments);
            console.log('Colors Data JSON:', document.getElementById('colors_data').value);
            console.log('Image Assignments JSON:', document.getElementById('image_color_assignments').value);

            // Check if assignments are empty
            if (Object.keys(imageColorAssignments).length === 0 && uploadedImages.length > 0) {
                console.warn('⚠️ WARNING: Images uploaded but no color assignments found!');
                if (!confirm('No color assignments found for uploaded images. Images will be saved without color assignments. Continue?')) {
                    return false;
                }
            }

            console.log('=== FORM SUBMISSION PROCEEDING ===');
            return true;
        }

        // Keep the old function for compatibility
        function validateForm() {
            return validateAndSubmitForm();
        }

        // Event listeners for color variant changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('color-name') || e.target.classList.contains('color-code')) {
                updateColorVariants();
                updateImageColorOptions();
            }

            if (e.target.classList.contains('default-color') && e.target.checked) {
                // Uncheck other default checkboxes
                document.querySelectorAll('.default-color').forEach(checkbox => {
                    if (checkbox !== e.target) {
                        checkbox.checked = false;
                    }
                });
                updateColorVariants();
            }
        });

        // Initialize - colors are now optional, so don't add automatically
        document.addEventListener('DOMContentLoaded', function() {
            // addColorVariant(); // Commented out - colors are now optional

            // Add event listener for main stock field
            const stockField = document.getElementById('stock');
            if (stockField) {
                stockField.addEventListener('input', function() {
                    updateMaxStockForColors();
                    updateStockDistribution();
                });
            }
        });
    </script>

    <br><br>
    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); ?>
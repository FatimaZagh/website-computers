<?php
ob_start();
session_start();
require_once("extfiles.php"); // Contains CSS/JS links
require_once("vars.php");     // Contains dbhost, dbuname, dbpass, dbname

if(!isset($_SESSION["pname"]))
{
    header("location:login.php");       //check for login
    exit; // Always exit after a header redirect
}
if($_SESSION["usertype"]!=="admin"){
    header("location:login.php");           //admin pages security
    exit; // Always exit after a header redirect
}

$pid = isset($_GET["pid"]) ? (int)$_GET["pid"] : 0; // Sanitize input

if ($pid == 0) {
    die("Product ID not provided or invalid.");
}

// Establish database connection
$connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

// Fetch current product details
$q_select = "SELECT productid, catid, subcatid, productname, rate, discount, description, stock, productpic FROM manageproduct WHERE productid = $pid";
$res_select = mysqli_query($connection, $q_select) or die("Error in query (select product): " . mysqli_error($connection));

if(mysqli_num_rows($res_select) == 0) {
    mysqli_close($connection);
    die("Product not found.");
}
$product_details = mysqli_fetch_assoc($res_select);

// Fetch color variants for this product
$color_q = "SELECT * FROM product_colors WHERE product_id = ? ORDER BY is_default DESC, id ASC";
$color_stmt = mysqli_prepare($connection, $color_q);
mysqli_stmt_bind_param($color_stmt, "i", $pid);
mysqli_stmt_execute($color_stmt);
$color_result = mysqli_stmt_get_result($color_stmt);
$color_variants = [];
while ($color_row = mysqli_fetch_assoc($color_result)) {
    $color_variants[] = $color_row;
}

// Fetch images for this product
$images_q = "SELECT pi.*, pc.color_name FROM product_images pi
             LEFT JOIN product_colors pc ON pi.color_id = pc.id
             WHERE pi.product_id = ? ORDER BY pi.color_id, pi.image_order";
$images_stmt = mysqli_prepare($connection, $images_q);
mysqli_stmt_bind_param($images_stmt, "i", $pid);
mysqli_stmt_execute($images_stmt);
$images_result = mysqli_stmt_get_result($images_stmt);
$product_images = [];
while ($image_row = mysqli_fetch_assoc($images_result)) {
    $product_images[] = $image_row;
}

?>

<html>
<head>
    <title>Update Product</title>
    <?php
    // require_once("extfiles.php"); // Already included above
    ?>
    <style>
        /* Basic styling for better layout */
        .container { max-width: 700px; margin-top: 20px; }
        .form-control { margin-bottom: 15px; }
        label { font-weight: bold; margin-top: 10px; display: block;}
    </style>

    <script>
        function updateDefaultColor(checkbox) {
            if (checkbox.checked) {
                // Uncheck all other default checkboxes
                document.querySelectorAll('input[name="color_default"]').forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });

                // Also uncheck the new color default checkbox
                const newColorDefault = document.querySelector('input[name="new_color_default"]');
                if (newColorDefault) {
                    newColorDefault.checked = false;
                }
            }
        }

        // Handle new color default checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const newColorDefault = document.querySelector('input[name="new_color_default"]');
            if (newColorDefault) {
                newColorDefault.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck all existing color default checkboxes
                        document.querySelectorAll('input[name="color_default"]').forEach(cb => {
                            cb.checked = false;
                        });
                    }
                });
            }
        });
    </script>
</head>
<body>
<?php
require_once("adminnavbar.php");
?>

<div class="container" style="margin-top:80px;"> <!-- Increased margin-top for navbar -->
    <h2 class="lead display-4 text-center">Update Product</h2>
    <br>

    <form name="form_update_product" method="post" enctype="multipart/form-data" class="text-center">
        <div style="max-width: 400px; margin: auto;"> <!-- Centering form elements -->
            <label for="productname">Product Name</label>
            <input type="text" value="<?php print htmlspecialchars($product_details['productname']); ?>" name="productname" placeholder="Product Name" class="form-control" required>

            <label for="rate">Rate (Price)</label>
            <input type="number" step="0.01" value="<?php print htmlspecialchars($product_details['rate']); ?>" name="rate" placeholder="Rate" class="form-control" required>

            <label for="discount">Discount (%)</label>
            <input type="number" step="0.01" value="<?php print htmlspecialchars($product_details['discount']); ?>" name="discount" placeholder="Discount" class="form-control" required>

            <label for="stock">Total Stock Quantity</label>
            <input type="number" name="stock" value="<?php print htmlspecialchars($product_details['stock']); ?>" placeholder="Total Stock" class="form-control" required>
            <small style="color: #666; font-size: 0.85em;">
                <i class="fas fa-info-circle"></i>
                This is the total stock. Individual color stock quantities are managed below.
                <?php if (!empty($color_variants)): ?>
                    <br><strong>Current color stock total:</strong>
                    <?php
                    $total_color_stock = array_sum(array_column($color_variants, 'stock_quantity'));
                    echo $total_color_stock;
                    if ($total_color_stock != $product_details['stock']) {
                        echo " <span style='color: #dc3545;'>(⚠️ Mismatch with total stock)</span>";
                    }
                    ?>
                <?php endif; ?>
            </small>

            <hr>
            <p><strong>Current Image:</strong></p>
            <?php if(!empty($product_details['productpic']) && file_exists("uploads/" . $product_details['productpic'])): ?>
                <img src='uploads/<?php print htmlspecialchars($product_details['productpic']); ?>' height='100' alt="Current Product Image">
            <?php else: ?>
                <p>No image available or default image.</p>
            <?php endif; ?>
            <br><br>

            <label for="updateppic">Choose new image, if required</label>
            <input type="file" name="updateppic" class="form-control-file">
            <br><br>

            <!-- Color Variants Section -->
            <hr>
            <h4>Color Variants</h4>
            <div style="text-align: left; max-width: 600px; margin: auto;">

                <!-- Existing Color Variants -->
                <?php if (!empty($color_variants)): ?>
                <h5 style="color: #28a745; margin-bottom: 15px;">
                    <i class="fas fa-palette"></i> Existing Colors
                </h5>
                <?php foreach ($color_variants as $color): ?>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: <?php echo $color['color_code']; ?>; border: 2px solid #ccc;"></div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <strong style="font-size: 1.1em;"><?php echo htmlspecialchars($color['color_name']); ?></strong>
                            <?php if ($color['is_default']): ?>
                                <span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7em;">DEFAULT</span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <label style="margin: 0; font-size: 0.9em;">
                                Stock:
                                <input type="number"
                                       name="color_stock[<?php echo $color['id']; ?>]"
                                       value="<?php echo $color['stock_quantity'] ?? 0; ?>"
                                       min="0"
                                       style="width: 80px; padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </label>
                            <label style="margin: 0; font-size: 0.9em;">
                                <input type="checkbox"
                                       name="color_default"
                                       value="<?php echo $color['id']; ?>"
                                       <?php echo $color['is_default'] ? 'checked' : ''; ?>
                                       onchange="updateDefaultColor(this)">
                                Set as Default
                            </label>
                        </div>
                    </div>
                    <a href="?pid=<?php echo $pid; ?>&delete_color=<?php echo $color['id']; ?>"
                       onclick="return confirm('Delete this color variant? This will also delete all images assigned to this color.')"
                       style="color: #dc3545; text-decoration: none; padding: 8px;">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p style="color: #666; font-style: italic;">No color variants found. Add some below!</p>
                <?php endif; ?>

                <!-- Add New Color Variant -->
                <hr style="margin: 20px 0;">
                <h5 style="color: #007bff; margin-bottom: 15px;">
                    <i class="fas fa-plus-circle"></i> Add New Color
                </h5>
                <div id="new-color-form" style="padding: 15px; border: 2px dashed #007bff; border-radius: 8px; background: rgba(0, 123, 255, 0.05);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Color Name:</label>
                            <input type="text"
                                   name="new_color_name"
                                   placeholder="e.g., Navy Blue"
                                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Color Code:</label>
                            <input type="color"
                                   name="new_color_code"
                                   value="#000000"
                                   style="width: 100%; height: 40px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Stock Quantity:</label>
                            <input type="number"
                                   name="new_color_stock"
                                   value="0"
                                   min="0"
                                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div style="display: flex; align-items: end;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: bold;">
                                <input type="checkbox" name="new_color_default" value="1">
                                Set as Default Color
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images Section -->
            <hr>
            <h4>Product Images</h4>
            <div style="text-align: left; max-width: 600px; margin: auto;">

                <!-- Existing Images -->
                <?php if (!empty($product_images)): ?>
                <h5 style="color: #28a745; margin-bottom: 15px;">
                    <i class="fas fa-images"></i> Existing Images
                </h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <?php foreach ($product_images as $image): ?>
                    <div style="border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; background: #f8f9fa;">
                        <img src="uploads/<?php echo htmlspecialchars($image['image_path']); ?>"
                             style="width: 120px; height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 8px;">
                        <div style="font-size: 0.85em; margin-bottom: 8px; font-weight: bold;">
                            <?php if ($image['color_name']): ?>
                                <span style="color: #28a745;"><?php echo htmlspecialchars($image['color_name']); ?></span>
                            <?php else: ?>
                                <span style="color: #dc3545;">No Color Assigned</span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            <a href="?pid=<?php echo $pid; ?>&delete_image=<?php echo $image['id']; ?>"
                               onclick="return confirm('Delete this image?')"
                               style="color: #dc3545; font-size: 0.8em; text-decoration: none; padding: 4px 8px; border: 1px solid #dc3545; border-radius: 4px;">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color: #666; font-style: italic; margin-bottom: 20px;">No images found. Add some below!</p>
                <?php endif; ?>

                <!-- Add New Images -->
                <hr style="margin: 20px 0;">
                <h5 style="color: #007bff; margin-bottom: 15px;">
                    <i class="fas fa-plus-circle"></i> Add New Images
                </h5>
                <div style="padding: 15px; border: 2px dashed #28a745; border-radius: 8px; background: rgba(40, 167, 69, 0.05);">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Upload Images:</label>
                        <input type="file"
                               name="new_images[]"
                               multiple
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <small style="color: #666; font-size: 0.85em;">
                            <i class="fas fa-info-circle"></i> Select multiple images. Max 2MB each. JPG, PNG, GIF, WEBP supported.
                        </small>
                    </div>

                    <?php if (!empty($color_variants)): ?>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Assign to Color:</label>
                        <select name="new_images_color" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="">No specific color (general product images)</option>
                            <?php foreach ($color_variants as $color): ?>
                            <option value="<?php echo $color['id']; ?>">
                                <?php echo htmlspecialchars($color['color_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #666; font-size: 0.85em;">
                            <i class="fas fa-lightbulb"></i> All uploaded images will be assigned to the selected color.
                        </small>
                    </div>
                    <?php else: ?>
                    <div style="padding: 10px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
                        <small style="color: #856404;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Add color variants first to assign images to specific colors.
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <input type="submit" name="btn_update" value="Update Product" class="btn btn-success">
                <input type="submit" name="btn_add_color" value="Add New Color" class="btn btn-primary">
                <input type="submit" name="btn_upload_images" value="Upload New Images" class="btn btn-info">
                <?php if (!empty($color_variants)): ?>
                <input type="submit" name="btn_sync_stock" value="Sync Stock Quantities" class="btn btn-warning"
                       title="Update total stock to match sum of color stocks">
                <?php endif; ?>
            </div>

            <?php if (!empty($color_variants)): ?>
            <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                <small style="color: #856404;">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Stock Management Tips:</strong><br>
                    • Update individual color stocks above, then click "Sync Stock Quantities" to update total stock<br>
                    • Or update total stock and it will be distributed among colors proportionally<br>
                    • "Add New Color" will refresh the page so you can then upload images for the new color
                </small>
            </div>
            <?php endif; ?>
            <br><br>
            <?php
            // The delete link can be styled as a button too
            print "<a class='btn btn-danger' href='delproduct.php?pid=$pid' onclick=\"return confirm('Are you sure you want to remove this product?');\">Remove Product</a>";
            ?>
            <a href="viewproducts.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    <br>

    <?php
    // Handle stock synchronization
    if (isset($_POST["btn_sync_stock"])) {
        if (isset($_POST['color_stock']) && is_array($_POST['color_stock'])) {
            mysqli_autocommit($connection, false);

            try {
                $total_color_stock = 0;

                // Update individual color stocks and calculate total
                foreach ($_POST['color_stock'] as $color_id => $stock_qty) {
                    $color_id = (int)$color_id;
                    $stock_qty = max(0, (int)$stock_qty); // Ensure non-negative
                    $total_color_stock += $stock_qty;

                    $update_color_stock = "UPDATE product_colors SET stock_quantity = ? WHERE id = ? AND product_id = ?";
                    $stmt = mysqli_prepare($connection, $update_color_stock);
                    mysqli_stmt_bind_param($stmt, "iii", $stock_qty, $color_id, $pid);

                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to update color stock for color ID $color_id");
                    }
                    mysqli_stmt_close($stmt);
                }

                // Update total product stock to match color stocks
                $update_total_stock = "UPDATE manageproduct SET stock = ? WHERE productid = ?";
                $stmt = mysqli_prepare($connection, $update_total_stock);
                mysqli_stmt_bind_param($stmt, "ii", $total_color_stock, $pid);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to update total product stock");
                }
                mysqli_stmt_close($stmt);

                mysqli_commit($connection);
                print "<div class='alert alert-success mt-3'>Stock quantities synchronized! Total stock updated to $total_color_stock.</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'updateproduct.php?pid=$pid'; }, 2000);</script>";

            } catch (Exception $e) {
                mysqli_rollback($connection);
                print "<div class='alert alert-danger mt-3'>Failed to sync stock: " . htmlspecialchars($e->getMessage()) . "</div>";
            }

            mysqli_autocommit($connection, true);
        } else {
            print "<div class='alert alert-warning mt-3'>No color stock data found to synchronize.</div>";
        }
    }

    // Handle adding new color (separate action)
    if (isset($_POST["btn_add_color"])) {
        if (!empty($_POST['new_color_name']) && !empty($_POST['new_color_code'])) {
            $new_color_name = mysqli_real_escape_string($connection, trim($_POST['new_color_name']));
            $new_color_code = mysqli_real_escape_string($connection, $_POST['new_color_code']);
            $new_color_stock = (int)($_POST['new_color_stock'] ?? 0);
            $new_color_default = isset($_POST['new_color_default']) ? 1 : 0;

            mysqli_autocommit($connection, false);

            try {
                // If this is set as default, remove default from others
                if ($new_color_default) {
                    $remove_defaults = "UPDATE product_colors SET is_default = 0 WHERE product_id = ?";
                    $stmt = mysqli_prepare($connection, $remove_defaults);
                    mysqli_stmt_bind_param($stmt, "i", $pid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $insert_color = "INSERT INTO product_colors (product_id, color_name, color_code, stock_quantity, is_default) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $insert_color);
                mysqli_stmt_bind_param($stmt, "issii", $pid, $new_color_name, $new_color_code, $new_color_stock, $new_color_default);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to add new color variant");
                }

                mysqli_stmt_close($stmt);
                mysqli_commit($connection);

                print "<div class='alert alert-success mt-3'>New color '$new_color_name' added successfully! You can now upload images for this color.</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'updateproduct.php?pid=$pid'; }, 2000);</script>";

            } catch (Exception $e) {
                mysqli_rollback($connection);
                print "<div class='alert alert-danger mt-3'>Failed to add color: " . htmlspecialchars($e->getMessage()) . "</div>";
            }

            mysqli_autocommit($connection, true);
        } else {
            print "<div class='alert alert-warning mt-3'>Please fill in color name and select a color code.</div>";
        }
    }

    // Handle uploading new images (separate action)
    if (isset($_POST["btn_upload_images"])) {
        if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
            $new_images_color_id = !empty($_POST['new_images_color']) ? (int)$_POST['new_images_color'] : null;
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $uploaded_count = 0;

            mysqli_autocommit($connection, false);

            try {
                for ($i = 0; $i < count($_FILES['new_images']['name']); $i++) {
                    if (!empty($_FILES['new_images']['name'][$i]) && $_FILES['new_images']['error'][$i] == 0) {
                        if (in_array($_FILES['new_images']['type'][$i], $allowed_types)) {
                            $original_name = basename($_FILES['new_images']['name'][$i]);
                            $safe_filename = time() . "_" . $i . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $original_name);
                            $temp_name = $_FILES['new_images']['tmp_name'][$i];

                            if (move_uploaded_file($temp_name, "uploads/" . $safe_filename)) {
                                // Get next image order
                                $order_query = "SELECT COALESCE(MAX(image_order), -1) + 1 as next_order FROM product_images WHERE product_id = ?";
                                $stmt = mysqli_prepare($connection, $order_query);
                                mysqli_stmt_bind_param($stmt, "i", $pid);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $order_row = mysqli_fetch_assoc($result);
                                $image_order = $order_row['next_order'];
                                mysqli_stmt_close($stmt);

                                // Insert image record
                                $insert_image = "INSERT INTO product_images (product_id, color_id, image_path, image_order, is_primary) VALUES (?, ?, ?, ?, 0)";
                                $stmt = mysqli_prepare($connection, $insert_image);
                                mysqli_stmt_bind_param($stmt, "iisi", $pid, $new_images_color_id, $safe_filename, $image_order);

                                if (mysqli_stmt_execute($stmt)) {
                                    $uploaded_count++;
                                } else {
                                    unlink("uploads/" . $safe_filename);
                                    throw new Exception("Failed to save image to database");
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                throw new Exception("Failed to upload image file");
                            }
                        }
                    }
                }

                mysqli_commit($connection);
                print "<div class='alert alert-success mt-3'>$uploaded_count image(s) uploaded successfully!</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'updateproduct.php?pid=$pid'; }, 2000);</script>";

            } catch (Exception $e) {
                mysqli_rollback($connection);
                print "<div class='alert alert-danger mt-3'>Failed to upload images: " . htmlspecialchars($e->getMessage()) . "</div>";
            }

            mysqli_autocommit($connection, true);
        } else {
            print "<div class='alert alert-warning mt-3'>Please select images to upload.</div>";
        }
    }

    // Handle color deletion
    if (isset($_GET['delete_color'])) {
        $color_id = intval($_GET['delete_color']);

        mysqli_autocommit($connection, false);

        try {
            // Get images to delete files
            $get_images_q = "SELECT image_path FROM product_images WHERE color_id = ?";
            $stmt = mysqli_prepare($connection, $get_images_q);
            mysqli_stmt_bind_param($stmt, "i", $color_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $images_to_delete = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $images_to_delete[] = $row['image_path'];
            }
            mysqli_stmt_close($stmt);

            // Delete associated images from database
            $delete_images_q = "DELETE FROM product_images WHERE color_id = ?";
            $stmt = mysqli_prepare($connection, $delete_images_q);
            mysqli_stmt_bind_param($stmt, "i", $color_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Delete the color
            $delete_color_q = "DELETE FROM product_colors WHERE id = ? AND product_id = ?";
            $stmt = mysqli_prepare($connection, $delete_color_q);
            mysqli_stmt_bind_param($stmt, "ii", $color_id, $pid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($connection);

            // Delete image files after successful database operations
            foreach ($images_to_delete as $image_path) {
                if (file_exists("uploads/" . $image_path)) {
                    unlink("uploads/" . $image_path);
                }
            }

            header("Location: updateproduct.php?pid=$pid&msg=color_deleted");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($connection);
            header("Location: updateproduct.php?pid=$pid&error=delete_failed");
            exit;
        }

        mysqli_autocommit($connection, true);
    }

    // Handle image deletion
    if (isset($_GET['delete_image'])) {
        $image_id = intval($_GET['delete_image']);

        // Get image path for file deletion
        $get_image_q = "SELECT image_path FROM product_images WHERE id = ? AND product_id = ?";
        $stmt = mysqli_prepare($connection, $get_image_q);
        mysqli_stmt_bind_param($stmt, "ii", $image_id, $pid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Delete file from uploads folder
            if (file_exists("uploads/" . $row['image_path'])) {
                unlink("uploads/" . $row['image_path']);
            }

            // Delete from database
            $delete_image_q = "DELETE FROM product_images WHERE id = ? AND product_id = ?";
            $stmt = mysqli_prepare($connection, $delete_image_q);
            mysqli_stmt_bind_param($stmt, "ii", $image_id, $pid);
            mysqli_stmt_execute($stmt);
        }

        header("Location: updateproduct.php?pid=$pid&msg=image_deleted");
        exit;
    }

    if (isset($_POST["btn_update"])) {
        $productname = mysqli_real_escape_string($connection, $_POST["productname"]);
        $rate = (float)$_POST["rate"]; // Ensure it's a float
        $discount = (float)$_POST["discount"]; // Ensure it's a float
        $stock = (int)$_POST["stock"]; // Ensure it's an integer
        // $pid is already defined and sanitized from $_GET

        $scpic = $product_details['productpic']; // Default to old picture
        $update_messages = [];

        // Start transaction for multiple operations
        mysqli_autocommit($connection, false);

        try {
            // Handle main product image update
            if (isset($_FILES["updateppic"]) && $_FILES["updateppic"]["error"] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (in_array($_FILES["updateppic"]["type"], $allowed_types)) {
                    // Delete old picture if it's not the default and a new one is uploaded
                    if ($product_details['productpic'] != "defaultpic.png" && file_exists("uploads/" . $product_details['productpic'])) {
                        unlink("uploads/" . $product_details['productpic']);
                    }
                    $scpic = time() . "_main_" . basename($_FILES["updateppic"]["name"]);
                    $tname = $_FILES["updateppic"]["tmp_name"];
                    if (!move_uploaded_file($tname, "uploads/$scpic")) {
                        throw new Exception("Failed to upload main product image.");
                    }
                    $update_messages[] = "Main product image updated.";
                } else {
                    $update_messages[] = "Invalid main image file type. Main image not updated.";
                }
            }

            // Update basic product information
            $q_update = "UPDATE manageproduct SET
                            productname = '$productname',
                            rate = '$rate',
                            discount = '$discount',
                            stock = '$stock',
                            productpic = '$scpic'
                         WHERE productid = '$pid'";

            if (!mysqli_query($connection, $q_update)) {
                throw new Exception("Failed to update product information: " . mysqli_error($connection));
            }
            $update_messages[] = "Product information updated.";

            // Handle color stock updates and default changes
            if (isset($_POST['color_stock']) && is_array($_POST['color_stock'])) {
                foreach ($_POST['color_stock'] as $color_id => $stock_qty) {
                    $color_id = (int)$color_id;
                    $stock_qty = (int)$stock_qty;

                    $update_color_stock = "UPDATE product_colors SET stock_quantity = ? WHERE id = ? AND product_id = ?";
                    $stmt = mysqli_prepare($connection, $update_color_stock);
                    mysqli_stmt_bind_param($stmt, "iii", $stock_qty, $color_id, $pid);

                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to update color stock for color ID $color_id");
                    }
                    mysqli_stmt_close($stmt);
                }
                $update_messages[] = "Color stock quantities updated.";
            }

            // Handle default color changes
            if (isset($_POST['color_default'])) {
                $new_default_id = (int)$_POST['color_default'];

                // First, remove default from all colors
                $remove_defaults = "UPDATE product_colors SET is_default = 0 WHERE product_id = ?";
                $stmt = mysqli_prepare($connection, $remove_defaults);
                mysqli_stmt_bind_param($stmt, "i", $pid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Set new default
                $set_default = "UPDATE product_colors SET is_default = 1 WHERE id = ? AND product_id = ?";
                $stmt = mysqli_prepare($connection, $set_default);
                mysqli_stmt_bind_param($stmt, "ii", $new_default_id, $pid);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to set new default color");
                }
                mysqli_stmt_close($stmt);
                $update_messages[] = "Default color updated.";
            }

            // Handle new color addition
            if (!empty($_POST['new_color_name']) && !empty($_POST['new_color_code'])) {
                $new_color_name = mysqli_real_escape_string($connection, trim($_POST['new_color_name']));
                $new_color_code = mysqli_real_escape_string($connection, $_POST['new_color_code']);
                $new_color_stock = (int)($_POST['new_color_stock'] ?? 0);
                $new_color_default = isset($_POST['new_color_default']) ? 1 : 0;

                // If this is set as default, remove default from others
                if ($new_color_default) {
                    $remove_defaults = "UPDATE product_colors SET is_default = 0 WHERE product_id = ?";
                    $stmt = mysqli_prepare($connection, $remove_defaults);
                    mysqli_stmt_bind_param($stmt, "i", $pid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $insert_color = "INSERT INTO product_colors (product_id, color_name, color_code, stock_quantity, is_default) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $insert_color);
                mysqli_stmt_bind_param($stmt, "issii", $pid, $new_color_name, $new_color_code, $new_color_stock, $new_color_default);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to add new color variant");
                }

                $new_color_id = mysqli_insert_id($connection);
                mysqli_stmt_close($stmt);
                $update_messages[] = "New color '$new_color_name' added.";
            }

            // Handle new images upload
            if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                $new_images_color_id = !empty($_POST['new_images_color']) ? (int)$_POST['new_images_color'] : null;
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $uploaded_count = 0;

                for ($i = 0; $i < count($_FILES['new_images']['name']); $i++) {
                    if (!empty($_FILES['new_images']['name'][$i]) && $_FILES['new_images']['error'][$i] == 0) {
                        if (in_array($_FILES['new_images']['type'][$i], $allowed_types)) {
                            $original_name = basename($_FILES['new_images']['name'][$i]);
                            $safe_filename = time() . "_" . $i . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $original_name);
                            $temp_name = $_FILES['new_images']['tmp_name'][$i];

                            if (move_uploaded_file($temp_name, "uploads/" . $safe_filename)) {
                                // Get next image order
                                $order_query = "SELECT COALESCE(MAX(image_order), -1) + 1 as next_order FROM product_images WHERE product_id = ?";
                                $stmt = mysqli_prepare($connection, $order_query);
                                mysqli_stmt_bind_param($stmt, "i", $pid);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $order_row = mysqli_fetch_assoc($result);
                                $image_order = $order_row['next_order'];
                                mysqli_stmt_close($stmt);

                                // Insert image record
                                $insert_image = "INSERT INTO product_images (product_id, color_id, image_path, image_order, is_primary) VALUES (?, ?, ?, ?, 0)";
                                $stmt = mysqli_prepare($connection, $insert_image);
                                mysqli_stmt_bind_param($stmt, "iisi", $pid, $new_images_color_id, $safe_filename, $image_order);

                                if (mysqli_stmt_execute($stmt)) {
                                    $uploaded_count++;
                                } else {
                                    // Delete uploaded file if database insert fails
                                    unlink("uploads/" . $safe_filename);
                                }
                                mysqli_stmt_close($stmt);
                            }
                        }
                    }
                }

                if ($uploaded_count > 0) {
                    $update_messages[] = "$uploaded_count new image(s) uploaded.";
                }
            }

            // Commit all changes
            mysqli_commit($connection);

            // Success message
            $success_message = implode(" ", $update_messages);
            print "<div class='alert alert-success mt-3'>$success_message</div>";

            // Refresh the page to show updated data
            echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";

        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($connection);
            print "<div class='alert alert-danger mt-3'>Update failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }

        // Re-enable autocommit
        mysqli_autocommit($connection, true);
    }
    ?>
</div>
<br><br>

<?php
// If connection is still open and not handled by the update block, close it.
if (isset($connection) && $connection) {
    mysqli_close($connection);
}
require_once("footer.php");
ob_end_flush();
?>
</body>
</html>
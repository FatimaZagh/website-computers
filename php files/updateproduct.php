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
        /* Modern Design System */
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #2d3748;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f7fafc;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .card-icon {
            width: 24px;
            height: 24px;
            color: #667eea;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-input {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .current-preview {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .preview-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .preview-info h4 {
            margin: 0 0 8px 0;
            color: #2d3748;
            font-weight: 600;
        }

        .preview-info p {
            margin: 4px 0;
            color: #718096;
            font-size: 0.9rem;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .color-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
        }

        .color-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .color-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .color-swatch {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .color-info h5 {
            margin: 0;
            font-weight: 600;
            color: #2d3748;
        }

        .default-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .color-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .stock-input {
            width: 80px;
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .image-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .image-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 12px;
            border: 2px solid #f7fafc;
        }

        .color-label {
            font-weight: 600;
            color: #48bb78;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .upload-area {
            border: 3px dashed #cbd5e0;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%);
        }

        .action-bar {
            position: sticky;
            bottom: 20px;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 40px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fefcbf 0%, #faf089 100%);
            color: #744210;
            border-left: 4px solid #ed8936;
        }

        .stock-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #718096;
        }

        .stock-bar {
            width: 60px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .stock-fill {
            height: 100%;
            background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-container { padding: 15px; }
            .form-grid { grid-template-columns: 1fr; }
            .color-grid { grid-template-columns: 1fr; }
            .image-gallery { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
            .action-bar { flex-direction: column; }
            .current-preview { flex-direction: column; text-align: center; }
        }
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

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">✨ Update Product</h1>
        <p style="color: #718096; margin: 10px 0 0 0;">Manage your product details, colors, and images</p>
    </div>

    <form name="form_update_product" method="post" enctype="multipart/form-data">

        <!-- Product Information Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-box card-icon"></i>
                <h3 class="card-title">Product Information</h3>
            </div>

            <!-- Current Preview -->
            <div class="current-preview">
                <?php if(!empty($product_details['productpic']) && file_exists("uploads/" . $product_details['productpic'])): ?>
                    <img src='uploads/<?php print htmlspecialchars($product_details['productpic']); ?>' class="preview-image" alt="Current Product Image">
                <?php else: ?>
                    <div class="preview-image" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #718096;">
                        <i class="fas fa-image" style="font-size: 24px;"></i>
                    </div>
                <?php endif; ?>
                <div class="preview-info">
                    <h4><?php echo htmlspecialchars($product_details['productname']); ?></h4>
                    <p><strong>Price:</strong> ₪<?php echo number_format($product_details['rate'], 2); ?></p>
                    <p><strong>Discount:</strong> <?php echo $product_details['discount']; ?>%</p>
                    <p><strong>Stock:</strong> <?php echo $product_details['stock']; ?> units</p>
                    <?php if (!empty($color_variants)): ?>
                        <p><strong>Colors:</strong> <?php echo count($color_variants); ?> variants</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="productname">
                        <i class="fas fa-tag"></i> Product Name
                    </label>
                    <input type="text"
                           value="<?php print htmlspecialchars($product_details['productname']); ?>"
                           name="productname"
                           placeholder="Enter product name"
                           class="form-input"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="rate">
                        <i class="fas fa-shekel-sign"></i> Price (₪)
                    </label>
                    <input type="number"
                           step="0.01"
                           value="<?php print htmlspecialchars($product_details['rate']); ?>"
                           name="rate"
                           placeholder="0.00"
                           class="form-input"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="discount">
                        <i class="fas fa-percent"></i> Discount (%)
                    </label>
                    <input type="number"
                           step="0.01"
                           value="<?php print htmlspecialchars($product_details['discount']); ?>"
                           name="discount"
                           placeholder="0"
                           class="form-input"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="stock">
                        <i class="fas fa-boxes"></i> Total Stock Quantity
                    </label>
                    <input type="number"
                           name="stock"
                           value="<?php print htmlspecialchars($product_details['stock']); ?>"
                           placeholder="0"
                           class="form-input"
                           required>
                    <small style="color: #718096; font-size: 0.85em; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i>
                        Total stock across all color variants
                        <?php if (!empty($color_variants)): ?>
                            <br><strong>Current color stock total:</strong>
                            <?php
                            $total_color_stock = array_sum(array_column($color_variants, 'stock_quantity'));
                            echo $total_color_stock;
                            if ($total_color_stock != $product_details['stock']) {
                                echo " <span style='color: #f56565;'>(⚠️ Mismatch)</span>";
                            } else {
                                echo " <span style='color: #48bb78;'>(✅ Synced)</span>";
                            }
                            ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="form-group">
                <label class="form-label" for="updateppic">
                    <i class="fas fa-camera"></i> Update Main Product Image
                </label>
                <div class="upload-area">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 10px;"></i>
                    <input type="file"
                           name="updateppic"
                           class="form-input"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           style="margin-top: 10px;">
                    <small style="color: #718096; margin-top: 10px; display: block;">
                        Choose a new main image (JPG, PNG, GIF, WEBP - Max 2MB)
                    </small>
                </div>
            </div>
        </div>

        <!-- Color Variants Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-palette card-icon"></i>
                <h3 class="card-title">Color Variants</h3>
            </div>

            <!-- Existing Color Variants -->
            <?php if (!empty($color_variants)): ?>
                <h5 style="color: #48bb78; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i> Existing Colors (<?php echo count($color_variants); ?>)
                </h5>
                <div class="color-grid">
                    <?php foreach ($color_variants as $color): ?>
                    <div class="color-card">
                        <?php if ($color['is_default']): ?>
                            <div class="default-badge">
                                <i class="fas fa-star"></i> DEFAULT
                            </div>
                        <?php endif; ?>

                        <div class="color-header">
                            <div class="color-swatch" style="background: <?php echo $color['color_code']; ?>;"></div>
                            <div class="color-info">
                                <h5><?php echo htmlspecialchars($color['color_name']); ?></h5>
                                <small style="color: #718096;"><?php echo $color['color_code']; ?></small>
                            </div>
                        </div>

                        <div class="color-controls">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label style="font-size: 0.9em; color: #4a5568; font-weight: 600;">Stock:</label>
                                <input type="number"
                                       name="color_stock[<?php echo $color['id']; ?>]"
                                       value="<?php echo $color['stock_quantity'] ?? 0; ?>"
                                       min="0"
                                       class="stock-input">
                            </div>

                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9em; cursor: pointer;">
                                <input type="checkbox"
                                       name="color_default"
                                       value="<?php echo $color['id']; ?>"
                                       <?php echo $color['is_default'] ? 'checked' : ''; ?>
                                       onchange="updateDefaultColor(this)"
                                       style="transform: scale(1.2);">
                                <span style="color: #4a5568; font-weight: 500;">Set as Default</span>
                            </label>

                            <a href="?pid=<?php echo $pid; ?>&delete_color=<?php echo $color['id']; ?>"
                               onclick="return confirm('Delete this color variant? This will also delete all images assigned to this color.')"
                               class="btn btn-danger"
                               style="padding: 8px 12px; font-size: 0.8rem;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #718096;">
                    <i class="fas fa-palette" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 1.1rem; margin: 0;">No color variants found</p>
                    <small>Add some colors below to get started!</small>
                </div>
            <?php endif; ?>

            <!-- Add New Color Section -->
            <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #f7fafc;">
                <h5 style="color: #667eea; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus-circle"></i> Add New Color Variant
                </h5>
                <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
                           border: 2px dashed #667eea; border-radius: 15px; padding: 25px;">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag"></i> Color Name
                            </label>
                            <input type="text"
                                   name="new_color_name"
                                   placeholder="e.g., Navy Blue, Forest Green"
                                   class="form-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-palette"></i> Color Code
                            </label>
                            <input type="color"
                                   name="new_color_code"
                                   value="#000000"
                                   class="form-input"
                                   style="height: 50px; padding: 5px;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-boxes"></i> Stock Quantity
                            </label>
                            <input type="number"
                                   name="new_color_stock"
                                   value="0"
                                   min="0"
                                   class="form-input">
                        </div>

                        <div class="form-group" style="display: flex; align-items: end;">
                            <label style="display: flex; align-items: center; gap: 12px; font-weight: 600; color: #4a5568; cursor: pointer;">
                                <input type="checkbox"
                                       name="new_color_default"
                                       value="1"
                                       style="transform: scale(1.3);">
                                <span><i class="fas fa-star" style="color: #ffd700;"></i> Set as Default Color</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Images Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-images card-icon"></i>
                <h3 class="card-title">Product Images</h3>
            </div>

            <!-- Existing Images -->
            <?php if (!empty($product_images)): ?>
                <h5 style="color: #48bb78; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i> Existing Images (<?php echo count($product_images); ?>)
                </h5>
                <div class="image-gallery">
                    <?php foreach ($product_images as $image): ?>
                    <div class="image-card">
                        <img src="uploads/<?php echo htmlspecialchars($image['image_path']); ?>"
                             class="image-thumbnail"
                             alt="Product Image">
                        <div class="color-label">
                            <?php if ($image['color_name']): ?>
                                <i class="fas fa-palette"></i> <?php echo htmlspecialchars($image['color_name']); ?>
                            <?php else: ?>
                                <span style="color: #f56565;"><i class="fas fa-exclamation-triangle"></i> No Color</span>
                            <?php endif; ?>
                        </div>
                        <a href="?pid=<?php echo $pid; ?>&delete_image=<?php echo $image['id']; ?>"
                           onclick="return confirm('Delete this image?')"
                           class="btn btn-danger"
                           style="padding: 6px 12px; font-size: 0.8rem;">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #718096;">
                    <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 1.1rem; margin: 0;">No images found</p>
                    <small>Upload some images below to get started!</small>
                </div>
            <?php endif; ?>

            <!-- Add New Images Section -->
            <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #f7fafc;">
                <h5 style="color: #48bb78; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus-circle"></i> Upload New Images
                </h5>
                <div style="background: linear-gradient(135deg, rgba(72, 187, 120, 0.05) 0%, rgba(56, 161, 105, 0.05) 100%);
                           border: 2px dashed #48bb78; border-radius: 15px; padding: 25px;">

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cloud-upload-alt"></i> Select Images
                        </label>
                        <div class="upload-area">
                            <i class="fas fa-images" style="font-size: 2.5rem; color: #cbd5e0; margin-bottom: 15px;"></i>
                            <input type="file"
                                   name="new_images[]"
                                   multiple
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="form-input"
                                   style="margin-top: 10px;">
                            <small style="color: #718096; margin-top: 10px; display: block;">
                                <i class="fas fa-info-circle"></i> Select multiple images • Max 2MB each • JPG, PNG, GIF, WEBP supported
                            </small>
                        </div>
                    </div>

                    <?php if (!empty($color_variants)): ?>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-palette"></i> Assign to Color
                        </label>
                        <select name="new_images_color" class="form-input">
                            <option value="">🎨 No specific color (general product images)</option>
                            <?php foreach ($color_variants as $color): ?>
                            <option value="<?php echo $color['id']; ?>">
                                🎨 <?php echo htmlspecialchars($color['color_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #718096; font-size: 0.85em; margin-top: 5px; display: block;">
                            <i class="fas fa-lightbulb"></i> All uploaded images will be assigned to the selected color
                        </small>
                    </div>
                    <?php else: ?>
                    <div style="padding: 15px; background: linear-gradient(135deg, #fefcbf 0%, #faf089 100%);
                               border-radius: 12px; border-left: 4px solid #ed8936;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-exclamation-triangle" style="color: #ed8936; font-size: 1.2rem;"></i>
                            <div>
                                <strong style="color: #744210;">Add color variants first</strong>
                                <p style="margin: 5px 0 0 0; color: #744210; font-size: 0.9em;">
                                    Create color variants to assign images to specific colors
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <input type="submit" name="btn_update" value="💾 Update Product" class="btn btn-success">
            <input type="submit" name="btn_add_color" value="🎨 Add New Color" class="btn btn-primary">
            <input type="submit" name="btn_upload_images" value="📸 Upload Images" class="btn btn-info">
            <?php if (!empty($color_variants)): ?>
            <input type="submit" name="btn_sync_stock" value="🔄 Sync Stock" class="btn btn-warning"
                   title="Update total stock to match sum of color stocks">
            <?php endif; ?>
            <a href="viewproducts.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
            <?php
            // The delete link styled as a modern button
            print "<a class='btn btn-danger' href='delproduct.php?pid=$pid' onclick=\"return confirm('⚠️ Are you sure you want to remove this product? This action cannot be undone.');\">
                    <i class='fas fa-trash'></i> Remove Product
                   </a>";
            ?>
        </div>

        <?php if (!empty($color_variants)): ?>
        <!-- Tips Card -->
        <div class="card" style="margin-top: 20px;">
            <div style="background: linear-gradient(135deg, #fefcbf 0%, #faf089 100%);
                       padding: 20px; border-radius: 15px; border-left: 4px solid #ed8936;">
                <div style="display: flex; align-items: start; gap: 15px;">
                    <i class="fas fa-lightbulb" style="color: #ed8936; font-size: 1.5rem; margin-top: 2px;"></i>
                    <div>
                        <h5 style="color: #744210; margin: 0 0 10px 0;">💡 Stock Management Tips</h5>
                        <ul style="color: #744210; margin: 0; padding-left: 20px; line-height: 1.6;">
                            <li>Update individual color stocks above, then click <strong>"Sync Stock"</strong> to update total stock</li>
                            <li>Or update total stock and it will be distributed among colors proportionally</li>
                            <li><strong>"Add New Color"</strong> will refresh the page so you can then upload images for the new color</li>
                            <li>Use <strong>"Upload Images"</strong> to add multiple images and assign them to specific colors</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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

<!-- Success/Error Messages Styling -->
<style>
.alert {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading states for buttons */
.btn:active {
    transform: translateY(0) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
}

/* Hover effects for cards */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

/* Focus states for inputs */
.form-input:focus {
    transform: translateY(-1px);
}

/* Mobile optimizations */
@media (max-width: 480px) {
    .page-title {
        font-size: 2rem;
    }

    .card {
        padding: 20px;
        margin-bottom: 20px;
    }

    .action-bar {
        position: relative;
        bottom: auto;
        margin-top: 20px;
    }
}
</style>

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
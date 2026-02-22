<?php session_start(); ob_start(); ?>
<html>
<head>
    <title>Categories</title>
    <?php require_once("extfiles.php")?>
    <style>
        body {
            background-color: white;
            color: black;
        }
        .col-sm {
            margin: 10px;
            background-color: #fafafa;
            border-style: solid;
            border-color: #fafafa;
            padding: 10px;
            border-radius: 4px;
            color: black;
        }
        .col-sm:hover {
            border-style: solid;
            border-color: black;
            background-color: #fafafa;
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
        .prod-image {
            width: 30%;
            float: left;
            padding-top: 50px;
        }
        .prod-details {
            width: 70%;
            float: right;
            background-color: #fafafa;
            padding: 20px;
        }
        @media (max-width: 940px) {
            .prod-image {
                width: 100%;
                margin-bottom: 20px;
            }
            .prod-details {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php require_once("header.php")?>

<div class="container" style="margin-top:100px;">
    <div class="row">
        <?php
        $pid = $_GET["pid"];
        require_once("vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection".mysqli_connect_error());

        // Get product details
        $q = "select * from manageproduct where productid=$pid";
        $res = mysqli_query($connection, $q) or die("Error in query" . mysqli_error($connection));
        $resarr = mysqli_fetch_array($res);

        // Get color variants for this product
        $color_q = "SELECT * FROM product_colors WHERE product_id = ? ORDER BY is_default DESC, id ASC";
        $color_stmt = mysqli_prepare($connection, $color_q);
        mysqli_stmt_bind_param($color_stmt, "i", $pid);
        mysqli_stmt_execute($color_stmt);
        $color_result = mysqli_stmt_get_result($color_stmt);
        $color_variants = [];
        $default_color = null;

        while ($color_row = mysqli_fetch_assoc($color_result)) {
            $color_variants[] = $color_row;
            if ($color_row['is_default'] == 1) {
                $default_color = $color_row;
            }
        }

        // Get images for this product
        $images_q = "SELECT pi.*, pc.color_name FROM product_images pi
                     LEFT JOIN product_colors pc ON pi.color_id = pc.id
                     WHERE pi.product_id = ? ORDER BY pi.color_id, pi.image_order";
        $images_stmt = mysqli_prepare($connection, $images_q);
        mysqli_stmt_bind_param($images_stmt, "i", $pid);
        mysqli_stmt_execute($images_stmt);
        $images_result = mysqli_stmt_get_result($images_stmt);
        $product_images = [];

        while ($image_row = mysqli_fetch_assoc($images_result)) {
            $color_id = $image_row['color_id'] ?? 'default';
            if (!isset($product_images[$color_id])) {
                $product_images[$color_id] = [];
            }
            $product_images[$color_id][] = $image_row;
        }

        // Debug: Log what we found
        error_log("Product $pid - Colors found: " . count($color_variants));
        error_log("Product $pid - Images found: " . json_encode($product_images));

        mysqli_close($connection);
        ?>

        <div class="container">
            <div class="prod-image">
                <form name="cart" method="POST">
                    <?php
                    $ramount = ($resarr[4] * $resarr[5]) / 100;
                    $discount = $resarr[4] - $ramount;

                    // Display main product image with color variant support
                    echo "<div id='product-image-gallery'>";

                    // If product has color variants and images
                    if (!empty($color_variants) && !empty($product_images)) {
                        // Show default color images or first available images
                        $default_images = [];
                        if ($default_color && isset($product_images[$default_color['id']])) {
                            $default_images = $product_images[$default_color['id']];
                        } else if (!empty($product_images)) {
                            $default_images = reset($product_images); // Get first color's images
                        }

                        if (!empty($default_images)) {
                            echo "<img id='main-product-image' src='uploads/{$default_images[0]['image_path']}'
                                      style='width:300px;height:300px;object-fit:contain;'
                                      alt='{$resarr[3]}'>";

                            // Show thumbnail gallery if multiple images
                            if (count($default_images) > 1) {
                                echo "<div id='image-thumbnails' style='margin-top: 10px; display: flex; gap: 5px; flex-wrap: wrap;'>";
                                foreach ($default_images as $index => $img) {
                                    $active_class = $index === 0 ? 'active' : '';
                                    echo "<img src='uploads/{$img['image_path']}'
                                              class='thumbnail-image $active_class'
                                              style='width: 60px; height: 60px; object-fit: cover; border: 2px solid " . ($index === 0 ? '#007bff' : '#ddd') . "; cursor: pointer; border-radius: 4px;'
                                              onclick='changeMainImage(this, \"{$img['image_path']}\")'>";
                                }
                                echo "</div>";
                            }
                        } else {
                            // Fallback to default product image
                            echo "<img id='main-product-image' src='uploads/{$resarr[8]}'
                                      style='width:300px;height:300px;object-fit:contain;'
                                      alt='{$resarr[3]}'>";
                        }
                    } else {
                        // No color variants, show default product image
                        echo "<img id='main-product-image' src='uploads/{$resarr[8]}'
                                  style='width:300px;height:300px;object-fit:contain;'
                                  alt='{$resarr[3]}'>";
                    }

                    echo "</div>";
                    ?>
            </div>
            <div class="prod-details">
                <?php
                require_once("vars.php");
                $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection".mysqli_connect_error());
                $q = "select * from manageproduct where productid=$pid";
                $res = mysqli_query($connection, $q) or die("Error in query" . mysqli_error($connection));
                $resarr = mysqli_fetch_array($res);
                mysqli_close($connection);
                print "
                            <h1 class='lead display-4' style='text-align:center'>$resarr[3]</h1><br>
                            <p class='lead'><b>Description </b><br>$resarr[6]</p><br>
                            <p class='lead'><del><b>NIS $resarr[4]</b></del><b> &nbsp;off</b></p>
                            <p class='lead'><b>Price -</b> $discount <b class='text-success' style='padding-left:20px;'>$resarr[5]% off</b></p>";

                // Add color selector if product has color variants
                if (!empty($color_variants)) {
                    echo "<div class='color-selector-section' style='margin: 20px 0; padding: 15px; background: rgba(102, 126, 234, 0.05); border-radius: 10px;'>";
                    echo "<label class='lead' style='display: block; margin-bottom: 10px;'><b>Available Colors:</b></label>";
                    echo "<div class='color-options' style='display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;'>";

                    foreach ($color_variants as $index => $color) {
                        $is_default = $color['is_default'] == 1;
                        $selected_class = $is_default ? 'selected' : '';
                        $border_style = $is_default ? 'border: 3px solid #007bff;' : 'border: 2px solid #ddd;';

                        echo "<div class='color-option $selected_class'
                                  data-color-id='{$color['id']}'
                                  data-color-name='{$color['color_name']}'
                                  data-color-code='{$color['color_code']}'
                                  onclick='selectColor(this)'
                                  style='cursor: pointer; padding: 8px 12px; border-radius: 8px; $border_style background: white; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;'>";

                        echo "<div style='width: 20px; height: 20px; border-radius: 50%; background: {$color['color_code']}; border: 1px solid #ccc;'></div>";
                        echo "<span style='font-weight: 500; color: #2d3748;'>{$color['color_name']}</span>";

                        // Show stock info if available
                        if (isset($color['stock_quantity']) && $color['stock_quantity'] > 0) {
                            echo "<small style='color: #48bb78;'>({$color['stock_quantity']} in stock)</small>";
                        } else if (isset($color['stock_quantity']) && $color['stock_quantity'] == 0) {
                            echo "<small style='color: #f56565;'>(Out of stock)</small>";
                        }

                        echo "</div>";
                    }

                    echo "</div>";
                    echo "<div id='selected-color-info' style='font-size: 0.9em; color: #718096;'>";
                    if ($default_color) {
                        echo "Selected: <span style='font-weight: 600; color: #2d3748;'>{$default_color['color_name']}</span>";
                    }
                    echo "</div>";
                    echo "</div>";

                    // Hidden field to store selected color
                    echo "<input type='hidden' id='selected_color_id' name='selected_color_id' value='" . ($default_color ? $default_color['id'] : '') . "'>";
                    echo "<input type='hidden' id='selected_color_name' name='selected_color_name' value='" . ($default_color ? $default_color['color_name'] : '') . "'>";
                    echo "<input type='hidden' id='selected_color_code' name='selected_color_code' value='" . ($default_color ? $default_color['color_code'] : '') . "'>";
                }

                echo "<span class='lead'>Quantity -</span>";

                echo "<select name='cart' id='quantity_select' class='form-select form-select-sm' onchange='updateTotalPrice()'><option value=''>Choose</option>";
                if ($resarr[7] > 5) {
                    for ($x = 1; $x <= 5; $x++) {
                        echo "<option value='$x'>$x</option>";
                    }
                } else {
                    for ($x = 1; $x <= $resarr[7]; $x++) {
                        echo "<option value='$x'>$x</option>";
                    }
                }
                echo "</select>";

                // Add total price display
                echo "<div id='total_price_display' style='margin: 15px 0; padding: 12px; background: rgba(102, 126, 234, 0.05); border-radius: 8px; border-left: 4px solid #667eea; display: none;'>
                        <div style='display: flex; justify-content: space-between; align-items: center;'>
                            <span style='font-weight: 500; color: #2d3748;'>Total Price:</span>
                            <span id='total_price_amount' style='font-size: 1.2em; font-weight: bold; color: #48bb78;'>₪0.00</span>
                        </div>
                        <div id='quantity_breakdown' style='font-size: 0.9em; color: #718096; margin-top: 5px;'></div>
                      </div><br>";

                // Add discount code section with real-time validation
                echo "
                <div class='discount-section' style='margin: 20px 0; padding: 15px; background: rgba(102, 126, 234, 0.05); border-radius: 10px;'>
                    <label for='discount_code' class='lead' style='display: block; margin-bottom: 8px;'>Discount Code:</label>
                    <div style='display: flex; gap: 10px; align-items: center; flex-wrap: wrap;'>
                        <input type='text' id='discount_code' name='discount_code' class='form-control'
                               placeholder='Enter discount code' style='flex: 1; min-width: 200px;'>
                        <button type='button' id='apply_discount_btn' class='btn btn-primary btn-sm'
                                onclick='applyDiscount()' style='white-space: nowrap;'>
                            <i class='fas fa-check'></i> Apply
                        </button>
                    </div>
                    <div id='discount_message' style='margin-top: 10px; min-height: 20px;'></div>
                    <div id='price_breakdown' style='margin-top: 10px; display: none;'>
                        <div style='background: white; padding: 10px; border-radius: 8px; border-left: 4px solid #48bb78;'>
                            <div style='display: flex; justify-content: space-between; margin-bottom: 5px;'>
                                <span>Original Price:</span>
                                <span id='original_price'>₪$discount</span>
                            </div>
                            <div style='display: flex; justify-content: space-between; margin-bottom: 5px; color: #48bb78;'>
                                <span id='discount_label'>Discount:</span>
                                <span id='discount_amount'>-₪0.00</span>
                            </div>
                            <hr style='margin: 8px 0;'>
                            <div style='display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1em;'>
                                <span>Final Price:</span>
                                <span id='final_price' style='color: #48bb78;'>₪$discount</span>
                            </div>
                        </div>
                    </div>
                </div>";

                // Hidden input to store the applied discount info
                echo "<input type='hidden' id='applied_discount_code' name='applied_discount_code' value=''>
                      <input type='hidden' id='applied_discount_value' name='applied_discount_value' value='0'>
                      <input type='hidden' id='applied_discount_type' name='applied_discount_type' value=''>
                      <input type='hidden' id='current_final_price' name='current_final_price' value='$discount'>
                      <input type='hidden' name='pid' value='{$_GET['pid']}'>";

                if ($resarr[7] > 0) {
                    echo "<input type='submit' class='btn btn-success' name='addcart' value='Add to Cart'>";
                } else {
                    echo "<br><p class='lead text-danger'>Out of Stock</p>";
                }

                if (isset($_POST["addcart"])) {
                    if (isset($_SESSION["pname"])) {
                        $username = $_SESSION["userprimid"];
                        $qty = $_POST["cart"];
                        $productid = $_POST["pid"];

                        // Validate quantity selection
                        if (empty($qty) || !is_numeric($qty) || $qty <= 0) {
                            echo "<br><div style='padding: 10px 15px; background: rgba(237, 137, 54, 0.1); color: #ed8936; border-radius: 8px; border-left: 4px solid #ed8936; font-weight: 500; margin-top: 10px;'>
                                    <i class='fas fa-exclamation-triangle'></i> Please select a quantity before adding to cart.
                                  </div>";
                        } else {
                            // Convert quantity to integer
                            $qty = intval($qty);

                            // Use the pre-calculated final price from the form
                            $final_price = floatval($_POST['current_final_price']);
                            $applied_code = trim($_POST['applied_discount_code']);

                            // Get selected color information
                            $selected_color_id = isset($_POST['selected_color_id']) ? intval($_POST['selected_color_id']) : null;
                            $selected_color_name = isset($_POST['selected_color_name']) ? trim($_POST['selected_color_name']) : null;
                            $selected_color_code = isset($_POST['selected_color_code']) ? trim($_POST['selected_color_code']) : null;

                            // If a discount code was applied, update its usage count
                            if (!empty($applied_code)) {
                            require_once("vars.php");
                            $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
                            $today = date("Y-m-d");

                            // Use prepared statement for security
                            $stmt = mysqli_prepare($connection, "SELECT id FROM discount_codes WHERE code = ? AND active = 1 AND (expiry_date IS NULL OR expiry_date >= ?) AND (usage_limit IS NULL OR used_count < usage_limit)");
                            mysqli_stmt_bind_param($stmt, "ss", $applied_code, $today);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            if ($row = mysqli_fetch_assoc($result)) {
                                // Update usage count
                                $update_stmt = mysqli_prepare($connection, "UPDATE discount_codes SET used_count = used_count + 1 WHERE id = ?");
                                mysqli_stmt_bind_param($update_stmt, "i", $row['id']);
                                mysqli_stmt_execute($update_stmt);
                            }
                            mysqli_close($connection);
                        }

                        $totalcost = $final_price * $qty;

                        require_once("vars.php");
                        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);

                        // Check if color columns exist in cart table
                        $check_columns = "SHOW COLUMNS FROM cart LIKE 'selected_color_id'";
                        $column_result = mysqli_query($connection, $check_columns);
                        $has_color_columns = mysqli_num_rows($column_result) > 0;

                        if ($has_color_columns) {
                            // Insert into cart with color information - FIXED COLUMN NAMES
                            $q = "INSERT INTO cart(ProductID, ProdPic, ProdName, Rate, Qty, TotalCost, UserName, selected_color_id, selected_color_name, selected_color_code)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = mysqli_prepare($connection, $q);
                            mysqli_stmt_bind_param($stmt, "issdidsiss",
                                $productid, $resarr[8], $resarr[3], $final_price, $qty, $totalcost, $username,
                                $selected_color_id, $selected_color_name, $selected_color_code);
                        } else {
                            // Fallback: Insert without color information (for backward compatibility)
                            $q = "INSERT INTO cart(ProductID, ProdPic, ProdName, Rate, Qty, TotalCost, UserName)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt = mysqli_prepare($connection, $q);
                            mysqli_stmt_bind_param($stmt, "issdids",
                                $productid, $resarr[8], $resarr[3], $final_price, $qty, $totalcost, $username);
                        }

                        $res = mysqli_stmt_execute($stmt);
                        $rowcount = mysqli_stmt_affected_rows($stmt);
                        mysqli_stmt_close($stmt);
                        mysqli_close($connection);

                        if ($rowcount == 1) {
                            echo "<br><span class='lead text-success'>Item Added To Cart Successfully</span>";
                        } else {
                            echo "<br><span class='lead text-danger'>There was an error occurred. Please Try Again.</span>";
                        }
                        } // Close the quantity validation else block
                    } else {
                        header("location:login.php");
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

</form>

<script>
// Global variables for discount functionality
const originalPrice = <?php echo $discount; ?>;
let currentDiscountCode = '';
let currentDiscountValue = 0;
let currentDiscountType = '';

// Global variables for color variants
const productImages = <?php echo json_encode($product_images, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const colorVariants = <?php echo json_encode($color_variants, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
let selectedColorId = <?php echo $default_color ? $default_color['id'] : 'null'; ?>;

// Debug: Log the data to console
console.log('Product Images:', productImages);
console.log('Color Variants:', colorVariants);
console.log('Selected Color ID:', selectedColorId);

function applyDiscount() {
    const discountCode = document.getElementById('discount_code').value.trim().toUpperCase();
    const messageDiv = document.getElementById('discount_message');
    const applyBtn = document.getElementById('apply_discount_btn');

    if (!discountCode) {
        showMessage('Please enter a discount code.', 'warning');
        return;
    }

    // Show loading state
    applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    applyBtn.disabled = true;
    messageDiv.innerHTML = '<div style="color: #718096;"><i class="fas fa-spinner fa-spin"></i> Validating discount code...</div>';

    // Make AJAX request to validate discount code
    fetch('validate_discount.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `code=${encodeURIComponent(discountCode)}&price=${originalPrice}`
    })
    .then(response => response.json())
    .then(data => {
        applyBtn.innerHTML = '<i class="fas fa-check"></i> Apply';
        applyBtn.disabled = false;

        if (data.success) {
            // Discount code is valid
            currentDiscountCode = discountCode;
            currentDiscountValue = parseFloat(data.discount_value);
            currentDiscountType = data.discount_type;

            const finalPrice = parseFloat(data.discounted_price);

            // Update hidden fields
            document.getElementById('applied_discount_code').value = discountCode;
            document.getElementById('applied_discount_value').value = currentDiscountValue;
            document.getElementById('applied_discount_type').value = currentDiscountType;
            document.getElementById('current_final_price').value = finalPrice;

            // Show success message
            showMessage(`✅ Discount code "${discountCode}" applied successfully!`, 'success');

            // Show price breakdown
            updatePriceBreakdown(finalPrice, data.discount_amount, data.discount_type);

            // Change button to "Remove" functionality
            applyBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
            applyBtn.onclick = removeDiscount;

        } else {
            // Discount code is invalid
            showMessage(`❌ ${data.message}`, 'error');
            resetDiscount();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        applyBtn.innerHTML = '<i class="fas fa-check"></i> Apply';
        applyBtn.disabled = false;
        showMessage('❌ Error validating discount code. Please try again.', 'error');
    });
}

function removeDiscount() {
    // Reset all values
    currentDiscountCode = '';
    currentDiscountValue = 0;
    currentDiscountType = '';

    // Reset hidden fields
    document.getElementById('applied_discount_code').value = '';
    document.getElementById('applied_discount_value').value = '0';
    document.getElementById('applied_discount_type').value = '';
    document.getElementById('current_final_price').value = originalPrice;

    // Reset input field
    document.getElementById('discount_code').value = '';

    // Hide price breakdown
    document.getElementById('price_breakdown').style.display = 'none';

    // Clear message
    document.getElementById('discount_message').innerHTML = '';

    // Reset button
    const applyBtn = document.getElementById('apply_discount_btn');
    applyBtn.innerHTML = '<i class="fas fa-check"></i> Apply';
    applyBtn.onclick = applyDiscount;

    showMessage('Discount code removed.', 'info');

    // Update total price display
    updateTotalPrice();
}

function resetDiscount() {
    // Reset hidden fields but keep the input field value
    document.getElementById('applied_discount_code').value = '';
    document.getElementById('applied_discount_value').value = '0';
    document.getElementById('applied_discount_type').value = '';
    document.getElementById('current_final_price').value = originalPrice;

    // Hide price breakdown
    document.getElementById('price_breakdown').style.display = 'none';

    // Update total price display
    updateTotalPrice();
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('discount_message');
    let bgColor, textColor, borderColor;

    switch(type) {
        case 'success':
            bgColor = 'rgba(72, 187, 120, 0.1)';
            textColor = '#48bb78';
            borderColor = '#48bb78';
            break;
        case 'error':
            bgColor = 'rgba(245, 101, 101, 0.1)';
            textColor = '#f56565';
            borderColor = '#f56565';
            break;
        case 'warning':
            bgColor = 'rgba(237, 137, 54, 0.1)';
            textColor = '#ed8936';
            borderColor = '#ed8936';
            break;
        case 'info':
            bgColor = 'rgba(102, 126, 234, 0.1)';
            textColor = '#667eea';
            borderColor = '#667eea';
            break;
        default:
            bgColor = 'rgba(102, 126, 234, 0.1)';
            textColor = '#667eea';
            borderColor = '#667eea';
    }

    messageDiv.innerHTML = `
        <div style="
            padding: 10px 15px;
            background: ${bgColor};
            color: ${textColor};
            border-radius: 8px;
            border-left: 4px solid ${borderColor};
            font-weight: 500;
        ">
            ${message}
        </div>
    `;
}

// Allow Enter key to apply discount
document.getElementById('discount_code').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        applyDiscount();
    }
});

// Auto-format discount code input
document.getElementById('discount_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Function to update total price based on quantity and discount
function updateTotalPrice() {
    const quantitySelect = document.getElementById('quantity_select');
    const totalPriceDisplay = document.getElementById('total_price_display');
    const totalPriceAmount = document.getElementById('total_price_amount');
    const quantityBreakdown = document.getElementById('quantity_breakdown');

    const selectedQuantity = parseInt(quantitySelect.value);

    if (!selectedQuantity || selectedQuantity <= 0) {
        // Hide total price display if no quantity selected
        totalPriceDisplay.style.display = 'none';
        return;
    }

    // Get current price (either original or discounted)
    const currentPrice = parseFloat(document.getElementById('current_final_price').value) || originalPrice;
    const totalPrice = currentPrice * selectedQuantity;

    // Show total price display
    totalPriceDisplay.style.display = 'block';
    totalPriceAmount.textContent = `₪${totalPrice.toFixed(2)}`;

    // Create breakdown text
    let breakdownText = `${selectedQuantity} × ₪${currentPrice.toFixed(2)}`;

    // Add discount info if applicable
    const appliedCode = document.getElementById('applied_discount_code').value;
    if (appliedCode) {
        const originalTotal = originalPrice * selectedQuantity;
        const savings = originalTotal - totalPrice;
        breakdownText += ` (Save ₪${savings.toFixed(2)} with code "${appliedCode}")`;
    }

    quantityBreakdown.textContent = breakdownText;
}

// Update total price when discount is applied/removed
function updatePriceBreakdown(finalPrice, discountAmount, discountType) {
    document.getElementById('original_price').textContent = `₪${originalPrice.toFixed(2)}`;
    document.getElementById('final_price').textContent = `₪${finalPrice.toFixed(2)}`;
    document.getElementById('discount_amount').textContent = `-₪${discountAmount}`;

    const discountLabel = discountType === 'percent' ?
        `Discount (${currentDiscountValue}%):` :
        `Discount (₪${currentDiscountValue}):`;
    document.getElementById('discount_label').textContent = discountLabel;

    document.getElementById('price_breakdown').style.display = 'block';

    // Update total price display if quantity is selected
    updateTotalPrice();
}

// Color variant functions
function selectColor(colorElement) {
    // Remove selected class from all color options
    document.querySelectorAll('.color-option').forEach(option => {
        option.classList.remove('selected');
        option.style.border = '2px solid #ddd';
    });

    // Add selected class to clicked option
    colorElement.classList.add('selected');
    colorElement.style.border = '3px solid #007bff';

    // Get color information
    const colorId = colorElement.dataset.colorId;
    const colorName = colorElement.dataset.colorName;
    const colorCode = colorElement.dataset.colorCode;

    // Update selected color variables
    selectedColorId = colorId;

    // Update hidden fields
    document.getElementById('selected_color_id').value = colorId;
    document.getElementById('selected_color_name').value = colorName;
    document.getElementById('selected_color_code').value = colorCode;

    // Update selected color info display
    document.getElementById('selected-color-info').innerHTML =
        `Selected: <span style='font-weight: 600; color: #2d3748;'>${colorName}</span>`;

    // Switch images for this color
    switchColorImages(colorId);
}

function switchColorImages(colorId) {
    console.log('=== SWITCHING COLOR IMAGES ===');
    console.log('Target color ID:', colorId);
    console.log('All available images:', productImages);

    const mainImage = document.getElementById('main-product-image');
    const thumbnailContainer = document.getElementById('image-thumbnails');

    if (!mainImage) {
        console.error('Main image element not found!');
        return;
    }

    // Convert colorId to string for comparison (in case it's passed as number)
    const colorIdStr = String(colorId);

    // Get images for this color - try both string and number keys
    let colorImages = productImages[colorId] || productImages[colorIdStr] || [];

    console.log('Images found for color', colorId, ':', colorImages);

    if (colorImages.length > 0) {
        // Update main image
        const newImageSrc = `uploads/${colorImages[0].image_path}`;
        console.log('Changing main image from:', mainImage.src);
        console.log('Changing main image to:', newImageSrc);

        mainImage.src = newImageSrc;

        // Update thumbnail gallery
        if (thumbnailContainer) {
            thumbnailContainer.innerHTML = '';

            if (colorImages.length > 1) {
                console.log('Creating', colorImages.length, 'thumbnails');
                colorImages.forEach((img, index) => {
                    const thumbnail = document.createElement('img');
                    thumbnail.src = `uploads/${img.image_path}`;
                    thumbnail.className = `thumbnail-image ${index === 0 ? 'active' : ''}`;
                    thumbnail.style.cssText = `
                        width: 60px;
                        height: 60px;
                        object-fit: cover;
                        border: 2px solid ${index === 0 ? '#007bff' : '#ddd'};
                        cursor: pointer;
                        border-radius: 4px;
                        margin-right: 5px;
                    `;
                    thumbnail.onclick = () => changeMainImage(thumbnail, img.image_path);
                    thumbnailContainer.appendChild(thumbnail);
                });
            } else {
                console.log('Only one image for this color, no thumbnails needed');
            }
        }

        console.log('✅ Image switch completed successfully');
    } else {
        console.warn('⚠️ No images found for color', colorId);
        console.log('Available color IDs in productImages:', Object.keys(productImages));

        // Try to find any images for this product as fallback
        const allImages = Object.values(productImages).flat();
        if (allImages.length > 0) {
            console.log('Using fallback image:', allImages[0].image_path);
            mainImage.src = `uploads/${allImages[0].image_path}`;
        }
    }
}

function changeMainImage(thumbnailElement, imagePath) {
    // Update main image
    document.getElementById('main-product-image').src = `uploads/${imagePath}`;

    // Update thumbnail borders
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.style.border = '2px solid #ddd';
        thumb.classList.remove('active');
    });

    // Highlight selected thumbnail
    thumbnailElement.style.border = '2px solid #007bff';
    thumbnailElement.classList.add('active');
}

// Track this product view for recently viewed
document.addEventListener('DOMContentLoaded', function() {
    // Add current product to recently viewed
    addToRecentlyViewed(
        '<?php echo $pid; ?>',
        '<?php echo addslashes($resarr[3]); ?>',
        '<?php echo $resarr[8]; ?>',
        '<?php echo $resarr[4]; ?>',
        '<?php echo $resarr[5]; ?>'
    );
});
</script>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>

<?php require_once("footer.php")?>
</body>
</html>

<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Recently Viewed - Computer Garage</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php require_once("extfiles.php"); ?>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding-top: 100px;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-product {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .test-product:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .test-product img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .clear-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px 0;
        }
        .clear-btn:hover {
            background: #c82333;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            color: #343a40;
        }
        .underlined-title {
            display: inline-block;
            padding-bottom: 10px;
            border-bottom: 3px solid #007bff;
        }
    </style>
</head>
<body>

<?php require_once("header.php"); ?>

<div class="test-container">
    <h1 class="section-title">
        <span class="underlined-title">Test Recently Viewed Feature</span>
    </h1>

    <p style="text-align: center; margin-bottom: 30px;">
        Click on any product below to add it to your recently viewed list.
        The recently viewed section will appear at the bottom of the page.
    </p>

    <div class="row">
        <!-- Test Product 1 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(1, 'MacBook Air M2', 'laptop.png', 4999, 10)">
                <img src="assets/laptop.png" alt="MacBook Air M2">
                <h4>MacBook Air M2</h4>
                <p>₪4999 <small>(10% off)</small></p>
            </div>
        </div>

        <!-- Test Product 2 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(2, 'Gaming Desktop PC', 'pc.png', 3500, 15)">
                <img src="assets/pc.png" alt="Gaming Desktop PC">
                <h4>Gaming Desktop PC</h4>
                <p>₪3500 <small>(15% off)</small></p>
            </div>
        </div>

        <!-- Test Product 3 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(3, 'Mechanical Keyboard', 'keyboard1.png', 299, 5)">
                <img src="assets/keyboard1.png" alt="Mechanical Keyboard">
                <h4>Mechanical Keyboard</h4>
                <p>₪299 <small>(5% off)</small></p>
            </div>
        </div>

        <!-- Test Product 4 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(4, 'Wireless Mouse', 'mouse.png', 149, 20)">
                <img src="assets/mouse.png" alt="Wireless Mouse">
                <h4>Wireless Mouse</h4>
                <p>₪149 <small>(20% off)</small></p>
            </div>
        </div>

        <!-- Test Product 5 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(5, 'Gaming Headphones', 'headphones.png', 399, 12)">
                <img src="assets/headphones.png" alt="Gaming Headphones">
                <h4>Gaming Headphones</h4>
                <p>₪399 <small>(12% off)</small></p>
            </div>
        </div>

        <!-- Test Product 6 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(6, 'Bluetooth Speaker', 'speaker.png', 199, 8)">
                <img src="assets/speaker.png" alt="Bluetooth Speaker">
                <h4>Bluetooth Speaker</h4>
                <p>₪199 <small>(8% off)</small></p>
            </div>
        </div>

        <!-- Test Product 7 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(7, 'SSD 1TB', 'ssd.png', 449, 25)">
                <img src="assets/ssd.png" alt="SSD 1TB">
                <h4>SSD 1TB</h4>
                <p>₪449 <small>(25% off)</small></p>
            </div>
        </div>

        <!-- Test Product 8 -->
        <div class="col-md-3">
            <div class="test-product" onclick="addTestProduct(8, 'USB-C Cable', 'Cable&Adapter.jpg', 49, 30)">
                <img src="assets/Cable&Adapter.jpg" alt="USB-C Cable">
                <h4>USB-C Cable</h4>
                <p>₪49 <small>(30% off)</small></p>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin: 40px 0;">
        <button class="clear-btn" onclick="clearRecentlyViewed()">
            Clear Recently Viewed
        </button>
        <p><small>Use this button to clear all recently viewed products for testing.</small></p>
    </div>
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

<?php require_once("footer.php"); ?>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>

<script>
function addTestProduct(id, name, image, price, discount) {
    // Add product to recently viewed
    addToRecentlyViewed(id, name, image, price, discount);

    // Show a brief confirmation
    const originalText = event.target.closest('.test-product').innerHTML;
    event.target.closest('.test-product').innerHTML = '<div style="padding: 50px;"><strong>Added to Recently Viewed!</strong></div>';

    setTimeout(() => {
        event.target.closest('.test-product').innerHTML = originalText;
    }, 1000);
}

function clearRecentlyViewed() {
    if (confirm('Are you sure you want to clear all recently viewed products?')) {
        recentlyViewedManager.clearRecentlyViewed();
        alert('Recently viewed products cleared!');
    }
}

// Show current localStorage data in console for debugging
console.log('Current localStorage data:', localStorage.getItem('computer_garage_recently_viewed'));
</script>

</body>
</html>

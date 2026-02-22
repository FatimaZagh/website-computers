<?php
session_start();
ob_start(); // Start output buffering at the very beginning

// Redirect if not logged in
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit; // Always exit after a header redirect
}

// Database credentials and other necessary files
require_once("vars.php");
require_once("extfiles.php"); // For CSS/JS like Bootstrap
require_once("header.php");   // Your site header

// --- Handle Form Submission ---
$errors = [];
$success_message = "";

if (isset($_POST["place_order_btn"])) {
    // Sanitize and validate inputs
    $fullname = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
    $address = isset($_POST["address"]) ? trim($_POST["address"]) : ''; // Main address field
    $city = isset($_POST["city"]) ? trim($_POST["city"]) : '';
    $pincode = isset($_POST["pincode"]) ? trim($_POST["pincode"]) : '';
    $state = isset($_POST["state"]) ? trim($_POST["state"]) : '';

    if (empty($fullname)) $errors[] = "Full Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required.";
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Valid 10-digit Phone Number is required."; // Basic 10-digit validation
    if (empty($address)) $errors[] = "Street Address is required.";
    if (empty($city)) $errors[] = "City is required.";
    if (empty($pincode) || !preg_match('/^[0-9]{6}$/', $pincode)) $errors[] = "Valid 6-digit Pincode is required."; // Basic 6-digit validation
    if (empty($state)) $errors[] = "State is required.";


    if (empty($errors)) {
        $full_shipping_address = $address . "\n" . $city . ", " . $state . " - " . $pincode;
        $pmethod = "Cash on Delivery";
        $username_id = $_SESSION["userprimid"]; // Assuming this is the user's ID
        $billamount = $_SESSION["billamount"];
        date_default_timezone_set('Asia/Kolkata');
        $orderdate = date("Y-m-d H:i:s"); // Standard SQL datetime format
        $status = "Order Placed";

        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

        // Escape all string variables before inserting into DB
        $fullname_db = mysqli_real_escape_string($connection, $fullname);
        $email_db = mysqli_real_escape_string($connection, $email);
        $phone_db = mysqli_real_escape_string($connection, $phone);
        $full_shipping_address_db = mysqli_real_escape_string($connection, $full_shipping_address);
        // $username_id is an ID, usually an integer, no need to escape if truly integer. If it's a string username, escape it.
        // $billamount is numeric.
        // $orderdate is generated, generally safe.
        // $pmethod and $status are hardcoded strings, safe.

        // **IMPORTANT: Ensure your `ordertable` has columns: FullName, Email, PhoneNumber**
        $q = "INSERT INTO ordertable (FullName, Email, PhoneNumber, ShippingAddress, PaymentMethod, Username, OrderDate, BillAmount, Status) 
              VALUES ('$fullname_db', '$email_db', '$phone_db', '$full_shipping_address_db', '$pmethod', '$username_id', '$orderdate', '$billamount', '$status')";

        if (mysqli_query($connection, $q)) {
            $rescount = mysqli_affected_rows($connection);
            if ($rescount == 1) {
                $order_id = mysqli_insert_id($connection); // Get the ID of the order just placed
                $_SESSION['last_order_id'] = $order_id; // Store it for the summary page
                mysqli_close($connection);
                header("location:ordersummary.php"); // Redirect to order summary
                exit;
            } else {
                $errors[] = "Order could not be placed due to a database issue (0 rows affected).";
            }
        } else {
            $errors[] = "Error processing order: " . mysqli_error($connection);
        }
        mysqli_close($connection);
    }
}
?>
    <!DOCTYPE html> <!-- Added Doctype -->
    <html> <!-- Removed xmlns -->
    <head>
        <title>Checkout - Place Your Order</title>
        <?php // extfiles.php is already included above
        ?>
        <style>
            body {
                background-color: #f8f9fa; /* Light gray background */
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .checkout-container {
                background-color: white;
                padding: 30px;
                margin-top: 50px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            .checkout-container h1 {
                color: #333;
                margin-bottom: 30px;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
            }
            .form-control:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            .order-summary-box {
                background-color: #e9ecef;
                padding: 20px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .order-summary-box h4 {
                margin-bottom: 15px;
                color: #495057;
            }
            .order-summary-box p {
                font-size: 1.1em;
                margin-bottom: 8px;
            }
            .btn-place-order {
                background-color: #28a745; /* Green */
                border-color: #28a745;
                padding: 10px 20px;
                font-size: 1.1em;
                width: 100%;
            }
            .btn-place-order:hover {
                background-color: #218838;
                border-color: #1e7e34;
            }
            .continue-shopping-link {
                display: block;
                text-align: center;
                margin-top: 20px;
                color: #007bff;
            }
            .text-muted-custom {
                font-size: 0.9em;
                color: #6c757d;
            }
            .form-label { /* Bootstrap 5 style label */
                margin-bottom: .5rem;
                font-weight: 500;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="checkout-container">
                    <h1 class="text-center display-5">Secure Checkout</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form name="order_form" method="post" action="checkout.php">
                        <div class="row">
                            <div class="col-md-7">
                                <h4>Contact Information</h4>
                                <hr>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="10-digit mobile number" pattern="[0-9]{10}" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                                    <small class="text-muted-custom">For delivery updates.</small>
                                </div>

                                <h4 class="mt-4">Shipping Address</h4>
                                <hr>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address / House No. <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="E.g., 123 Main St, Apartment 4B" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="Your City" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="6-digit Pincode" pattern="[0-9]{6}" value="<?php echo isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" placeholder="Your State" value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="order-summary-box">
                                    <h4>Order Summary</h4>
                                    <p><strong>Total Amount:</strong>
                                        <span class="float-end">
                                            ₪‎ <?php echo isset($_SESSION["billamount"]) ? number_format($_SESSION["billamount"], 2) : '0.00'; ?>
                                        </span>
                                    </p>
                                    <p><strong>Payment Method:</strong> <span class="float-end">Cash On Delivery</span></p>
                                    <small class="text-muted-custom">You will pay when your order arrives.</small>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-place-order" name="place_order_btn">Place Order Securely</button>
                                </div>
                                <a href="showcat.php" class="continue-shopping-link">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>

    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); // Flush the output buffer ?>
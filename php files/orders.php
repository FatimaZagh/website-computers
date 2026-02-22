<?php
session_start();
ob_start();

// Admin security checks
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit;
}
if ($_SESSION["usertype"] !== "admin") {
    header("location:login.php");
    exit;
}

require_once("vars.php"); // Assuming this has dbhost, dbuname, dbpass, dbname
// extfiles.php should be included in the <head> for CSS
// adminnavbar.php will be included in the <body>

// --- Handle Status Update ---
if (isset($_POST['update_status_btn'])) {
    if (isset($_POST['order_id']) && isset($_POST['new_status'])) {
        $order_id_to_update = (int)$_POST['order_id'];
        $new_status_val = $_POST['new_status'];

        $allowed_statuses = ['Order Placed', 'Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled', 'Returned', 'Refunded'];

        if (in_array($new_status_val, $allowed_statuses)) {
            $conn_update = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Connection error for update: " . mysqli_connect_error());
            $stmt = mysqli_prepare($conn_update, "UPDATE ordertable SET Status = ? WHERE OrderID = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_status_val, $order_id_to_update);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['admin_message'] = "Order ID: $order_id_to_update status updated to '" . htmlspecialchars($new_status_val) . "'.";
            } else {
                $_SESSION['admin_error'] = "Error updating status for Order ID: $order_id_to_update. " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn_update);
        } else {
            $_SESSION['admin_error'] = "Invalid status value provided.";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Customer Orders</title>
        <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48; /* For rgba card backgrounds */
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --table-header-bg: rgba(var(--secondary-bg-rgb), 0.7);
                --table-row-bg: rgba(var(--secondary-bg-rgb), 0.5);
                --table-row-hover-bg: rgba(var(--secondary-bg-rgb), 0.8);
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

            /* Navbar Styling (Copied from admin home for consistency) */
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
            #acc:hover { /* From your original admin home */
                color: var(--accent-color) !important;
                text-shadow: 0 0 8px var(--glow-color);
            }


            .orders-main-content { /* Renamed from admin-main-content for clarity */
                flex-grow: 1;
                padding-top: 40px;
                padding-bottom: 60px;
                position: relative;
                z-index: 1;
            }

            .page-title {
                color: #fff;
                font-weight: 600;
                margin-bottom: 30px;
                padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color);
                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block; /* To make border-bottom fit content */
            }
            .text-center .page-title { /* Center if text-center class is on parent */
                display: block;
                border-bottom: none; /* Remove border if centered fully */
                border-bottom: 2px solid var(--accent-color); /* Or keep it like this */
                width: fit-content;
                margin-left: auto;
                margin-right: auto;
            }


            .table-wrapper {
                background-color: rgba(var(--secondary-bg-rgb), 0.6); /* Glassmorphism */
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                padding: 25px;
                box-shadow: 0 12px 35px var(--shadow-color);
                border: 1px solid var(--border-color);
                overflow: hidden; /* Important for border-radius on table */
            }

            .table-admin-orders {
                width: 100%;
                margin-bottom: 0; /* Remove default bootstrap margin */
                border-collapse: separate; /* Allows for border-radius on cells/rows */
                border-spacing: 0;
                font-size: 0.9rem;
            }

            .table-admin-orders th,
            .table-admin-orders td {
                padding: 0.9rem 0.75rem; /* Increased padding */
                vertical-align: middle;
                border-top: 1px solid var(--border-color) !important; /* Use !important sparingly */
                border-bottom: none !important; /* Remove bottom borders, use top only */
                color: var(--text-color);
            }
            .table-admin-orders th:first-child,
            .table-admin-orders td:first-child { border-left: none !important; }
            .table-admin-orders th:last-child,
            .table-admin-orders td:last-child { border-right: none !important; }


            .table-admin-orders thead th {
                background-color: var(--table-header-bg);
                color: #fff; /* Brighter text for headers */
                font-weight: 600;
                text-align: left;
                border-bottom: 2px solid var(--accent-color) !important;
            }
            .table-admin-orders thead th:first-child { border-top-left-radius: calc(var(--card-border-radius) - 1px); } /* Match wrapper radius */
            .table-admin-orders thead th:last-child { border-top-right-radius: calc(var(--card-border-radius) - 1px); }


            .table-admin-orders tbody tr {
                background-color: var(--table-row-bg);
                transition: background-color var(--transition-speed) ease;
            }
            .table-admin-orders tbody tr:nth-child(odd) {
                /* background-color: rgba(var(--secondary-bg-rgb), 0.45); /* Slightly different for odd rows */
            }


            .table-admin-orders tbody tr:hover {
                background-color: var(--table-row-hover-bg);
                color: #fff;
            }
            .table-admin-orders tbody tr:hover td {
                color: #fff;
            }


            .table-admin-orders tbody tr:last-child td:first-child { border-bottom-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-admin-orders tbody tr:last-child td:last-child { border-bottom-right-radius: calc(var(--card-border-radius) - 1px); }


            .order-id-link {
                font-weight: bold;
                color: var(--accent-color);
                text-decoration: none;
                transition: color var(--transition-speed) ease, text-shadow var(--transition-speed) ease;
            }
            .order-id-link:hover {
                color: #fff;
                text-shadow: 0 0 5px var(--glow-color);
            }

            .status-select.form-select {
                background-color: rgba(var(--secondary-bg-rgb), 0.8);
                color: var(--text-color);
                border: 1px solid var(--border-color);
                min-width: 160px; /* Ensure dropdown is wide enough */
                font-size: 0.85rem;
            }
            .status-select.form-select:focus {
                border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color);
            }

            .update-btn-sm.btn-primary {
                background-color: var(--accent-color);
                border-color: var(--accent-color);
                color: #fff;
                font-size: 0.85rem;
                padding: 0.3rem 0.75rem;
                transition: background-color var(--transition-speed) ease, border-color var(--transition-speed) ease;
            }
            .update-btn-sm.btn-primary:hover {
                background-color: darken(var(--accent-color), 10%);
                border-color: darken(var(--accent-color), 10%);
            }
            /* For the darken function, you'd typically use SASS/SCSS.
               In pure CSS, you might need to define a hover color variable.
               For now, let's use a slightly different color directly or just rely on default Bootstrap hover.
               Or a subtle opacity change or brightness filter. */
            .update-btn-sm.btn-primary:hover {
                background-color: #0095e0; /* Slightly darker blue */
                border-color: #0088cc;
            }


            .alert { /* Styling alerts to fit the theme */
                background-color: rgba(var(--secondary-bg-rgb), 0.7);
                border: 1px solid var(--border-color);
                color: var(--text-color);
                border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);
            }
            .alert-success {
                border-left: 5px solid #28a745; /* Green accent for success */
                color: #d4edda; /* Lighter green text */
                background-color: rgba(40,167,69,0.3);
            }
            .alert-danger {
                border-left: 5px solid #dc3545; /* Red accent for danger */
                color: #f8d7da; /* Lighter red text */
                background-color: rgba(220,53,69,0.3);
            }
            .alert-info {
                border-left: 5px solid #17a2b8; /* Info blue accent */
                color: #d1ecf1;
                background-color: rgba(23,162,184,0.3);
            }

            /* Responsive table handling */
            .table-responsive-custom {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Footer Styling (Copied from admin home for consistency) */
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

            @media (max-width: 768px) {
                body::before, body::after {
                    width: 120vmax;
                    height: 120vmax;
                }
                .page-title {
                    font-size: 1.8rem;
                }
                .table-wrapper {
                    padding: 15px;
                }
                .table-admin-orders th, .table-admin-orders td {
                    padding: 0.6rem 0.5rem;
                    font-size: 0.8rem;
                }
                .status-select.form-select {
                    min-width: 120px;
                }
            }

        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container-fluid orders-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Manage Customer Orders</h1>
        </div>
        <br>

        <?php
        if (isset($_SESSION['admin_message'])) {
            echo "<div class='alert alert-success mx-auto col-md-8 col-lg-6'>" . htmlspecialchars($_SESSION['admin_message']) . "</div>";
            unset($_SESSION['admin_message']);
        }
        if (isset($_SESSION['admin_error'])) {
            echo "<div class='alert alert-danger mx-auto col-md-8 col-lg-6'>" . htmlspecialchars($_SESSION['admin_error']) . "</div>";
            unset($_SESSION['admin_error']);
        }
        ?>

        <div class="table-wrapper mt-4">
            <div class="table-responsive-custom">
                <?php
                $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());
                $q = "SELECT OrderID, FullName, Email, PhoneNumber, Username as UserIdentifier, ShippingAddress, PaymentMethod, OrderDate, BillAmount, Status
                  FROM ordertable
                  ORDER BY OrderDate DESC";
                $res = mysqli_query($connection, $q) or die("Error in query: " . mysqli_error($connection));
                $rescount = mysqli_num_rows($res);

                if ($rescount == 0) {
                    echo "<div class='alert alert-info text-center'>No orders found in the system.</div>";
                } else {
                    echo "<p class='mb-3' style='color: var(--text-color-darker);'>Total orders found: $rescount</p>";
                    echo "<table class='table table-admin-orders'>"; // Removed Bootstrap table classes like table-bordered, table-hover as we are custom styling
                    echo "
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Login</th>
                        <th>Shipping Address</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>";

                    $allowed_statuses = ['Order Placed', 'Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled', 'Returned', 'Refunded'];

                    while ($order = mysqli_fetch_assoc($res)) {
                        $formatted_date = date("d M Y, h:i A", strtotime($order['OrderDate']));
                        $status_class = 'status-' . strtolower(str_replace(' ', '-', $order['Status'])); // For potential status-specific styling

                        echo "<tr>";
                        echo "<td><a href='adminorderdetails.php?oid=" . htmlspecialchars($order['OrderID']) . "' class='order-id-link'>" . htmlspecialchars($order['OrderID']) . "</a></td>";
                        echo "<td>" . htmlspecialchars($order['FullName'] ?: 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($order['Email'] ?: 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($order['PhoneNumber'] ?: 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($order['UserIdentifier']) . "</td>";
                        echo "<td>" . nl2br(htmlspecialchars($order['ShippingAddress'])) . "</td>";
                        echo "<td>" . htmlspecialchars($order['PaymentMethod']) . "</td>";
                        echo "<td>₪ " . number_format($order['BillAmount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
                        echo "<td class='fw-bold " . htmlspecialchars($status_class) . "'>" . htmlspecialchars($order['Status']) . "</td>";
                        echo "<td>
                            <form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='d-flex align-items-center'>
                                <input type='hidden' name='order_id' value='" . htmlspecialchars($order['OrderID']) . "'>
                                <select name='new_status' class='form-select form-select-sm status-select me-2'>";
                        foreach ($allowed_statuses as $status_option) {
                            $selected = ($status_option == $order['Status']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($status_option) . "' $selected>" . htmlspecialchars($status_option) . "</option>";
                        }
                        echo "      </select>
                                <button type='submit' name='update_status_btn' class='btn btn-primary btn-sm update-btn-sm flex-shrink-0'>Update</button>
                            </form>
                          </td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
                mysqli_close($connection);
                ?>
            </div> <!-- .table-responsive-custom -->
        </div> <!-- .table-wrapper -->

        <?php if ($rescount > 0): ?>
            <p class="text-center mt-4" style="color: var(--text-color-darker);"><?php echo "$rescount order(s) found"; ?></p>
        <?php endif; ?>
    </div> <!-- .orders-main-content -->

    <br><br>
    <?php include_once("footer.php"); ?>

    </body>
    </html>
<?php ob_end_flush(); ?>
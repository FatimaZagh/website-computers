<?php
session_start();
ob_start(); // Start output buffering at the very beginning

// Redirect if not logged in
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit; // Always exit after a header redirect
}

// Necessary files
require_once("vars.php");
require_once("extfiles.php"); // For CSS/JS like Bootstrap
require_once("header.php");   // Your site header
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>My Orders</title>
        <?php // extfiles.php is already included above ?>
        <style>
            body {
                background-color: #f8f9fa;
            }
            .orders-container {
                margin-top: 40px; /* Reduced top margin */
            }
            .orders-container h1 {
                margin-bottom: 30px;
                color: #333;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
            }
            .table-orders { /* Custom class for the orders table */
                background-color: white;
                box-shadow: 0 2px 10px rgba(0,0,0,0.075);
            }
            .table-orders th {
                background-color: #e9ecef;
                color: #495057;
                font-weight: 600; /* Bootstrap's default is 700, slightly less bold */
            }
            .table-orders td, .table-orders th {
                vertical-align: middle;
                padding: 0.9rem; /* Consistent padding */
            }
            .order-id-link {
                font-weight: bold;
                color: #007bff;
            }
            .order-id-link:hover {
                text-decoration: underline;
            }
            .status-placed { color: #17a2b8; font-weight: bold; } /* Info blue */
            .status-shipped { color: #ffc107; font-weight: bold; } /* Warning yellow */
            .status-delivered { color: #28a745; font-weight: bold; } /* Success green */
            .status-cancelled { color: #dc3545; font-weight: bold; } /* Danger red */

            /* Responsive table handling */
            .table-responsive-custom {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            /* Your existing styles for cartimg - may not be needed on this page */
            .cartimg{
                width:100px;
                height:100px;
                object-fit:contain;
            }
            @media (max-width:940px){
                .cartimg{
                    width:50px;
                    height:50px;
                }
            }
        </style>
    </head>
    <body>
    <div class="container orders-container">
        <h1 class="display-5 text-center">My Order History</h1>
        <br>

        <div class="table-responsive-custom">
            <?php
            $user_id = $_SESSION["userprimid"]; // Assuming this is the integer ID
            $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

            // Select the new columns as well
            $q = "SELECT OrderID, FullName, Email, PhoneNumber, ShippingAddress, PaymentMethod, Username, OrderDate, BillAmount, Status 
                  FROM ordertable 
                  WHERE Username = '$user_id' 
                  ORDER BY OrderDate DESC";
            // Note: If Username in ordertable is an INT, no quotes needed around $user_id. If it's a string, it's fine.

            $res = mysqli_query($connection, $q) or die("Error in query: " . mysqli_error($connection));
            $rescount = mysqli_num_rows($res); // Use mysqli_num_rows for SELECT

            if ($rescount == 0) {
                echo "<div class='alert alert-info text-center' role='alert'>You haven't placed any orders yet.</div>";
            } else {
                echo "<table class='table table-hover table-striped table-orders'>"; // Added Bootstrap classes
                echo "
                <thead class='thead-light'>
                    <tr>
                        <th>Order ID</th>
                        <th>Full Name</th>
                        <th>Shipping Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

                while ($order = mysqli_fetch_assoc($res)) {
                    // Basic status styling
                    $status_class = '';
                    switch (strtolower($order['Status'])) {
                        case 'order placed': $status_class = 'status-placed'; break;
                        case 'shipped': $status_class = 'status-shipped'; break;
                        case 'delivered': $status_class = 'status-delivered'; break;
                        case 'cancelled': $status_class = 'status-cancelled'; break;
                    }

                    // Format date for better readability
                    $formatted_date = date("d M Y, h:i A", strtotime($order['OrderDate']));

                    echo "<tr>";
                    echo "<td><a href='userorderdetails.php?oid=" . htmlspecialchars($order['OrderID']) . "' class='order-id-link'>" . htmlspecialchars($order['OrderID']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($order['FullName']) . "</td>";
                    echo "<td>" . nl2br(htmlspecialchars($order['ShippingAddress'])) . "</td>"; // nl2br to respect newlines in address
                    echo "<td>" . htmlspecialchars($order['Email']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['PhoneNumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($order['PaymentMethod']) . "</td>";
                    echo "<td>₹ " . number_format($order['BillAmount'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
                    echo "<td class='" . $status_class . "'>" . htmlspecialchars($order['Status']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            mysqli_close($connection);
            ?>
        </div>
        <br><br>
        <div class="text-center">
            <a href='showcat.php' class='btn btn-primary btn-lg'>
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
    </div>
    <br><br><br>

    <?php include_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); // Flush the output buffer ?>
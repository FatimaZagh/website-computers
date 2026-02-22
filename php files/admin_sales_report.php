<?php
session_start();
ob_start();

// Admin security checks
if (!isset($_SESSION["pname"]) || $_SESSION["usertype"] !== "admin") {
    header("location:login.php");
    exit;
}

require_once("vars.php");
// extfiles.php and adminnavbar.php will be included in HTML

// --- Date Filtering Logic ---
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'all_time';
$start_date_input = isset($_GET['start_date']) ? $_GET['start_date'] : ''; // Store user input
$end_date_input = isset($_GET['end_date']) ? $_GET['end_date'] : '';     // Store user input

$where_clause = "";
$report_period_text = "All Time";

// Effective dates used for query, may differ from input if input is invalid
$query_start_date = '';
$query_end_date = '';


// Validate and process dates
if (!empty($start_date_input)) {
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date_input);
    if ($start_date_obj && $start_date_obj->format('Y-m-d') === $start_date_input) {
        $query_start_date = $start_date_input;
    } else {
        $start_date_input = ''; // Clear invalid input for display
    }
}
if (!empty($end_date_input)) {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date_input);
    if ($end_date_obj && $end_date_obj->format('Y-m-d') === $end_date_input) {
        $query_end_date = $end_date_input;
    } else {
        $end_date_input = ''; // Clear invalid input for display
    }
}


switch ($date_filter) {
    case 'today':
        $query_start_date = date('Y-m-d');
        $query_end_date = date('Y-m-d');
        $where_clause = "WHERE DATE(OrderDate) = CURDATE()";
        $report_period_text = "Today (" . date('d M Y') . ")";
        break;
    case 'yesterday':
        $query_start_date = date('Y-m-d', strtotime('-1 day'));
        $query_end_date = date('Y-m-d', strtotime('-1 day'));
        $where_clause = "WHERE DATE(OrderDate) = CURDATE() - INTERVAL 1 DAY";
        $report_period_text = "Yesterday (" . date('d M Y', strtotime('-1 day')) . ")";
        break;
    case 'last_7_days':
        $query_start_date = date('Y-m-d', strtotime('-6 days'));
        $query_end_date = date('Y-m-d');
        $where_clause = "WHERE OrderDate >= CURDATE() - INTERVAL 6 DAY AND OrderDate < CURDATE() + INTERVAL 1 DAY";
        $report_period_text = "Last 7 Days (" . date('d M Y', strtotime($query_start_date)) . " - " . date('d M Y', strtotime($query_end_date)) . ")";
        break;
    case 'last_30_days':
        $query_start_date = date('Y-m-d', strtotime('-29 days'));
        $query_end_date = date('Y-m-d');
        $where_clause = "WHERE OrderDate >= CURDATE() - INTERVAL 29 DAY AND OrderDate < CURDATE() + INTERVAL 1 DAY";
        $report_period_text = "Last 30 Days (" . date('d M Y', strtotime($query_start_date)) . " - " . date('d M Y', strtotime($query_end_date)) . ")";
        break;
    case 'this_month':
        $query_start_date = date('Y-m-01');
        $query_end_date = date('Y-m-t');
        $where_clause = "WHERE OrderDate >= '" . date('Y-m-01') . "' AND OrderDate < '" . date('Y-m-01', strtotime('+1 month')) . "'";
        $report_period_text = "This Month (" . date('F Y') . ")";
        break;
    case 'last_month':
        $query_start_date = date('Y-m-01', strtotime('first day of last month'));
        $query_end_date = date('Y-m-t', strtotime('last day of last month'));
        $where_clause = "WHERE OrderDate >= '" . date('Y-m-01', strtotime('first day of last month')) . "' AND OrderDate < '" . date('Y-m-01', strtotime('first day of this month')) . "'";
        $report_period_text = "Last Month (" . date('F Y', strtotime('last month')) . ")";
        break;
    case 'custom_range':
        if (!empty($query_start_date) && !empty($query_end_date)) {
            // Ensure end_date is not before start_date
            if (strtotime($query_end_date) < strtotime($query_start_date)) {
                // Swap dates or handle error - for now, let's assume valid range or fallback
                $temp_date = $query_start_date;
                $query_start_date = $query_end_date;
                $query_end_date = $temp_date;
            }
            $connection_temp = mysqli_connect(dbhost, dbuname, dbpass, dbname);
            $escaped_start_date = mysqli_real_escape_string($connection_temp, $query_start_date . " 00:00:00");
            $escaped_end_date = mysqli_real_escape_string($connection_temp, $query_end_date . " 23:59:59");
            mysqli_close($connection_temp);

            $where_clause = "WHERE OrderDate >= '$escaped_start_date' AND OrderDate <= '$escaped_end_date'";
            $report_period_text = "Custom: " . date('d M Y', strtotime($query_start_date)) . " to " . date('d M Y', strtotime($query_end_date));
        } else {
            $date_filter = 'all_time';
            $where_clause = "";
            $report_period_text = "All Time (Custom range invalid/incomplete)";
        }
        break;
    case 'all_time':
    default:
        $where_clause = "";
        $report_period_text = "All Time";
        break;
}

// --- Database Queries ---
$total_sales = 0;
$total_orders = 0;
$orders_list = [];

$connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

$q_summary = "SELECT SUM(BillAmount) as total_sales_amount, COUNT(OrderID) as total_order_count
              FROM ordertable
              $where_clause";
$res_summary = mysqli_query($connection, $q_summary) or die("Error in summary query: " . mysqli_error($connection));
if ($summary_row = mysqli_fetch_assoc($res_summary)) {
    $total_sales = $summary_row['total_sales_amount'] ?: 0;
    $total_orders = $summary_row['total_order_count'] ?: 0;
}

$q_orders = "SELECT OrderID, FullName, Username as UserIdentifier, OrderDate, BillAmount, Status
             FROM ordertable
             $where_clause
             ORDER BY OrderDate DESC";
$res_orders = mysqli_query($connection, $q_orders) or die("Error in orders query: " . mysqli_error($connection));
while ($order_row = mysqli_fetch_assoc($res_orders)) {
    $orders_list[] = $order_row;
}

mysqli_close($connection);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sales Report</title>
        <?php require_once("extfiles.php"); ?>
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
                --table-header-bg: rgba(var(--secondary-bg-rgb), 0.7);
                --table-row-bg: rgba(var(--secondary-bg-rgb), 0.5);
                --table-row-hover-bg: rgba(var(--secondary-bg-rgb), 0.8);
                --input-bg: rgba(var(--primary-bg), 0.7); /* For form inputs */
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
            #acc:hover {
                color: var(--accent-color) !important;
                text-shadow: 0 0 8px var(--glow-color);
            }

            .report-main-content {
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
                display: inline-block;
            }
            .text-center .page-title {
                display: block;
                width: fit-content;
                margin-left: auto;
                margin-right: auto;
            }

            .filter-form-wrapper {
                margin-bottom: 40px;
                padding: 25px 30px;
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                box-shadow: 0 8px 20px var(--shadow-color);
                border: 1px solid var(--border-color);
            }
            .filter-form-wrapper .form-label {
                color: var(--text-color-darker);
                margin-bottom: 0.3rem;
                font-size: 0.9rem;
            }
            .filter-form-wrapper .form-select,
            .filter-form-wrapper .form-control[type="date"] {
                background-color: var(--input-bg);
                color: var(--text-color);
                border: 1px solid var(--border-color);
                border-radius: 6px;
            }
            .filter-form-wrapper .form-select option {
                background-color: var(--primary-bg); /* Dropdown options background */
                color: var(--text-color);
            }
            .filter-form-wrapper .form-control[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(0.8); /* Make date picker icon visible on dark bg */
            }
            .filter-form-wrapper .form-select:focus,
            .filter-form-wrapper .form-control[type="date"]:focus {
                background-color: rgba(var(--primary-bg), 0.9);
                border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color);
                color: var(--text-color);
            }
            .filter-form-wrapper .btn-primary {
                background-color: var(--accent-color);
                border-color: var(--accent-color);
                color: #fff;
                font-weight: 500;
                transition: background-color var(--transition-speed) ease, border-color var(--transition-speed) ease;
            }
            .filter-form-wrapper .btn-primary:hover {
                background-color: #0095e0;
                border-color: #0088cc;
            }

            .report-period-info {
                color: var(--text-color);
                font-size: 1.2rem;
                font-weight: 500;
                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }
            .report-period-info strong {
                color: var(--accent-color);
            }


            .summary-card {
                background-color: rgba(var(--secondary-bg-rgb), 0.5);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                padding: 25px;
                border-radius: var(--card-border-radius);
                box-shadow: 0 10px 30px var(--shadow-color);
                border: 1px solid var(--border-color);
                margin-bottom: 25px;
                text-align: center;
                transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            }
            .summary-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px var(--shadow-color), 0 0 15px var(--glow-color);
            }
            .summary-card h4 {
                color: var(--accent-color);
                margin-bottom: 10px;
                font-size: 1.1rem;
                font-weight: 500;
            }
            .summary-card p {
                font-size: 2.2em;
                font-weight: 600;
                margin-bottom: 0;
                color: #fff;
            }
            .summary-card i { /* Optional icon for summary cards */
                font-size: 1.5em;
                margin-right: 10px;
                opacity: 0.7;
            }


            .table-wrapper {
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                padding: 25px;
                box-shadow: 0 12px 35px var(--shadow-color);
                border: 1px solid var(--border-color);
                overflow: hidden;
            }
            .table-sales-report {
                width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0; font-size: 0.9rem;
            }
            .table-sales-report th, .table-sales-report td {
                padding: 0.9rem 0.75rem; vertical-align: middle;
                border-top: 1px solid var(--border-color) !important; border-bottom: none !important;
                color: var(--text-color);
            }
            .table-sales-report thead th {
                background-color: var(--table-header-bg); color: #fff; font-weight: 600; text-align: left;
                border-bottom: 2px solid var(--accent-color) !important;
            }
            .table-sales-report thead th:first-child { border-top-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-sales-report thead th:last-child { border-top-right-radius: calc(var(--card-border-radius) - 1px); }
            .table-sales-report tbody tr {
                background-color: var(--table-row-bg);
                transition: background-color var(--transition-speed) ease;
            }
            .table-sales-report tbody tr:hover { background-color: var(--table-row-hover-bg); }
            .table-sales-report tbody tr:hover td { color: #fff; }
            .table-sales-report tbody tr:last-child td:first-child { border-bottom-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-sales-report tbody tr:last-child td:last-child { border-bottom-right-radius: calc(var(--card-border-radius) - 1px); }
            .table-sales-report .order-id-link {
                font-weight: bold; color: var(--accent-color); text-decoration: none;
                transition: color var(--transition-speed) ease, text-shadow var(--transition-speed) ease;
            }
            .table-sales-report .order-id-link:hover { color: #fff; text-shadow: 0 0 5px var(--glow-color); }

            .alert-info {
                background-color: rgba(var(--secondary-bg-rgb), 0.7); border: 1px solid var(--border-color);
                color: var(--text-color); border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                border-left: 5px solid var(--accent-color) !important;
            }
            .section-title {
                color: #fff; font-weight: 500; margin-top: 2.5rem; margin-bottom: 1.5rem;
                padding-bottom: 10px; border-bottom: 1px solid var(--border-color);
            }
            .count-footer { color: var(--text-color-darker); }

            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3);
                backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                color: var(--text-color-darker); padding: 25px 0; text-align: center;
                border-top: 1px solid var(--border-color); margin-top: auto; position: relative; z-index: 1;
            }

            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .filter-form-wrapper, .table-wrapper { padding: 15px; }
                .summary-card { padding: 20px; }
                .summary-card p { font-size: 1.8em; }
                .table-sales-report th, .table-sales-report td { padding: 0.6rem 0.5rem; font-size: 0.8rem; }
                .filter-form-wrapper .form-label { font-size: 0.85rem; }
            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container-fluid report-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Sales Report</h1>
        </div>

        <!-- Filter Form -->
        <div class="filter-form-wrapper">
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label for="date_filter" class="form-label">Report Period:</label>
                    <select name="date_filter" id="date_filter" class="form-select" onchange="toggleCustomDate(this.value)">
                        <option value="all_time" <?php if ($date_filter == 'all_time') echo 'selected'; ?>>All Time</option>
                        <option value="today" <?php if ($date_filter == 'today') echo 'selected'; ?>>Today</option>
                        <option value="yesterday" <?php if ($date_filter == 'yesterday') echo 'selected'; ?>>Yesterday</option>
                        <option value="last_7_days" <?php if ($date_filter == 'last_7_days') echo 'selected'; ?>>Last 7 Days</option>
                        <option value="last_30_days" <?php if ($date_filter == 'last_30_days') echo 'selected'; ?>>Last 30 Days</option>
                        <option value="this_month" <?php if ($date_filter == 'this_month') echo 'selected'; ?>>This Month</option>
                        <option value="last_month" <?php if ($date_filter == 'last_month') echo 'selected'; ?>>Last Month</option>
                        <option value="custom_range" <?php if ($date_filter == 'custom_range') echo 'selected'; ?>>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-3" id="custom_start_date_div" style="<?php echo ($date_filter == 'custom_range') ? '' : 'display:none;'; ?>">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date_input); ?>">
                </div>
                <div class="col-md-3 col-lg-3" id="custom_end_date_div" style="<?php echo ($date_filter == 'custom_range') ? '' : 'display:none;'; ?>">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date_input); ?>">
                </div>
                <div class="col-md-2 col-lg-3 mt-md-0 mt-3"> <!-- Adjusted for button alignment -->
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-filter me-2"></i>Apply Filter</button>
                </div>
            </form>
        </div>

        <h4 class="text-center report-period-info mb-4">
            <i class="fas fa-calendar-alt me-2"></i>Report for: <strong><?php echo htmlspecialchars($report_period_text); ?></strong>
        </h4>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="summary-card">
                    <h4><i class="fas fa-dollar-sign"></i>Total Sales Amount</h4>
                    <p>₪ <?php echo number_format($total_sales, 2); ?></p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="summary-card">
                    <h4><i class="fas fa-shopping-cart"></i>Total Orders Placed</h4>
                    <p><?php echo number_format($total_orders); ?></p>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <h3 class="section-title"><i class="fas fa-list-ul me-2"></i>Orders in Selected Period</h3>
        <div class="table-wrapper">
            <div class="table-responsive-custom">
                <?php if (empty($orders_list)): ?>
                    <div class="alert alert-info text-center">No orders found for the selected period.</div>
                <?php else: ?>
                    <table class="table-sales-report">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>User ID</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders_list as $order):
                            $formatted_date = date("d M Y, H:i A", strtotime($order['OrderDate']));
                            ?>
                            <tr>
                                <td>
                                    <a href="adminorderdetails.php?oid=<?php echo htmlspecialchars($order['OrderID']); ?>" class="order-id-link">
                                        <?php echo htmlspecialchars($order['OrderID']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($order['FullName'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['UserIdentifier']); ?></td>
                                <td><?php echo htmlspecialchars($formatted_date); ?></td>
                                <td><?php echo htmlspecialchars($order['Status']); ?></td>
                                <td>₪ <?php echo number_format($order['BillAmount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($orders_list)): ?>
            <p class="text-center mt-4 count-footer"><?php echo count($orders_list); ?> order(s) listed for this period.</p>
        <?php endif; ?>
    </div>

    <br><br>

    <script>
        function toggleCustomDate(selectedValue) {
            var startDateDiv = document.getElementById('custom_start_date_div');
            var endDateDiv = document.getElementById('custom_end_date_div');
            var startDateInput = document.getElementById('start_date');
            var endDateInput = document.getElementById('end_date');

            if (selectedValue === 'custom_range') {
                startDateDiv.style.display = 'block';
                endDateDiv.style.display = 'block';
                // Optionally set required attribute if you want HTML5 validation
                // startDateInput.required = true;
                // endDateInput.required = true;
            } else {
                startDateDiv.style.display = 'none';
                endDateDiv.style.display = 'none';
                // startDateInput.required = false;
                // endDateInput.required = false;
                // Clear values if not custom range to avoid confusion if user switches back
                // startDateInput.value = '';
                // endDateInput.value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            toggleCustomDate(document.getElementById('date_filter').value);
        });
    </script>

    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); ?>
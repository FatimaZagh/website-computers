<?php
session_start();
ob_start(); // Good practice if headers might be sent later

// Admin security checks
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit;
}
if ($_SESSION["usertype"] !== "admin") {
    header("location:login.php");
    exit;
}

require_once("vars.php");
// extfiles.php should be included in the <head> for CSS
// adminnavbar.php will be included in the <body>

$search_term = "";
if (isset($_GET['search_name']) && !empty(trim($_GET['search_name']))) {
    $search_term = trim($_GET['search_name']);
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Members Management</title>
        <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --danger-color: #dc3545; /* Bootstrap danger */
                --danger-color-rgb: 220, 53, 69;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --glow-danger-color: rgba(var(--danger-color-rgb), 0.4);
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

            /* Navbar Styling */
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

            .management-main-content {
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

            .search-form-container {
                margin-bottom: 40px;
                padding: 25px 30px;
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                box-shadow: 0 8px 20px var(--shadow-color);
                border: 1px solid var(--border-color);
            }
            .search-form-container .form-control {
                background-color: rgba(var(--primary-bg), 0.7);
                color: var(--text-color);
                border: 1px solid var(--border-color);
                border-radius: 6px;
            }
            .search-form-container .form-control::placeholder {
                color: var(--text-color-darker);
            }
            .search-form-container .form-control:focus {
                background-color: rgba(var(--primary-bg), 0.9);
                border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color);
                color: var(--text-color);
            }
            .search-form-container .btn-primary {
                background-color: var(--accent-color);
                border-color: var(--accent-color);
                color: #fff;
                transition: background-color var(--transition-speed) ease, border-color var(--transition-speed) ease;
            }
            .search-form-container .btn-primary:hover {
                background-color: #0095e0;
                border-color: #0088cc;
            }
            .search-form-container .btn-outline-secondary {
                color: var(--text-color-darker);
                border-color: var(--border-color);
            }
            .search-form-container .btn-outline-secondary:hover {
                background-color: rgba(var(--secondary-bg-rgb), 0.3);
                color: var(--text-color);
                border-color: var(--text-color-darker);
            }

            .table-wrapper { /* Re-using from orders.php for consistency */
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                padding: 25px;
                box-shadow: 0 12px 35px var(--shadow-color);
                border: 1px solid var(--border-color);
                overflow: hidden;
            }

            .table-members {
                width: 100%;
                margin-bottom: 0;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 0.9rem;
            }

            .table-members th,
            .table-members td {
                padding: 0.9rem 0.75rem;
                vertical-align: middle;
                border-top: 1px solid var(--border-color) !important;
                border-bottom: none !important;
                color: var(--text-color);
                text-align: left; /* Default to left, center specific columns if needed */
            }
            .table-members th { text-align: center; } /* Center headers */
            .table-members td:nth-child(3), /* Phone */
            .table-members td:nth-child(5), /* Usertype */
            .table-members td:last-child   /* Action */
            { text-align: center; }


            .table-members thead th {
                background-color: var(--table-header-bg);
                color: #fff;
                font-weight: 600;
                border-bottom: 2px solid var(--accent-color) !important;
            }
            .table-members thead th:first-child { border-top-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-members thead th:last-child { border-top-right-radius: calc(var(--card-border-radius) - 1px); }

            .table-members tbody tr {
                background-color: var(--table-row-bg);
                transition: background-color var(--transition-speed) ease;
            }
            .table-members tbody tr:hover {
                background-color: var(--table-row-hover-bg);
                color: #fff;
            }
            .table-members tbody tr:hover td {
                color: #fff;
            }
            .table-members tbody tr:last-child td:first-child { border-bottom-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-members tbody tr:last-child td:last-child { border-bottom-right-radius: calc(var(--card-border-radius) - 1px); }

            .delbtn {
                display: inline-block;
                border: none;
                background-color: var(--danger-color);
                border-radius: 6px;
                color: white !important;
                padding: 6px 12px;
                text-decoration: none;
                font-size: 0.85em;
                font-weight: 500;
                transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            }
            .delbtn:hover {
                text-decoration: none;
                background-color: #c21a2b; /* Darker danger */
                color: white !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px var(--glow-danger-color);
            }

            .highlight-count {
                background-color: rgba(var(--accent-color-rgb), 0.2);
                color: var(--accent-color);
                border: 1px solid var(--accent-color);
                padding: 10px 20px;
                border-radius: var(--card-border-radius);
                font-weight: 600;
                display: inline-block;
                box-shadow: 0 0 15px var(--glow-color);
            }

            .alert { /* Styling alerts to fit the theme */
                background-color: rgba(var(--secondary-bg-rgb), 0.7);
                border: 1px solid var(--border-color);
                color: var(--text-color);
                border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);
            }
            .alert-warning { /* For "no results" */
                border-left: 5px solid #ffc107;
                color: #fff3cd;
                background-color: rgba(255,193,7,0.3);
            }
            .alert-info {
                border-left: 5px solid #17a2b8;
                color: #d1ecf1;
                background-color: rgba(23,162,184,0.3);
            }

            .results-info { /* For "Showing results for..." */
                color: var(--text-color-darker);
                margin-bottom: 20px;
                font-size: 1.1rem;
            }
            .results-info strong {
                color: var(--text-color);
            }

            .table-responsive-custom {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

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
                .search-form-container {
                    padding: 20px;
                }
                .table-wrapper {
                    padding: 15px;
                }
                .table-members th, .table-members td {
                    padding: 0.6rem 0.5rem;
                    font-size: 0.8rem;
                }
            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container management-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Manage Members List</h1>
        </div>

        <!-- Search Form -->
        <div class="search-form-container">
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="row g-3 align-items-center justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <label for="search_name" class="visually-hidden">Search by Name</label>
                    <input type="text" class="form-control form-control-lg" id="search_name" name="search_name" placeholder="Enter member name to search" value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search me-2"></i>Search</button>
                </div>
                <?php if (!empty($search_term)): ?>
                    <div class="col-auto">
                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-secondary btn-lg">Clear</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($search_term)): ?>
            <p class="lead text-center results-info">Showing results for: <strong>"<?php echo htmlspecialchars($search_term); ?>"</strong></p>
        <?php endif; ?>

        <div class="table-wrapper mt-4">
            <div class="table-responsive-custom">
                <?php
                $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

                // IMPORTANT: Ensure 'Phone Number' column name matches exactly what's in your database.
                // If it has a space, you MUST use backticks in SQL: `Phone Number`.
                // It's best practice to avoid spaces in column names (e.g., use PhoneNumber or phone_number).
                $q = "SELECT `Name`, `Username`, `Phone Number`, `Password`, `Usertype` FROM `signup_page`";

                if (!empty($search_term)) {
                    $escaped_search_term = mysqli_real_escape_string($connection, $search_term);
                    $q .= " WHERE `Name` LIKE '%" . $escaped_search_term . "%'";
                }
                $q .= " ORDER BY `Name` ASC";

                $res = mysqli_query($connection, $q) or die("Error in query: " . mysqli_error($connection));
                $rowcount = mysqli_num_rows($res);

                if ($rowcount == 0) {
                    $message = !empty($search_term) ?
                        "No members found matching: \"" . htmlspecialchars($search_term) . "\"." :
                        "No members found in the system.";
                    echo "<div class='alert " . (!empty($search_term) ? 'alert-warning' : 'alert-info') . " text-center col-md-8 mx-auto'>$message</div>";
                } else {
                    echo "<table class='table-members'>";
                    echo "<thead><tr><th>Name</th><th>Username (Email)</th><th>Phone</th><th>Password (Masked)</th><th>User Type</th><th>Action</th></tr></thead>";
                    echo "<tbody>";
                    while ($member = mysqli_fetch_assoc($res)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($member['Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['Username']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['Phone Number'] ?? 'N/A') . "</td>"; // Use null coalescing for safety
                        echo "<td>********</td>"; // MASKED PASSWORD - Good practice!
                        echo "<td>" . htmlspecialchars($member['Usertype']) . "</td>";
                        echo "<td><a href='delmemb.php?un=" . urlencode($member['Username']) . "' class='delbtn' onclick=\"return confirm('Are you sure you want to delete member: " . htmlspecialchars(addslashes($member['Name']), ENT_QUOTES) . "?');\"><i class='fas fa-trash-alt me-1'></i>Delete</a></td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
                mysqli_close($connection);
                ?>
            </div> <!-- .table-responsive-custom -->
        </div> <!-- .table-wrapper -->

        <?php if ($rowcount > 0): ?>
            <div class='text-center mt-4'><span class='highlight-count'>Members Found: <?php echo $rowcount; ?></span></div>
        <?php endif; ?>

    </div> <!-- .management-main-content -->

    <br><br><br>
    <?php require_once("footer.php"); ?>

    </body>
    </html>
<?php ob_end_flush(); ?>
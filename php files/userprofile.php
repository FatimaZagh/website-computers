<?php
session_start();
ob_start(); // Start output buffering

// Security check for login (applies to both user and admin viewing their profile)
if (!isset($_SESSION["pname"])) {
    header("location:login.php");
    exit;
}

require_once("vars.php"); // For db credentials, if not already included by headers
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($_SESSION["pname"]); ?> - Profile</title>
        <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff; /* Standard accent */
                --accent-color-user: #4caf50; /* A slightly different accent for user pages if needed, or keep same */
                --accent-color-user-rgb: 76, 175, 80;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --card-hover-bg-rgb: 48, 52, 61;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3); /* Using standard glow */
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
            }

            body {
                font-family: 'Poppins', sans-serif; background-color: var(--primary-bg); color: var(--text-color);
                margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh;
                overflow-x: hidden; position: relative;
            }
            body::before, body::after { /* Animated background blobs */
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

            /* Navbar Styling (Assuming adminnavbar.php and header.php have compatible structures or will be styled) */
            /* This is a generic placeholder, adjust based on your actual navbar classes */
            .navbar, .header-class-if-any { /* Target both potential navbars */
                background-color: rgba(var(--secondary-bg-rgb), 0.5) !important;
                backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--border-color); position: sticky; top: 0;
                z-index: 1000; padding: 0.75rem 1rem;
            }
            .navbar .navbar-brand, .navbar .nav-link,
            .header-class-if-any .brand-link, .header-class-if-any .nav-item-link {
                color: var(--text-color) !important; font-weight: 500;
                transition: color var(--transition-speed) ease;
            }
            .navbar .nav-link:hover, .navbar .navbar-brand:hover,
            .header-class-if-any .nav-item-link:hover, .header-class-if-any .brand-link:hover {
                color: var(--accent-color) !important;
                text-shadow: 0 0 8px var(--glow-color);
            }
            #acc:hover { /* Your specific ID from original CSS */
                color: var(--accent-color) !important;
                text-shadow: 0 0 8px var(--glow-color);
            }


            .page-main-content { flex-grow: 1; padding-top: 100px; padding-bottom: 60px; position: relative; z-index: 1; }
            .page-title {
                color: #fff; font-weight: 600; margin-bottom: 40px; padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color); text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block;
            }
            .text-center .page-title { display: block; width: fit-content; margin-left: auto; margin-right: auto; }

            .profile-card-wrapper {
                max-width: 700px; /* Limit width of the profile card area */
                margin-left: auto;
                margin-right: auto;
            }
            .profile-card {
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                padding: 30px 40px;
                box-shadow: 0 10px 30px var(--shadow-color);
                border: 1px solid var(--border-color);
                text-align: left; /* Align text to left within card */
            }
            .profile-card .profile-detail {
                display: flex;
                justify-content: space-between;
                padding: 12px 0;
                border-bottom: 1px solid var(--border-color);
                font-size: 1.1rem;
            }
            .profile-card .profile-detail:last-of-type {
                border-bottom: none;
            }
            .profile-card .detail-label {
                font-weight: 500;
                color: var(--text-color-darker);
                margin-right: 15px;
            }
            .profile-card .detail-value {
                font-weight: 500;
                color: var(--text-color);
                word-break: break-all; /* For long user IDs/emails */
            }
            .profile-card .user-avatar { /* Optional: If you add an avatar */
                width: 100px; height: 100px; border-radius: 50%;
                margin: 0 auto 25px auto; display: block;
                border: 3px solid var(--accent-color);
                object-fit: cover;
            }


            .profile-actions {
                margin-top: 30px;
                display: flex;
                flex-direction: column; /* Stack links */
                align-items: center; /* Center links */
                gap: 15px; /* Space between links */
            }
            .action-button-link { /* Replaces .lnkbtn */
                background-color: rgba(var(--secondary-bg-rgb), 0.5);
                backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
                border: 1px solid var(--border-color);
                border-radius: 8px; /* Slightly less rounded than cards */
                padding: 12px 25px;
                color: var(--text-color);
                text-decoration: none;
                font-weight: 500;
                font-size: 1rem;
                width: 100%; /* Full width within its flex container if needed, or set max-width */
                max-width: 350px; /* Control max width of buttons */
                text-align: center;
                transition: transform var(--transition-speed) ease, background-color var(--transition-speed) ease,
                box-shadow var(--transition-speed) ease, border-color var(--transition-speed) ease;
            }
            .action-button-link:hover {
                background-color: rgba(var(--card-hover-bg-rgb), 0.7);
                transform: translateY(-3px);
                color: #fff;
                box-shadow: 0 8px 15px var(--shadow-color), 0 0 10px var(--glow-color);
                border-color: rgba(var(--accent-color-rgb), 0.4);
            }
            .action-button-link i {
                margin-right: 8px;
            }
            .action-button-link.logout { /* Special style for logout if needed */
                border-color: rgba(220, 53, 69, 0.5); /* Danger accent */
            }
            .action-button-link.logout:hover {
                background-color: rgba(220, 53, 69, 0.3); /* Danger background on hover */
                border-color: rgba(220, 53, 69, 0.8);
                box-shadow: 0 8px 15px var(--shadow-color), 0 0 10px rgba(220, 53, 69, 0.4);
            }


            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3); backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px); color: var(--text-color-darker);
                padding: 25px 0; text-align: center; border-top: 1px solid var(--border-color);
                margin-top: auto; position: relative; z-index: 1;
            }

            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .profile-card { padding: 20px; }
                .profile-card .profile-detail { flex-direction: column; align-items: flex-start; }
                .profile-card .detail-label { margin-bottom: 5px; }
                .action-button-link { max-width: 90%; }
            }
        </style>
    </head>
    <body>

    <?php
    // Header Condition For Admin and Normal User
    // Ensure session_id() check is reliable or remove if session_start() is always at the top.
    // $usertype is already set if session is started.
    $usertype = $_SESSION["usertype"];
    if ($usertype == 'admin') {
        require_once("adminnavbar.php");
    } else {
        require_once("header.php"); // Ensure header.php is styled or adapts
    }
    ?>

    <div class="container page-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5"><i class="fas fa-user-circle me-2"></i>User Profile</h1>
        </div>

        <div class="profile-card-wrapper">
            <div class="profile-card">
                <?php
                $uname_display = $_SESSION['pname']; // Already HTML-safe if set correctly during login/signup

                // It's better to use User ID from session if available, rather than querying by Name
                // For now, sticking to your existing logic of querying by Name
                $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());

                // IMPORTANT: Querying by 'Name' can be problematic if names are not unique.
                // It's much safer to query by 'Username' (email/unique ID) or a stored user_id.
                // Assuming 'Name' is the display name from $_SESSION['pname']
                // And 'Username' is the unique login identifier (email) in the database.
                // If $_SESSION['pname'] IS the unique identifier, then this query is fine.
                // Otherwise, you need to adjust. Let's assume $_SESSION['pname'] can be used to find the unique user.

                // Option 1: If $_SESSION['pname'] is the unique Username/Email
                // $q = "SELECT `Name`, `Username`, `Phone Number`, `Usertype` FROM `signup_page` WHERE `Username` = ?";
                // $stmt = mysqli_prepare($connection, $q);
                // mysqli_stmt_bind_param($stmt, "s", $uname_display);

                // Option 2: Sticking to your current query by 'Name' (less ideal for uniqueness)
                $q = "SELECT `Name`, `Username`, `Phone Number`, `Usertype` FROM `signup_page` WHERE `Name` = ?";
                $stmt = mysqli_prepare($connection, $q);
                mysqli_stmt_bind_param($stmt, "s", $uname_display);

                mysqli_stmt_execute($stmt);
                $res_profile = mysqli_stmt_get_result($stmt);

                if ($res_profile && mysqli_num_rows($res_profile) > 0) {
                    $profile_data = mysqli_fetch_assoc($res_profile);
                    // Store unique User ID (Username/email) in session if not already there from login
                    // This is important for future operations.
                    if (!isset($_SESSION["userid"]) || $_SESSION["userid"] !== $profile_data['Username']) {
                        $_SESSION["userid"] = $profile_data['Username'];
                    }

                    // Optional: If you had a user avatar/profile picture
                    // echo "<img src='path/to/avatar/" . htmlspecialchars($profile_data['avatar_filename'] ?? 'default-avatar.png') . "' alt='User Avatar' class='user-avatar'>";
                    ?>
                    <div class="profile-detail">
                        <span class="detail-label"><i class="fas fa-user me-2"></i>Display Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($profile_data['Name']); ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label"><i class="fas fa-envelope me-2"></i>User ID (Email):</span>
                        <span class="detail-value"><?php echo htmlspecialchars($profile_data['Username']); ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label"><i class="fas fa-phone me-2"></i>Phone Number:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($profile_data['Phone Number'] ?? 'Not Provided'); ?></span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label"><i class="fas fa-user-tag me-2"></i>Account Type:</span>
                        <span class="detail-value"><?php echo htmlspecialchars(ucfirst($profile_data['Usertype'])); ?></span>
                    </div>
                    <?php
                } else {
                    echo "<p class='text-danger text-center'>Could not retrieve profile information.</p>";
                }
                mysqli_stmt_close($stmt);
                mysqli_close($connection);
                ?>
            </div>

            <div class="profile-actions">
                <a href="changepass.php" class="action-button-link"><i class="fas fa-key"></i>Change Password</a>
                <?php if ($usertype == 'admin'): // Admin-specific links can go here, if any for their own profile ?>
                    <!-- e.g., <a href="admin_settings.php" class="action-button-link"><i class="fas fa-cogs"></i>Admin Settings</a> -->
                <?php endif; ?>
                <a href="logout.php" class="action-button-link logout"><i class="fas fa-sign-out-alt"></i>Log Out</a>
            </div>
        </div> <!-- /.profile-card-wrapper -->
    </div> <!-- /.page-main-content -->

    <br><br>
    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); ?>
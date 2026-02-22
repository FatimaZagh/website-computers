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

// --- Handle Mark as Read/Unread and Delete ---
// Open connection only if an action is present to avoid unnecessary connections on page load
if (isset($_GET['action']) && isset($_GET['id'])) {
    $connection_action = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("DB connection error for action: " . mysqli_connect_error());
    $action = $_GET['action'];
    $message_id = (int)$_GET['id'];

    if ($action == 'toggle_read') {
        $stmt_current = mysqli_prepare($connection_action, "SELECT is_read FROM feedback_messages WHERE id = ?");
        mysqli_stmt_bind_param($stmt_current, "i", $message_id);
        mysqli_stmt_execute($stmt_current);
        mysqli_stmt_bind_result($stmt_current, $current_is_read);
        $fetched = mysqli_stmt_fetch($stmt_current); // Check if a row was found
        mysqli_stmt_close($stmt_current);

        if ($fetched) { // Proceed only if the feedback ID exists
            $new_is_read = $current_is_read ? 0 : 1;
            $stmt_toggle = mysqli_prepare($connection_action, "UPDATE feedback_messages SET is_read = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_toggle, "ii", $new_is_read, $message_id);
            mysqli_stmt_execute($stmt_toggle);
            mysqli_stmt_close($stmt_toggle);
        }
    } elseif ($action == 'delete') {
        $stmt_delete = mysqli_prepare($connection_action, "DELETE FROM feedback_messages WHERE id = ?");
        mysqli_stmt_bind_param($stmt_delete, "i", $message_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
    }
    mysqli_close($connection_action);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Customer Feedback</title>
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
                --info-color: #17a2b8; /* Bootstrap info */
                --info-color-rgb: 23, 162, 184;
                --danger-color: #dc3545;
                --danger-color-rgb: 220, 53, 69;
                --warning-color-rgb: 255, 193, 7; /* For unread */
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --glow-danger-color: rgba(var(--danger-color-rgb), 0.4);
                --glow-info-color: rgba(var(--info-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --table-header-bg: rgba(var(--secondary-bg-rgb), 0.7);
                --table-row-bg: rgba(var(--secondary-bg-rgb), 0.5);
                --table-row-hover-bg: rgba(var(--secondary-bg-rgb), 0.8);
                --unread-bg-color: rgba(var(--warning-color-rgb), 0.15); /* Subtle yellow for unread */
                --unread-text-color: #fff3cd; /* Lighter text for unread rows */
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

            .feedback-main-content {
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

            .table-feedback {
                width: 100%;
                margin-bottom: 0;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 0.9rem;
            }

            .table-feedback th,
            .table-feedback td {
                padding: 0.9rem 0.75rem;
                vertical-align: middle;
                border-top: 1px solid var(--border-color) !important;
                border-bottom: none !important;
                color: var(--text-color);
            }

            .table-feedback thead th {
                background-color: var(--table-header-bg);
                color: #fff;
                font-weight: 600;
                text-align: left;
                border-bottom: 2px solid var(--accent-color) !important;
            }
            .table-feedback thead th:first-child { border-top-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-feedback thead th:last-child { border-top-right-radius: calc(var(--card-border-radius) - 1px); }

            .table-feedback tbody tr {
                background-color: var(--table-row-bg);
                transition: background-color var(--transition-speed) ease;
            }
            .table-feedback tbody tr:hover {
                background-color: var(--table-row-hover-bg);
                /* color: #fff; /* Text color change handled by td below */
            }
            .table-feedback tbody tr:hover td {
                color: #fff;
            }
            .table-feedback tbody tr:last-child td:first-child { border-bottom-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-feedback tbody tr:last-child td:last-child { border-bottom-right-radius: calc(var(--card-border-radius) - 1px); }

            .message-unread td {
                font-weight: bold;
                background-color: var(--unread-bg-color) !important; /* Important to override hover if needed */
                color: var(--unread-text-color) !important; /* Ensure text is readable on unread bg */
            }
            .message-unread:hover td { /* Keep unread style on hover */
                background-color: var(--unread-bg-color) !important;
                color: var(--unread-text-color) !important;
            }
            .message-unread td a { /* Make links visible on unread bg */
                color: var(--unread-text-color) !important;
            }


            .message-content {
                max-width: 350px; /* Adjust as needed */
                min-width: 250px;
                overflow-wrap: break-word;
                white-space: pre-wrap; /* Preserve line breaks */
            }

            .action-links a.btn {
                margin-right: 8px;
                margin-bottom: 5px; /* For stacking on small screens if needed */
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
                font-weight: 500;
                border-radius: 6px;
                transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            }
            .action-links a.btn-info {
                background-color: var(--info-color);
                border-color: var(--info-color);
                color: white;
            }
            .action-links a.btn-info:hover {
                background-color: #138496; /* Darker info */
                border-color: #117a8b;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px var(--glow-info-color);
            }
            .action-links a.btn-danger {
                background-color: var(--danger-color);
                border-color: var(--danger-color);
                color: white;
            }
            .action-links a.btn-danger:hover {
                background-color: #c21a2b; /* Darker danger */
                border-color: #b8192a;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px var(--glow-danger-color);
            }
            .action-links a i {
                margin-right: 4px;
            }

            .alert {
                background-color: rgba(var(--secondary-bg-rgb), 0.7);
                border: 1px solid var(--border-color);
                color: var(--text-color);
                border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);
            }
            .alert-info {
                border-left: 5px solid var(--info-color);
                color: #d1ecf1;
                background-color: rgba(var(--info-color-rgb),0.3);
            }

            .table-responsive-custom {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .count-footer {
                color: var(--text-color-darker);
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
                .table-wrapper {
                    padding: 15px;
                }
                .table-feedback th, .table-feedback td {
                    padding: 0.6rem 0.5rem;
                    font-size: 0.8rem;
                }
                .message-content {
                    max-width: 200px;
                    min-width: 150px;
                }
                .action-links a.btn {
                    display: block; /* Stack buttons on small screens */
                    margin-bottom: 8px;
                    width: 100%;
                }
                .action-links a.btn:last-child { margin-bottom: 0; }

            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container-fluid feedback-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Customer Feedback Messages</h1>
        </div>
        <br>

        <div class="table-wrapper mt-4">
            <div class="table-responsive-custom">
                <?php
                $connection_display = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());
                $q = "SELECT id, name, email, subject, message, submission_date, is_read 
                  FROM feedback_messages 
                  ORDER BY is_read ASC, submission_date DESC"; // Show unread first, then by date
                $res = mysqli_query($connection_display, $q) or die("Error in query: " . mysqli_error($connection_display));
                $rescount = mysqli_num_rows($res);

                if ($rescount == 0) {
                    echo "<div class='alert alert-info text-center col-md-8 mx-auto'>No feedback messages found.</div>";
                } else {
                    // Removed Bootstrap table classes from <table> tag
                    echo "<table class='table-feedback'>";
                    echo "
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th class='message-content-th'>Message</th> <!-- Specific class for header if needed -->
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";
                    while ($fb = mysqli_fetch_assoc($res)) {
                        $row_class = $fb['is_read'] ? '' : 'message-unread';
                        $read_status_text = $fb['is_read'] ? '<i class="fas fa-check-circle text-success me-1"></i>Read' : '<i class="fas fa-envelope text-warning me-1"></i>Unread';
                        $toggle_read_text = $fb['is_read'] ? '<i class="fas fa-eye-slash"></i> Mark Unread' : '<i class="fas fa-eye"></i> Mark Read';
                        $formatted_date = date("d M Y, H:i", strtotime($fb['submission_date']));

                        echo "<tr class='$row_class'>";
                        echo "<td>" . htmlspecialchars($fb['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($fb['name']) . "</td>";
                        echo "<td><a href='mailto:" . htmlspecialchars($fb['email']) . "'>" . htmlspecialchars($fb['email']) . "</a></td>";
                        echo "<td>" . htmlspecialchars($fb['subject']) . "</td>";
                        echo "<td class='message-content'>" . nl2br(htmlspecialchars($fb['message'])) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
                        echo "<td>" . $read_status_text . "</td>"; // Using HTML for icons
                        echo "<td class='action-links'>
                            <a href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?action=toggle_read&id=" . $fb['id'] . "' class='btn btn-sm btn-info'>" . $toggle_read_text . "</a>
                            <a href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?action=delete&id=" . $fb['id'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this message from " . htmlspecialchars(addslashes($fb['name']), ENT_QUOTES) . "?');\"><i class='fas fa-trash-alt'></i> Delete</a>
                          </td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
                mysqli_close($connection_display);
                ?>
            </div> <!-- .table-responsive-custom -->
        </div> <!-- .table-wrapper -->

        <?php if ($rescount > 0): ?>
            <p class="text-center mt-4 count-footer"><?php echo "$rescount feedback message(s) found"; ?></p>
        <?php endif; ?>
    </div> <!-- .feedback-main-content -->

    <br><br>
    <?php require_once("footer.php"); ?>

    </body>
    </html>
<?php ob_end_flush(); ?>
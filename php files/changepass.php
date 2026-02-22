<?php
session_start();
ob_start(); // Start output buffering

// Security check: User must be logged in to change password
if (!isset($_SESSION["pname"]) || !isset($_SESSION["userid"])) { // Check for both display name and user identifier
    header("location:login.php");
    exit;
}

require_once("vars.php"); // For db credentials

$change_message = ""; // To store success/error messages

if (isset($_POST["cnge"])) {
    $userid = $_SESSION["userid"]; // Use the unique userid from session
    $cpass = $_POST["cpass"];
    $newpass = $_POST["npass"];
    $cnewpass = $_POST["cnpass"];

    // Basic Validations
    if (empty($cpass) || empty($newpass) || empty($cnewpass)) {
        $change_message = "<p class='text-danger'>All password fields are required.</p>";
    } elseif (strlen($newpass) < 6) { // Example: Enforce minimum password length
        $change_message = "<p class='text-danger'>New password must be at least 6 characters long.</p>";
    } elseif ($newpass !== $cnewpass) {
        $change_message = "<p class='text-danger'>New passwords do not match.</p>";
    } else {
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());

        // IMPORTANT: You should be HASHING passwords, not storing them in plain text.
        // This example assumes you are NOT hashing for now to match your current logic.
        // In a real application, you would:
        // 1. Fetch the hashed password for $userid.
        // 2. Use password_verify($cpass, $hashed_password_from_db) to check current password.
        // 3. If verified, hash $newpass using password_hash($newpass, PASSWORD_DEFAULT).
        // 4. Update the database with the new hashed password.

        // For this example, sticking to your current direct password comparison logic:
        // Use prepared statements for security
        $q = "UPDATE signup_page SET Password = ? WHERE Username = ? AND Password = ?";
        $stmt = mysqli_prepare($connection, $q);
        // Assuming passwords are not hashed, so binding them as strings.
        // If they were hashed, the types might differ.
        mysqli_stmt_bind_param($stmt, "sss", $newpass, $userid, $cpass);

        mysqli_stmt_execute($stmt);
        $rowcount = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($connection);

        if ($rowcount == 1) {
            $change_message = "<p class='text-success'>Password Changed Successfully!</p>";
        } else {
            $change_message = "<p class='text-danger'>Incorrect current password or an error occurred.</p>";
        }
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change Password</title>
        <?php require_once("extfiles.php"); // Bootstrap CSS etc. ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --success-color: #28a745; /* Bootstrap success */
                --danger-color: #dc3545;  /* Bootstrap danger */
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --input-bg: rgba(var(--primary-bg), 0.7);
            }

            body {
                font-family: 'Poppins', sans-serif; background-color: var(--primary-bg); color: var(--text-color);
                margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh;
                overflow-x: hidden; position: relative;
            }
            body::before, body::after {
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

            /* Navbar Styling - adjust if your header.php has different classes */
            .navbar, .header-class-if-any {
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
            #acc:hover {
                color: var(--accent-color) !important;
                text-shadow: 0 0 8px var(--glow-color);
            }

            .page-main-content { flex-grow: 1; padding-top: 60px; padding-bottom: 60px; position: relative; z-index: 1; }
            .page-title {
                color: #fff; font-weight: 600; margin-bottom: 30px; padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color); text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block; font-size: 2.2rem; /* Adjusted size from display-4 */
            }
            .text-center .page-title { display: block; width: fit-content; margin-left: auto; margin-right: auto; }

            .form-wrapper { /* Replaces .back and centers content */
                max-width: 500px; /* Control form width */
                margin: 0 auto; /* Center the wrapper */
                padding: 30px 40px;
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                box-shadow: 0 10px 30px var(--shadow-color);
                border: 1px solid var(--border-color);
            }
            .form-wrapper .form-control {
                background-color: var(--input-bg); color: var(--text-color);
                border: 1px solid var(--border-color); border-radius: 6px;
                padding: .75rem 1rem; /* Larger padding for inputs */
                margin-bottom: 1rem; /* Space between inputs */
                width: 100%;
            }
            .form-wrapper .form-control::placeholder { color: var(--text-color-darker); font-size: 0.9rem; }
            .form-wrapper .form-control:focus {
                background-color: rgba(var(--primary-bg), 0.9);
                border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color);
                color: var(--text-color);
            }

            /* Password Toggle Styles */
            .password-container {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .password-container .form-control {
                padding-right: 45px; /* Space for eye icon */
            }

            .password-toggle {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: var(--text-color-darker);
                font-size: 16px;
                transition: color 0.3s ease;
                z-index: 10;
                user-select: none;
            }

            .password-toggle:hover {
                color: var(--accent-color);
            }

            .password-toggle.active {
                color: var(--accent-color);
            }

            .password-toggle i {
                transition: transform 0.2s ease;
            }

            .password-toggle:hover i {
                transform: scale(1.1);
            }
            .form-wrapper .btn-submit-password { /* Replaces .logbtn */
                background-color: var(--accent-color); border-color: var(--accent-color);
                color: #fff; font-weight: 500; padding: 0.6rem 1.5rem;
                width: 100%; font-size: 1.1rem; border-radius: 6px;
                transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease;
            }
            .form-wrapper .btn-submit-password:hover {
                background-color: #0095e0; border-color: #0088cc;
                transform: translateY(-2px);
            }
            .form-message { margin-top: 20px; text-align: center; font-weight: 500; }
            .form-message p { margin-bottom: 0.5rem; font-size: 0.95rem; }
            .text-success { color: var(--success-color) !important; }
            .text-danger { color: var(--danger-color) !important; }

            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3); backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px); color: var(--text-color-darker);
                padding: 25px 0; text-align: center; border-top: 1px solid var(--border-color);
                margin-top: auto; position: relative; z-index: 1;
            }

            /* Remove old table specific styles as we are not using table for layout */
            /*
            table tr td{ width: 150px; text-align: center; padding: 5px; }
            table tr th{ width: 150px; text-align: center; padding: 5px; }
            table{ margin-top:40px; }
            */

            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .form-wrapper { margin-left: 15px; margin-right: 15px; padding: 25px; }
            }
        </style>
    </head>
    <body>

    <?php
    // Header Condition
    // $usertype is already set if session is started.
    $usertype = $_SESSION["usertype"] ?? 'guest'; // Default to guest if not set, though login check should prevent this
    if ($usertype == 'admin') {
        require_once("adminnavbar.php");
    } else {
        require_once("header.php"); // Ensure header.php is styled or adapts
    }
    ?>

    <div class="container page-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title"><i class="fas fa-key me-2"></i>Change Your Password</h1>
        </div>

        <div class="form-wrapper">
            <form name="changePasswordForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-3">
                    <label for="cpass" class="form-label visually-hidden">Current Password</label>
                    <div class="password-container">
                        <input type="password" id="cpass" name="cpass" placeholder="Current Password" class="form-control" required>
                        <span class="password-toggle" onclick="togglePassword('cpass', this)">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="npass" class="form-label visually-hidden">New Password</label>
                    <div class="password-container">
                        <input type="password" id="npass" name="npass" placeholder="New Password (min. 6 characters)" class="form-control" required>
                        <span class="password-toggle" onclick="togglePassword('npass', this)">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cnpass" class="form-label visually-hidden">Confirm New Password</label>
                    <div class="password-container">
                        <input type="password" id="cnpass" name="cnpass" placeholder="Confirm New Password" class="form-control" required>
                        <span class="password-toggle" onclick="togglePassword('cnpass', this)">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" name="cnge" class="btn btn-submit-password"><i class="fas fa-check-circle me-2"></i>Change Password</button>
                </div>

                <?php if (!empty($change_message)): ?>
                    <div class="form-message mt-3"><?php echo $change_message; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <br><br>
    <?php require_once("footer.php"); ?>

    <script>
        function togglePassword(inputId, toggleElement) {
            const passwordInput = document.getElementById(inputId);
            const icon = toggleElement.querySelector('i');

            if (passwordInput.type === "password") {
                // Show password - change to open eye (password visible)
                passwordInput.type = "text";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                toggleElement.classList.add('active');
                toggleElement.title = "Hide password";
            } else {
                // Hide password - change to closed eye (password hidden)
                passwordInput.type = "password";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                toggleElement.classList.remove('active');
                toggleElement.title = "Show password";
            }
        }

        // Initialize tooltips on page load
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggles = document.querySelectorAll('.password-toggle');
            passwordToggles.forEach(function(toggle) {
                toggle.title = "Show password";
            });
        });
    </script>

    </body>
    </html>
<?php ob_end_flush(); ?>
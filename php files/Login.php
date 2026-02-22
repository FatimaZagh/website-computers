<?php
session_start(); // Start session AT THE VERY TOP
ob_start(); // Start output buffering

require_once("extfiles.php");
require_once("vars.php"); // For dbhost, dbuname, dbpass, dbname

// It's good practice to initialize variables
$message = ""; // To display messages to the user
?>
    <html xmlns:cellspacing="http://www.w3.org/1999/xhtml">
    <head>
        <title>
            Login
        </title>
        <!-- Font Awesome for eye icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body{ background-color:#fafafa;}
            ::placeholder{ font-size:13px; background-color:#fafafa; }
            table tr td{ padding-top:10px; padding-bottom:10px; }
            input{ border-radius:4px; border-style:solid; padding:5px; }
            table{ margin-top:40px; }
            .logbtn{ background-color: #0095f6; border: none; color: white; padding-top: 10px; padding-bottom: 10px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; width:100%; border-radius:4px; cursor: pointer; }
            .logbtn:hover{ background-color:#0086dd; }
            .signup{ text-decoration:none; }
            .signup:hover{ text-decoration:none; }
            .back{ background-color:white;margin-left:auto;margin-right:auto; max-width: 500px; padding: 20px; border: 1px solid #dbdbdb; border-radius: 3px;} /* Centered and max-width */
            .forpass{ color:#204891; font-size:12px; }
            .forpass:hover{ text-decoration:none; }
            .error-msg { color: red; text-align: center; margin-bottom: 10px; }
            .success-msg { color: green; text-align: center; margin-bottom: 10px; } /* For success messages */
            @media (max-width:940px){ .back{ margin-left:20px; margin-right:20px; } }
            input[type="text"],
            input[type="password"] {
                width: 100%;
                box-sizing: border-box;
                padding: 8px 40px 8px 8px; /* extra right padding for eye icon */
                font-size: 13px;
                background-color: #fafafa;
                border-radius: 4px;
                border: 1px solid #ccc;
                height: 36px; /* fixed height for consistency */
            }

            /* Password Toggle Styles */
            .password-container {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .password-toggle {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #666;
                font-size: 16px;
                transition: color 0.3s ease;
                z-index: 10;
                user-select: none;
            }

            .password-toggle:hover {
                color: #0095f6;
            }

            .password-toggle.active {
                color: #0095f6;
            }

            /* Eye icon animations */
            .password-toggle i {
                transition: transform 0.2s ease;
            }

            .password-toggle:hover i {
                transform: scale(1.1);
            }

        </style>
    </head>
    <body style="background:#fafafa;">
    <?php require_once("header.php"); ?>
    <br><br><br>
    <div class="back">
        <h1 class="display-4"align="center">Login</h1>

        <?php
        // Display success message from registration if set
        if (isset($_SESSION['success_message'])) {
            echo '<p class="success-msg">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
            unset($_SESSION['success_message']); // Clear it after displaying
        }
        // Display any error messages from login attempt
        if (!empty($message)) {
            echo '<p class="error-msg">' . htmlspecialchars($message) . '</p>';
        }
        ?>

        <form name="login" method="post" action="login.php"> <!-- Added action attribute -->
            <table align="center" style="width:100%; max-width: 400px; margin: 0 auto;">
            <!-- Added style for better layout -->
                <tr>
                    <td>
                        <input type="text" placeholder="Enter Email" name="username" required
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="password-container">
                            <input type="password" id="password" placeholder="Enter Password" name="pass" required>
                            <span class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye-slash" id="password-icon"></i>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr align="center">
                    <td><input type="submit" class="logbtn" value="Log In" name="lbtn"></td>
                </tr>
                <tr align="center">
                    <td><hr align="center" color="#dbdbdb"></td> <!-- Lighter hr color -->
                </tr>
                <tr align="center">
                    <td><a href="forgetpass.php" class="forpass">Forgot Password ?</a></td>
                </tr>
                <tr align="center">
                    <td style="font-size:15px;">Don't have an account? <a href="signup.php" class="signup">Sign up</a></td>
                </tr>
            </table>
        </form>
    </div>
    <br><br>

    <?php
    // Moved PHP processing block here for better structure
    // ob_start(); // Already called at the top

    if (isset($_POST["lbtn"])) {
        $username_email_submitted = trim($_POST["username"]); // Renamed for clarity
        $plain_password_submitted = $_POST["pass"];

        if (empty($username_email_submitted) || empty($plain_password_submitted)) {
            $message = "Please enter both email and password.";
        } else {
            $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);

            if (!$connection) {
                $message = "Database connection error. Please try again later.";
                error_log("Login DB connection error: " . mysqli_connect_error()); // Log error
            } else {
                // Use prepared statements to prevent SQL injection
                // Select all necessary fields: Name, Username (for session), Password (for verification), Usertype
                $q = "SELECT `Name`, `Username`, `Password`, `Usertype` FROM signup_page WHERE username = ?";
                $stmt = mysqli_prepare($connection, $q);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $username_email_submitted);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $user_data = mysqli_fetch_assoc($result); // Fetch as associative array

                    if ($user_data) { // User found
                        $hashed_password_from_db = $user_data['Password'];

                        // ****** THE CRITICAL CHANGE: USE password_verify() ******
                        if (password_verify($plain_password_submitted, $hashed_password_from_db)) {
                            // Password is correct!
                            $_SESSION["pname"] = $user_data['Name'];
                            $_SESSION["usertype"] = $user_data['Usertype'];
                            $_SESSION["userprimid"] = $user_data['Username']; // Store email as userprimid

                            // Regenerate session ID for security after login
                            session_regenerate_id(true);

                            if ($user_data['Usertype'] == "admin") {
                                mysqli_stmt_close($stmt);
                                mysqli_close($connection);
                                header("location:adminhome.php");
                                exit(); // Always exit after a header redirect
                            } elseif ($user_data['Usertype'] == "normal") {
                                mysqli_stmt_close($stmt);
                                mysqli_close($connection);
                                header("location:index.php");
                                exit(); // Always exit after a header redirect
                            } else {
                                // Should not happen if Usertype is always 'admin' or 'normal'
                                $message = "Unknown user type.";
                            }
                        } else {
                            // Password not matched
                            $message = "Invalid email or password.";
                        }
                    } else {
                        // No user found with that email
                        $message = "Invalid email or password.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    // Error preparing the statement
                    $message = "Login query error. Please try again.";
                    error_log("Login mysqli_prepare error: " . mysqli_error($connection)); // Log error
                }
                mysqli_close($connection);
            }
        }
        // If there was a message, the script will continue and display it above the form
        // No need for an explicit "else" here for the message display
        // We need to re-display the form if login fails or if there are messages.
        // So, the HTML part should not be inside an else block of the login processing.
        // To ensure the message is displayed if set:
        if (!empty($message)) {
            // One way to ensure the message is displayed immediately before the footer if redirection didn't happen
            // This is a bit of a workaround if the message display above the form isn't working as expected
            // or if you prefer the message to appear below the form content.
            // However, the current structure should display it above the form.
            // For clarity, I'm removing this echo and relying on the one above the form.
            // echo '<div align="center" style="color:red; margin-top:10px;">' . htmlspecialchars($message) . '</div>';
        }
    }
    ?>
    <br><br><br>
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
<?php ob_end_flush(); // Send output buffer and turn off output buffering ?>
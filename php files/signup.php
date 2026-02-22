<?php
session_start(); // Start session at the very beginning
ob_start(); // Start output buffering

// --- PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php'; // Adjust path if needed
require 'PHPMailer/SMTP.php';       // Adjust path if needed
require 'PHPMailer/Exception.php';  // Adjust path if needed
// -----------------

require_once("extfiles.php"); // Your existing includes
require_once("vars.php");    // For dbhost, dbuname, dbpass, dbname

$message = []; // To store messages for the user

if (isset($_POST["signup"])) {
    $name = trim($_POST["pname"]);
    $phone = trim($_POST["pnumber"]);
    $username_email = trim($_POST["username"]); // This is the email
    $password = $_POST["pass"];
    $cpass = $_POST["cpass"];
    $admin_code_submitted = isset($_POST["admin_code"]) ? trim($_POST["admin_code"]) : "";

    // --- DEFINE YOUR SECRET ADMIN CODE ---
    $your_actual_secret_admin_code = "123"; // !!! CHANGE THIS !!!
    // -------------------------------------

    $usertype_for_session = 'normal'; // Default to normal user
    if (!empty($admin_code_submitted) && $admin_code_submitted === $your_actual_secret_admin_code) {
        $usertype_for_session = 'admin';
    }

    if (empty($name) || empty($phone) || empty($username_email) || empty($password)) {
        $message[] = "Please fill in all required fields.";
    } elseif (!filter_var($username_email, FILTER_VALIDATE_EMAIL)) {
        $message[] = "Invalid email format.";
    } elseif ($password != $cpass) {
        $message[] = "Passwords didn't match.";
    } else {
        // All basic client-side validations passed, now check DB for existing email
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());

        $email_check_sql = "SELECT `Username` FROM `signup_page` WHERE `Username` = ?";
        $stmt_email_check = mysqli_prepare($connection, $email_check_sql);
        mysqli_stmt_bind_param($stmt_email_check, "s", $username_email);
        mysqli_stmt_execute($stmt_email_check);
        mysqli_stmt_store_result($stmt_email_check);

        if (mysqli_stmt_num_rows($stmt_email_check) > 0) {
            $message[] = "This email address is already registered.";
            mysqli_stmt_close($stmt_email_check);
            mysqli_close($connection);
        } else {
            mysqli_stmt_close($stmt_email_check); // Close this statement
            mysqli_close($connection); // Close connection, will reopen if needed or can keep open

            // Store details in session
            $_SESSION['signup_name'] = $name;
            $_SESSION['signup_phone'] = $phone;
            $_SESSION['signup_email'] = $username_email;
            $_SESSION['signup_password'] = $password; // Store plain password temporarily
            $_SESSION['signup_usertype'] = $usertype_for_session;

            // Generate verification code
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;

            // Send verification email
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fatima.a.zaghlol@gmail.com'; // YOUR GMAIL or SMTP username
                $mail->Password   = 'hgtq ljka enzv rboa'; // YOUR GMAIL APP PASSWORD or SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Optional: for local development if you have SSL issues
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ];

                //Recipients
                $mail->setFrom('fatima.a.zaghlol@gmail.com', 'Your Website Name'); // From Email & Name
                $mail->addAddress($username_email, $name); // To Email & Name

                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email Address';
                $mail->Body    = "<p>Hello " . htmlspecialchars($name) . ",</p>" .
                    "<p>Thank you for registering. Your verification code is: <strong>" . $verification_code . "</strong></p>" .
                    "<p>Please enter this code on the verification page to complete your registration.</p>";
                $mail->AltBody = 'Your verification code is: ' . $verification_code;

                $mail->send();

                // Redirect to verification page
                header('Location: verify_account.php');
                exit();

            } catch (Exception $e) {
                $message[] = "Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                // Log this error for yourself, don't show detailed Mailer Error to user in production
            }
        }
    }
}
?>
    <!DOCTYPE html>
    <html xmlns:cellspacing="http://www.w3.org/1999/xhtml">
    <head>
        <title>Signup</title>
        <!-- Font Awesome for eye icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            /* Your CSS styles */
            body{ background-color:#fafafa; }
            ::placeholder{ font-size:13px; background-color:#fafafa; }
            table tr td{ padding-top:10px; padding-bottom:10px; }
            input{ border-radius:4px; border-style:solid; padding:5px; }

            /* Password Toggle Styles */
            .password-container {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            input[type="password"] {
                padding-right: 40px; /* Space for eye icon */
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

            .password-toggle i {
                transition: transform 0.2s ease;
            }

            .password-toggle:hover i {
                transform: scale(1.1);
            }
            table{ margin-top:40px; }
            .logbtn{ background-color: #0095f6; border: none; color: white; padding-top: 10px; padding-bottom: 10px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; width:100%; border-radius:4px; cursor: pointer; }
            .logbtn:hover{ background-color:#0086dd; }
            .signup{ text-decoration:none; }
            .signup:hover{ text-decoration:none; }
            .back{ background-color:white; margin-left:auto; margin-right:auto; max-width:500px; padding: 20px; border: 1px solid #dbdbdb; border-radius: 3px;}
            .error-msg { color: red; font-size: 0.9em; margin-top: 5px; }
            @media (max-width:940px){ .back{ margin-left:20px; margin-right:20px; } }
        </style>
    </head>
    <body style="background:#fafafa;">
    <?php require_once("header.php"); ?>
    <br><br><br>
    <div class="back">
        <h1 class="display-4"align="center">Signup</h1>

        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<p class="error-msg" style="text-align:center;">' . htmlspecialchars($msg) . '</p>';
            }
        }
        ?>

        <form name="signupForm" method="post" enctype="multipart/form-data"> <!-- Changed form name for clarity -->
            <table align="center" style="width:100%;">
                <tr>
                    <td><input type="text" placeholder="Full Name" name="pname" required value="<?php echo isset($_POST['pname']) ? htmlspecialchars($_POST['pname']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td><input type="text" placeholder="Phone Number" name="pnumber" required value="<?php echo isset($_POST['pnumber']) ? htmlspecialchars($_POST['pnumber']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td><input type="email" placeholder="Your Email" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td>
                        <div class="password-container">
                            <input type="password" id="password" placeholder="Password" name="pass" required>
                            <span class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye-slash"></i>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="password-container">
                            <input type="password" id="confirm-password" placeholder="Confirm Password" name="cpass" required>
                            <span class="password-toggle" onclick="togglePassword('confirm-password', this)">
                                <i class="fas fa-eye-slash"></i>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><input type="text" placeholder="Admin Code (Optional)" name="admin_code"></td>
                </tr>
                <tr align="center">
                    <td><input type="submit" class="logbtn" value="Sign up" name="signup"></td>
                </tr>
                <tr align="center">
                    <td><hr align="center" color="#dbdbdb"></td>
                </tr>
                <tr align="center">
                    <td style="font-size:15px;">Have an account? <a href="login.php" class="signup">Log in </a></td>
                </tr>
            </table>
        </form>
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
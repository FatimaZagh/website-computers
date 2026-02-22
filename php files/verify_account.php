<?php
session_start();
ob_start();

require_once("vars.php"); // For dbhost, dbuname, dbpass, dbname

$message = [];

// Check if the user should even be on this page
if (!isset($_SESSION['signup_email']) || !isset($_SESSION['verification_code'])) {
    // If session data is missing, redirect to signup, maybe they landed here directly
    header('Location: signup.php');
    exit();
}

if (isset($_POST['verify_submit'])) {
    $entered_code = trim($_POST['verification_code_input']);

    if (empty($entered_code)) {
        $message[] = "Please enter the verification code.";
    } elseif ($entered_code == $_SESSION['verification_code']) {
        // Codes match - proceed to register the user

        $name = $_SESSION['signup_name'];
        $phone = $_SESSION['signup_phone'];
        $email = $_SESSION['signup_email'];
        $plain_password = $_SESSION['signup_password']; // Get plain password from session
        $usertype = $_SESSION['signup_usertype'];

        // **HASH THE PASSWORD**
        $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());

        $qinsert_sql = "INSERT INTO signup_page (`Name`, `Username`, `Phone Number`, `Password`, `Usertype`) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($connection, $qinsert_sql);
        mysqli_stmt_bind_param($stmt_insert, "sssss", $name, $email, $phone, $hashed_password, $usertype);

        if (mysqli_stmt_execute($stmt_insert)) {
            // Registration successful
            // Clear session variables used for signup
            unset($_SESSION['signup_name']);
            unset($_SESSION['signup_phone']);
            unset($_SESSION['signup_email']);
            unset($_SESSION['signup_password']);
            unset($_SESSION['signup_usertype']);
            unset($_SESSION['verification_code']);

            $_SESSION['success_message'] = "Account verified and registered successfully! Please login.";
            header('Location: login.php'); // Redirect to login page
            exit();
        } else {
            $message[] = "Error registering account: " . mysqli_stmt_error($stmt_insert);
            // In production, log this error and show a generic message to the user
        }
        mysqli_stmt_close($stmt_insert);
        mysqli_close($connection);

    } else {
        $message[] = "Invalid verification code. Please try again.";
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Verify Account</title>
        <style>
            /* You can reuse styles from signup.php or create new ones */
            body{ background-color:#fafafa; font-family: Arial, sans-serif; }
            .back{ background-color:white; margin-left:auto; margin-right:auto; max-width:400px; padding: 20px; border: 1px solid #dbdbdb; border-radius: 3px; margin-top: 50px; text-align: center;}
            input[type="text"], input[type="number"] { width: 80%; padding: 10px; margin-bottom: 15px; border: 1px solid #dbdbdb; border-radius: 3px; }
            .logbtn{ background-color: #0095f6; border: none; color: white; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; border-radius:4px; cursor: pointer; }
            .logbtn:hover{ background-color:#0086dd; }
            .error-msg { color: red; font-size: 0.9em; margin-bottom: 10px; }
            .info-msg { color: #555; margin-bottom: 15px; }
        </style>
    </head>
    <body>
    <?php // You might want to include your header.php here if it's generic enough
    // require_once("header.php");
    ?>
    <div class="back">
        <h2>Verify Your Email</h2>
        <p class="info-msg">A 6-digit verification code has been sent to <strong><?php echo htmlspecialchars($_SESSION['signup_email']); ?></strong>. Please enter it below.</p>

        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<p class="error-msg">' . htmlspecialchars($msg) . '</p>';
            }
        }
        ?>

        <form method="post" action="verify_account.php">
            <input type="number" name="verification_code_input" placeholder="Enter 6-digit code" maxlength="6" required><br>
            <input type="submit" name="verify_submit" value="Verify & Register" class="logbtn">
        </form>
        <p style="margin-top: 20px; font-size:0.9em;">Didn't receive the code? <a href="signup.php">Go back and try signing up again</a>.</p>
        <!-- For a real app, you'd have a "Resend Code" feature here -->
    </div>
    <?php // You might want to include your footer.php here
    // require_once("footer.php");
    ?>
    </body>
    </html>
<?php ob_end_flush(); ?>
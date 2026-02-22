<?php
session_start();
ob_start(); // Start output buffering

// --- PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; // Note: SMTP class is not explicitly used here but good to have if debugging

// Assuming PHPMailer folder is directly in your project root
require 'PHPMailer/PHPMailer.php'; // Adjust path if needed
require 'PHPMailer/SMTP.php';       // Adjust path if needed
require 'PHPMailer/Exception.php';  // Adjust path if needed
// -----------------     // Corrected path for PHPMailer v6+
// -----------------

require_once("vars.php"); // For database credentials
require_once("extfiles.php");
require_once("header.php");

$feedback_message_display = ""; // For success/error messages on the page
$errors = [];

if (isset($_POST["submit_feedback_btn"])) {
    // Sanitize and validate inputs
    $name = isset($_POST["person_name"]) ? trim($_POST["person_name"]) : '';
    $email_from_user = isset($_POST["person_email"]) ? trim($_POST["person_email"]) : '';
    $subject_from_user = isset($_POST["person_subject"]) ? trim($_POST["person_subject"]) : '';
    $message_content = isset($_POST["person_message"]) ? trim($_POST["person_message"]) : '';

    if (empty($name)) $errors[] = "Your Name is required.";
    if (empty($email_from_user) || !filter_var($email_from_user, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email is required.";
    if (empty($subject_from_user)) $errors[] = "Subject is required.";
    if (empty($message_content)) $errors[] = "Message cannot be empty.";

    if (empty($errors)) {
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

        // Prepare statement for database insertion
        $stmt = mysqli_prepare($connection, "INSERT INTO feedback_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email_from_user, $subject_from_user, $message_content);

        if (mysqli_stmt_execute($stmt)) {
            $feedback_message_display = "<div class='alert alert-success text-center'>Thank you, " . htmlspecialchars($name) . "! Your feedback has been submitted successfully. An email confirmation has been sent to you.</div>";

            // --- SEND CONFIRMATION EMAIL TO USER with PHPMailer (using your working config) ---
            $mail = new PHPMailer(true); // Passing `true` enables exceptions

            try {
                //Server settings (Copied from your working signup code)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fatima.a.zaghlol@gmail.com'; // YOUR GMAIL or SMTP username
                $mail->Password   = 'hgtq ljka enzv rboa';       // YOUR GMAIL APP PASSWORD or SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Optional: for local development if you have SSL issues (from your signup code)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ];

                //Recipients
                $mail->setFrom('fatima.a.zaghlol@gmail.com', 'Computer Garage Support'); // From Email & Name (use your actual from)
                $mail->addAddress($email_from_user, $name);     // Add a recipient (user's email and name)
                $mail->addReplyTo('fatima.a.zaghlol@gmail.com', 'Computer Garage Support'); // Your support reply-to (use your actual support email)

                //Content
                $mail->isHTML(false); // Set to true if you want to send HTML email
                $mail->Subject = "Your Feedback Submission - Computer Garage";

                $email_body_user = "Dear " . htmlspecialchars($name) . ",\n\n";
                $email_body_user .= "Thank you for contacting Computer Garage and submitting your feedback.\n";
                $email_body_user .= "We have successfully received your message regarding: \"" . htmlspecialchars($subject_from_user) . "\".\n\n";
                $email_body_user .= "Our team will review your feedback and get back to you as soon as possible if a response is required.\n\n";
                $email_body_user .= "Here's a copy of your message:\n";
                $email_body_user .= "---------------------------------------\n";
                $email_body_user .= htmlspecialchars($message_content) . "\n";
                $email_body_user .= "---------------------------------------\n\n";
                $email_body_user .= "Sincerely,\n";
                $email_body_user .= "The Computer Garage Team\n";
                $email_body_user .= "https://www.facebook.com/fatima.zaghlol.2025/";

                $mail->Body    = $email_body_user;

                $mail->send();
                // Email sent successfully
            } catch (Exception $e) {
                error_log("PHPMailer Error (Contact Us): Message could not be sent to {$email_from_user}. Mailer Error: {$mail->ErrorInfo}");
                $feedback_message_display .= "<br><small class='text-muted'>Note: We received your feedback, but there was an issue sending the confirmation email.</small>";
            }
            // --- END SEND CONFIRMATION EMAIL ---

            $_POST = array(); // Clear POST data

        } else {
            $feedback_message_display = "<div class='alert alert-danger text-center'>Error: Could not submit your feedback to the database. DB Error: " . mysqli_stmt_error($stmt) . "</div>";
        }
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
    } else {
        $feedback_message_display = "<div class='alert alert-danger text-center'><strong>Please correct the following errors:</strong><ul>";
        foreach($errors as $error) {
            $feedback_message_display .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $feedback_message_display .= "</ul></div>";
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Us - Computer Garage</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <?php // extfiles.php already included ?>
        <style>
            body {
                font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #0a0a0f;
                color: #ffffff;
                line-height: 1.6;
                margin: 0;
                padding-top: 100px;
                overflow-x: hidden;
            }

            /* Hero Section */
            .contact-hero {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                position: relative;
                padding: 120px 0;
                text-align: center;
                overflow: hidden;
            }

            .contact-hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 30% 20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                            radial-gradient(circle at 80% 80%, rgba(255, 119, 198, 0.2) 0%, transparent 50%);
                pointer-events: none;
            }

            .contact-hero h1 {
                font-size: 4rem;
                font-weight: 800;
                margin-bottom: 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                position: relative;
                z-index: 2;
            }

            .contact-hero p {
                font-size: 1.3rem;
                max-width: 700px;
                margin: 0 auto;
                opacity: 0.8;
                position: relative;
                z-index: 2;
                font-weight: 300;
            }

            /* Main Contact Section */
            .contact-section {
                padding: 100px 0;
                background: #0a0a0f;
                position: relative;
            }

            /* Contact Form Container */
            .contact-form-container {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 25px;
                padding: 60px 50px;
                position: relative;
                overflow: hidden;
                max-width: 600px;
                margin: 0 auto;
            }

            .contact-form-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                opacity: 0.5;
                pointer-events: none;
            }

            .contact-form-container > * {
                position: relative;
                z-index: 2;
            }

            /* Form Styling */
            .form-group {
                margin-bottom: 25px;
            }

            .form-label {
                color: rgba(255, 255, 255, 0.8);
                font-weight: 600;
                margin-bottom: 8px;
                display: block;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-control {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                padding: 15px 20px;
                color: #ffffff;
                font-size: 1rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .form-control:focus {
                background: rgba(255, 255, 255, 0.12);
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
                outline: none;
                color: #ffffff;
            }

            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            textarea.form-control {
                resize: vertical;
                min-height: 120px;
            }

            /* Submit Button */
            .submit-btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 50px;
                padding: 18px 40px;
                color: white;
                font-weight: 700;
                font-size: 1.1rem;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 100%;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .submit-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
            }

            /* Section Title */
            .section-title {
                text-align: center;
                font-size: 3rem;
                font-weight: 800;
                margin-bottom: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Team Section */
            .team-section {
                background: #0a0a0f;
                padding: 100px 0;
                position: relative;
            }

            .team-card {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 40px 30px;
                text-align: center;
                transition: all 0.4s ease;
                position: relative;
                overflow: hidden;
                height: 100%;
            }

            .team-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                opacity: 0;
                transition: opacity 0.4s ease;
            }

            .team-card:hover::before {
                opacity: 1;
            }

            .team-card:hover {
                transform: translateY(-10px);
                border-color: rgba(102, 126, 234, 0.3);
                box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
            }

            .team-card img {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                margin: 0 auto 25px;
                border: 4px solid rgba(102, 126, 234, 0.3);
                position: relative;
                z-index: 2;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            }

            .team-card h3 {
                color: #ffffff;
                font-weight: 700;
                margin: 20px 0 10px;
                position: relative;
                z-index: 2;
                font-size: 1.4rem;
            }

            .team-card .title {
                color: #667eea !important;
                font-weight: 600;
                font-size: 1rem;
                margin-bottom: 10px;
                position: relative;
                z-index: 2;
            }

            .team-card p {
                color: rgba(255, 255, 255, 0.7);
                position: relative;
                z-index: 2;
                margin-bottom: 15px;
            }

            .team-card .button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 25px;
                padding: 12px 30px;
                color: white !important;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
                position: relative;
                z-index: 2;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }

            .team-card .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
                text-decoration: none;
                color: white !important;
            }

            /* Alert Styling */
            .alert {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 15px;
                color: #ffffff;
                margin-bottom: 30px;
            }

            .alert-success {
                border-color: rgba(40, 167, 69, 0.5);
                background: rgba(40, 167, 69, 0.1);
            }

            .alert-danger {
                border-color: rgba(220, 53, 69, 0.5);
                background: rgba(220, 53, 69, 0.1);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .contact-hero h1 {
                    font-size: 2.5rem;
                }

                .contact-hero p {
                    font-size: 1.1rem;
                }

                .contact-form-container {
                    padding: 40px 30px;
                    margin: 0 20px;
                }

                .section-title {
                    font-size: 2.2rem;
                }
            }

            @media (max-width: 480px) {
                .contact-hero {
                    padding: 80px 0;
                }

                .contact-hero h1 {
                    font-size: 2rem;
                }

                .contact-section, .team-section {
                    padding: 60px 0;
                }

                .contact-form-container {
                    padding: 30px 20px;
                }
            }
        </style>
    </head>
    <body>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Get in touch with our team. We're here to help you with any questions or feedback you may have.</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-section">
        <div class="container">
            <h2 class="section-title">Let's Get In Touch</h2>

            <?php if (!empty($feedback_message_display)) echo $feedback_message_display; ?>

            <div class="contact-form-container">
                <form name="feedback_form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" placeholder="Your Full Name" name="person_name" class="form-control" value="<?php echo isset($_POST['person_name']) ? htmlspecialchars($_POST['person_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" placeholder="Your Email Address" name="person_email" class="form-control" value="<?php echo isset($_POST['person_email']) ? htmlspecialchars($_POST['person_email']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" placeholder="Subject" name="person_subject" class="form-control" value="<?php echo isset($_POST['person_subject']) ? htmlspecialchars($_POST['person_subject']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea placeholder="Your Message" name="person_message" class="form-control" required><?php echo isset($_POST['person_message']) ? htmlspecialchars($_POST['person_message']) : ''; ?></textarea>
                    </div>

                    <button type="submit" name="submit_feedback_btn" class="submit-btn">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="row justify-content-center">
                <!-- Team Member 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <img src="uploads/girl.jpg" alt="Fatima Zaghlol">
                        <h3>Fatima Zaghlol</h3>
                        <p class="title">CEO & Founder</p>
                        <p>Palestine / Nablus</p>
                        <a class="button" href="https://www.facebook.com/fatima.zaghlol.2025/">Contact</a>
                    </div>
                </div>
                <!-- Team Member 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <img src="uploads/boy.jpg" alt="Mohammed Thaher">
                        <h3>Mohammed Thaher</h3>
                        <p class="title">Engineer</p>
                        <p>Palestine / Nablus</p>
                        <a class="button" href="https://x.com/MB_Thaher">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Viewed Products Section -->
    <div id="recently-viewed-section" class="recently-viewed-strip" style="display: none;">
        <div class="container">
            <h4 class="strip-title">
                <span><i class="fas fa-history"></i> Recently Viewed</span>
                <button class="clear-all-btn" onclick="recentlyViewedManager.clearRecentlyViewed()">Clear All</button>
            </h4>
            <div class="products-scroll-container">
                <div id="recently-viewed-products" class="products-scroll">
                    <!-- Products will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br>
    <?php require_once("footer.php"); ?>

    <!-- Recently Viewed Products JavaScript -->
    <script src="js/recently-viewed.js"></script>
    </body>
    </html>
<?php ob_end_flush(); ?>
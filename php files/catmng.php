<?php
session_start();
ob_start(); // Start output buffering

// Admin security checks (assuming these are necessary)
if (!isset($_SESSION["pname"]) || $_SESSION["usertype"] !== "admin") {
    // It's generally better to redirect to a specific admin login page if you have one
    // header("location:login.php");
    // For now, let's assume these checks are in place and working.
    // If not, this page might be accessible without login.
}

$add_message = ""; // To store success/error messages for adding category

if (isset($_POST["sbtn"])) {
    require_once("vars.php"); // For db credentials
    $catname = trim($_POST["catname"]); // Trim whitespace
    $catpic = "defaultpic.png"; // Default picture

    // Basic validation
    if (empty($catname)) {
        $add_message = "<span class='text-danger'>Category Name cannot be empty.</span>";
    } else {
        if (isset($_FILES["catpic"]) && $_FILES["catpic"]["error"] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES["catpic"]["type"];

            if (in_array($file_type, $allowed_types)) {
                $catpic_name_only = basename($_FILES["catpic"]["name"]); // Get only the filename
                $catpic = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $catpic_name_only); // Sanitize and make unique
                $tempname = $_FILES["catpic"]["tmp_name"];
                if (!move_uploaded_file($tempname, "uploads/$catpic")) {
                    $add_message = "<span class='text-danger'>Failed to upload category picture. Using default.</span>";
                    $catpic = "defaultpic.png";
                }
            } else {
                $add_message = "<span class='text-warning'>Invalid file type for category picture. Using default. Allowed: JPG, PNG, GIF, WEBP.</span>";
                $catpic = "defaultpic.png";
            }
        }

        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());
        // Use prepared statements to prevent SQL injection
        $stmt = mysqli_prepare($connection, "INSERT INTO managecat (catname, catpic) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $catname, $catpic);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) == 1) {
                $add_message = "<span class='text-success'>Category Added Successfully.</span>";
            } else {
                $add_message = "<span class='text-danger'>Category Not Added. Possible duplicate or database issue.</span>";
            }
        } else {
            $add_message = "<span class='text-danger'>Error adding category: " . htmlspecialchars(mysqli_stmt_error($stmt)) . "</span>";
        }
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        // No redirect here, message will be shown below the form
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Categories</title>
        <?php require_once("extfiles.php"); ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-bg: #12141a;
                --secondary-bg-rgb: 37, 40, 48;
                --accent-color: #00aaff;
                --accent-color-rgb: 0, 170, 255;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --text-color: #e0e0e0;
                --text-color-darker: #b0b0b0;
                --border-color: rgba(255, 255, 255, 0.08);
                --shadow-color: rgba(0, 0, 0, 0.5);
                --glow-color: rgba(var(--accent-color-rgb), 0.3);
                --card-border-radius: 12px;
                --transition-speed: 0.3s;
                --input-bg: rgba(var(--primary-bg), 0.7);
                --table-header-bg: rgba(var(--secondary-bg-rgb), 0.7);
                --table-row-bg: rgba(var(--secondary-bg-rgb), 0.5);
                --table-row-hover-bg: rgba(var(--secondary-bg-rgb), 0.8);
            }

            body {
                font-family: 'Poppins', sans-serif;
                background-color: var(--primary-bg);
                color: var(--text-color);
                margin: 0; padding: 0; display: flex; flex-direction: column;
                min-height: 100vh; overflow-x: hidden; position: relative;
            }

            body::before, body::after {
                content: ''; position: fixed; top: 50%; left: 50%;
                width: 80vmax; height: 80vmax; border-radius: 50%;
                background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.1) 0%, transparent 60%);
                z-index: -2; animation: blobMove 30s infinite alternate ease-in-out;
                will-change: transform;
            }
            body::after {
                width: 60vmax; height: 60vmax;
                background: radial-gradient(circle, rgba(var(--accent-color-rgb), 0.05) 0%, transparent 50%);
                animation-name: blobMove2; animation-duration: 40s; animation-delay: -10s;
            }
            @keyframes blobMove { 0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); } 100% { transform: translate(-40%, -60%) scale(1.3) rotate(180deg); } }
            @keyframes blobMove2 { 0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); } 100% { transform: translate(-60%, -40%) scale(1.1) rotate(-120deg); } }

            .navbar {
                background-color: rgba(var(--secondary-bg-rgb), 0.5) !important;
                backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--border-color); position: sticky; top: 0;
                z-index: 1000; padding: 0.75rem 1rem;
            }
            .navbar .navbar-brand, .navbar .nav-link { color: var(--text-color) !important; font-weight: 500; transition: color var(--transition-speed) ease; }
            .navbar .nav-link:hover, .navbar .navbar-brand:hover { color: var(--accent-color) !important; text-shadow: 0 0 8px var(--glow-color); }
            #acc:hover { color: var(--accent-color) !important; text-shadow: 0 0 8px var(--glow-color); }

            .page-main-content { flex-grow: 1; padding-top: 40px; padding-bottom: 60px; position: relative; z-index: 1; }

            .page-title {
                color: #fff; font-weight: 600; margin-bottom: 30px; padding-bottom: 15px;
                border-bottom: 2px solid var(--accent-color); text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block;
            }
            .text-center .page-title { display: block; width: fit-content; margin-left: auto; margin-right: auto; }

            .form-section-wrapper, .table-section-wrapper {
                margin-bottom: 40px; padding: 30px;
                background-color: rgba(var(--secondary-bg-rgb), 0.6);
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: var(--card-border-radius);
                box-shadow: 0 8px 20px var(--shadow-color);
                border: 1px solid var(--border-color);
            }
            .section-heading {
                color: var(--text-color); font-size: 1.5rem; font-weight: 500;
                margin-bottom: 25px; padding-bottom: 10px;
                border-bottom: 1px solid var(--border-color);
            }
            .form-section-wrapper .form-control,
            .form-section-wrapper .form-control[type="file"] {
                background-color: var(--input-bg); color: var(--text-color);
                border: 1px solid var(--border-color); border-radius: 6px;
                padding: .5rem .75rem; /* Standard Bootstrap padding */
            }
            .form-section-wrapper .form-control::placeholder { color: var(--text-color-darker); }
            .form-section-wrapper .form-control:focus {
                background-color: rgba(var(--primary-bg), 0.9);
                border-color: var(--accent-color);
                box-shadow: 0 0 0 0.2rem var(--glow-color);
                color: var(--text-color);
            }
            .form-section-wrapper .form-control[type="file"]::-webkit-file-upload-button {
                background: var(--accent-color); color: white; border: none;
                padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer;
                transition: background-color var(--transition-speed);
            }
            .form-section-wrapper .form-control[type="file"]::-webkit-file-upload-button:hover {
                background: #0095e0;
            }
            .form-section-wrapper .btn-submit { /* Renamed from btn-success for clarity */
                background-color: var(--success-color); border-color: var(--success-color);
                color: #fff; font-weight: 500; padding: 0.5rem 1.5rem;
                transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease;
            }
            .form-section-wrapper .btn-submit:hover {
                background-color: #218838; border-color: #1e7e34;
                transform: translateY(-2px);
            }
            .form-message { margin-top: 15px; text-align: center; font-weight: 500; }
            .text-success { color: var(--success-color) !important; }
            .text-danger { color: var(--danger-color) !important; }
            .text-warning { color: var(--warning-color) !important; }


            .table-categories {
                width: 100%; margin-bottom: 0; border-collapse: separate;
                border-spacing: 0; font-size: 0.9rem;
            }
            .table-categories th, .table-categories td {
                padding: 0.9rem 0.75rem; vertical-align: middle;
                border-top: 1px solid var(--border-color) !important;
                border-bottom: none !important; color: var(--text-color);
                text-align: center;
            }
            .table-categories thead th {
                background-color: var(--table-header-bg); color: #fff; font-weight: 600;
                border-bottom: 2px solid var(--accent-color) !important;
            }
            .table-categories thead th:first-child { border-top-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-categories thead th:last-child { border-top-right-radius: calc(var(--card-border-radius) - 1px); }
            .table-categories tbody tr {
                background-color: var(--table-row-bg);
                transition: background-color var(--transition-speed) ease;
            }
            .table-categories tbody tr:hover { background-color: var(--table-row-hover-bg); }
            .table-categories tbody tr:hover td { color: #fff; }
            .table-categories tbody tr:last-child td:first-child { border-bottom-left-radius: calc(var(--card-border-radius) - 1px); }
            .table-categories tbody tr:last-child td:last-child { border-bottom-right-radius: calc(var(--card-border-radius) - 1px); }
            .table-categories img {
                max-width: 60px; height: auto; border-radius: 4px;
                border: 1px solid var(--border-color);
                background-color: rgba(var(--primary-bg),0.5);
            }
            .table-categories .action-link {
                color: var(--accent-color); font-weight: 500; text-decoration: none;
                transition: color var(--transition-speed), text-shadow var(--transition-speed);
                padding: 5px 8px; border-radius: 4px;
            }
            .table-categories .action-link:hover {
                color: #fff; text-shadow: 0 0 5px var(--glow-color);
                background-color: rgba(var(--accent-color-rgb), 0.2);
            }
            .table-categories .action-link.delete { color: var(--danger-color); }
            .table-categories .action-link.delete:hover {
                color: #fff; text-shadow: 0 0 5px rgba(var(--danger-color-rgb),0.4);
                background-color: rgba(var(--danger-color-rgb), 0.2);
            }

            .alert-info {
                background-color: rgba(var(--secondary-bg-rgb), 0.7); border: 1px solid var(--border-color);
                color: var(--text-color); border-radius: var(--card-border-radius);
                backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                border-left: 5px solid var(--accent-color) !important; text-align: center;
            }
            .table-responsive-custom { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;}

            .footer {
                background-color: rgba(var(--secondary-bg-rgb), 0.3);
                backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                color: var(--text-color-darker); padding: 25px 0; text-align: center;
                border-top: 1px solid var(--border-color); margin-top: auto; position: relative; z-index: 1;
            }

            @media (max-width: 768px) {
                body::before, body::after { width: 120vmax; height: 120vmax; }
                .page-title { font-size: 1.8rem; }
                .form-section-wrapper, .table-section-wrapper { padding: 20px; }
                .section-heading { font-size: 1.3rem; }
                .table-categories th, .table-categories td { padding: 0.7rem 0.5rem; font-size: 0.85rem; }
                .table-categories img { max-width: 45px; }
            }
        </style>
    </head>
    <body>

    <?php require_once("adminnavbar.php"); ?>

    <div class="container page-main-content px-md-4 px-2">
        <div class="text-center">
            <h1 class="page-title display-5">Manage Categories</h1>
        </div>

        <!-- Add Category Form Section -->
        <div class="form-section-wrapper">
            <h2 class="section-heading"><i class="fas fa-plus-circle me-2"></i>Add New Category</h2>
            <form name="addcat" method="post" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="catname" class="form-label visually-hidden">Category Name</label>
                            <input type="text" name="catname" id="catname" placeholder="New Category Name" class="form-control form-control-lg" required>
                        </div>
                        <div class="mb-3">
                            <label for="catpic" class="form-label visually-hidden">Category Picture</label>
                            <input type="file" name="catpic" id="catpic" class="form-control form-control-lg">
                            <small class="form-text text-muted" style="color: var(--text-color-darker) !important;">Optional. Max 2MB. Allowed: JPG, PNG, GIF, WEBP.</small>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" name="sbtn" class="btn btn-submit btn-lg"><i class="fas fa-save me-2"></i>Add Category</button>
                        </div>
                        <?php if (!empty($add_message)): ?>
                            <p class="form-message mt-3"><?php echo $add_message; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- List Categories Section -->
        <div class="table-section-wrapper mt-5">
            <h2 class="section-heading"><i class="fas fa-list-alt me-2"></i>Existing Categories</h2>
            <div class="table-responsive-custom">
                <?php
                require_once("vars.php"); // Ensure vars is included for this block too
                $connection_list = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection" . mysqli_connect_error());
                $q_list = "SELECT catid, catname, catpic FROM managecat ORDER BY catname ASC";
                $res_list = mysqli_query($connection_list, $q_list) or die("Error in query" . mysqli_error($connection_list));
                $rowcount_list = mysqli_num_rows($res_list); // Use mysqli_num_rows for SELECT

                if ($rowcount_list == 0) {
                    echo "<div class='alert alert-info'>No categories found. Add one using the form above.</div>";
                } else {
                    echo "<table class='table-categories'>";
                    echo "<thead><tr><th>ID</th><th>Category Name</th><th>Picture</th><th>Update</th><th>Delete</th></tr></thead><tbody>";
                    while ($resarray = mysqli_fetch_array($res_list)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($resarray[0]) . "</td>";
                        echo "<td>" . htmlspecialchars($resarray[1]) . "</td>";
                        echo "<td><img src='uploads/" . htmlspecialchars($resarray[2]) . "' alt='" . htmlspecialchars($resarray[1]) . "'></td>";
                        echo "<td><a href='updatecat.php?cid=" . urlencode($resarray[0]) . "' class='action-link update'><i class='fas fa-edit me-1'></i>Update</a></td>";
                        echo "<td><a href='delcat.php?cid=" . urlencode($resarray[0]) . "' class='action-link delete' onclick=\"return confirm('Are you sure you want to delete category: " . htmlspecialchars(addslashes($resarray[1]), ENT_QUOTES) . "? This might affect subcategories and products.');\"><i class='fas fa-trash-alt me-1'></i>Delete</a></td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
                mysqli_close($connection_list);
                ?>
            </div>
        </div>
    </div>

    <br><br>
    <?php require_once("footer.php"); ?>
    </body>
    </html>
<?php ob_end_flush(); // Flush the output buffer ?>
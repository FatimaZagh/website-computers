<?php
session_start();
ob_start();

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

$message = "";
$message_type = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());

    if (isset($_POST['add_code'])) {
        // Add new discount code
        $code = trim($_POST['code']);
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = floatval($_POST['discount_value']);
        $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
        $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
        $active = isset($_POST['active']) ? 1 : 0;

        // Validation
        if (empty($code)) {
            $message = "Discount code cannot be empty.";
            $message_type = "danger";
        } elseif ($discount_value <= 0) {
            $message = "Discount value must be greater than 0.";
            $message_type = "danger";
        } else {
            // Check if code already exists
            $check_q = "SELECT id FROM discount_codes WHERE code = ?";
            $check_stmt = mysqli_prepare($connection, $check_q);
            mysqli_stmt_bind_param($check_stmt, "s", $code);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                $message = "Discount code already exists. Please choose a different code.";
                $message_type = "danger";
            } else {
                // Insert new discount code
                $insert_q = "INSERT INTO discount_codes (code, description, discount_type, discount_value, usage_limit, expiry_date, active, used_count) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
                $insert_stmt = mysqli_prepare($connection, $insert_q);
                mysqli_stmt_bind_param($insert_stmt, "sssdssi", $code, $description, $discount_type, $discount_value, $usage_limit, $expiry_date, $active);

                if (mysqli_stmt_execute($insert_stmt)) {
                    $message = "Discount code added successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error adding discount code: " . mysqli_error($connection);
                    $message_type = "danger";
                }
            }
        }
    } elseif (isset($_POST['toggle_status'])) {
        // Toggle active status
        $id = intval($_POST['code_id']);
        $new_status = intval($_POST['new_status']);

        $update_q = "UPDATE discount_codes SET active = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($connection, $update_q);
        mysqli_stmt_bind_param($update_stmt, "ii", $new_status, $id);

        if (mysqli_stmt_execute($update_stmt)) {
            $status_text = $new_status ? "activated" : "deactivated";
            $message = "Discount code $status_text successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating discount code status.";
            $message_type = "danger";
        }
    } elseif (isset($_POST['delete_code'])) {
        // Delete discount code
        $id = intval($_POST['code_id']);

        $delete_q = "DELETE FROM discount_codes WHERE id = ?";
        $delete_stmt = mysqli_prepare($connection, $delete_q);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);

        if (mysqli_stmt_execute($delete_stmt)) {
            $message = "Discount code deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting discount code.";
            $message_type = "danger";
        }
    }

    mysqli_close($connection);
}

// Get all discount codes for display
$connection = mysqli_connect(dbhost, dbuname, dbpass, dbname) or die("Error in connection: " . mysqli_connect_error());
$codes_q = "SELECT * FROM discount_codes ORDER BY id DESC";
$codes_result = mysqli_query($connection, $codes_q) or die("Error in query: " . mysqli_error($connection));
$discount_codes = [];
while ($row = mysqli_fetch_assoc($codes_result)) {
    $discount_codes[] = $row;
}
mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Discount Codes</title>
    <?php require_once("extfiles.php"); ?>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-bg-rgb: 255, 255, 255;
            --accent-color: #667eea;
            --text-color: #2d3748;
            --text-color-light: #718096;
            --text-color-darker: #1a202c;
            --border-color: rgba(255, 255, 255, 0.2);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --glow-color: rgba(102, 126, 234, 0.4);
            --transition-speed: 0.3s;
            --border-radius: 15px;
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-bg);
            min-height: 100vh;
            color: var(--text-color);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            width: 200vmax;
            height: 200vmax;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            animation: float 20s infinite ease-in-out;
        }

        body::before {
            top: -50%;
            left: -50%;
            animation-delay: 0s;
        }

        body::after {
            bottom: -50%;
            right: -50%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .page-main-content {
            margin-top: 80px;
            padding: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .welcome-card {
            background: rgba(var(--secondary-bg-rgb), 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 40px 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px var(--shadow-color);
            color: white;
        }

        .welcome-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-card .lead-text {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .content-card {
            background: rgba(var(--secondary-bg-rgb), 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px var(--shadow-color);
            transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color-darker);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color-darker);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all var(--transition-speed) ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--glow-color);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all var(--transition-speed) ease;
            background: white;
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--glow-color);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .form-check-input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #e53e3e;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background: #dd6b20;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 0.875rem;
        }

        /* Alert Styling */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: rgba(245, 101, 101, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-info {
            background: rgba(102, 126, 234, 0.1);
            color: var(--accent-color);
            border-left: 4px solid var(--accent-color);
        }

        /* Table Styling */
        .table-wrapper {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 20px var(--shadow-color);
        }

        .table-discount-codes {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table-discount-codes th {
            background: var(--accent-color);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-discount-codes td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-discount-codes tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .table-discount-codes tr:last-child td {
            border-bottom: none;
        }

        /* Status badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success-color);
        }

        .status-inactive {
            background: rgba(245, 101, 101, 0.1);
            color: var(--danger-color);
        }

        .discount-type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-percent {
            background: rgba(237, 137, 54, 0.1);
            color: var(--warning-color);
        }

        .type-fixed {
            background: rgba(102, 126, 234, 0.1);
            color: var(--accent-color);
        }

        /* Responsive grid for form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        /* Action buttons container */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .welcome-card h1 {
                font-size: 2rem;
            }

            .welcome-card {
                padding: 25px 20px;
            }

            .content-card {
                padding: 20px;
            }

            .form-row, .form-row-3 {
                grid-template-columns: 1fr;
            }

            .table-wrapper {
                overflow-x: auto;
            }

            .table-discount-codes {
                min-width: 800px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                margin-bottom: 5px;
            }

            body::before, body::after {
                width: 120vmax;
                height: 120vmax;
            }
        }
    </style>
</head>
<body>

<?php require_once("adminnavbar.php"); ?>

<div class="page-main-content">
    <div class="welcome-card">
        <h1><i class="fas fa-tags me-3"></i>Manage Discount Codes</h1>
        <p class="lead-text">Create, edit, and manage discount codes for your customers</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Add New Discount Code Form -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="fas fa-plus-circle"></i>Add New Discount Code
        </h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="code" class="form-label">Discount Code *</label>
                    <input type="text" id="code" name="code" class="form-control" required
                           placeholder="e.g., SAVE20, WELCOME10" maxlength="50">
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" id="description" name="description" class="form-control"
                           placeholder="Brief description of the discount" maxlength="255">
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="discount_type" class="form-label">Discount Type *</label>
                    <select id="discount_type" name="discount_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="percent">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₪)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discount_value" class="form-label">Discount Value *</label>
                    <input type="number" id="discount_value" name="discount_value" class="form-control"
                           step="0.01" min="0.01" required placeholder="e.g., 20 or 50.00">
                </div>
                <div class="form-group">
                    <label for="usage_limit" class="form-label">Usage Limit</label>
                    <input type="number" id="usage_limit" name="usage_limit" class="form-control"
                           min="1" placeholder="Leave empty for unlimited">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                           min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="active" name="active" class="form-check-input" checked>
                        <label for="active" class="form-label">Active (Enable this discount code)</label>
                    </div>
                </div>
            </div>

            <button type="submit" name="add_code" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Discount Code
            </button>
        </form>
    </div>

    <!-- Existing Discount Codes -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="fas fa-list"></i>Existing Discount Codes
        </h2>

        <?php if (empty($discount_codes)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No discount codes found. Create your first discount code using the form above.
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table-discount-codes">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($discount_codes as $code): ?>
                            <tr>
                                <td>
                                    <strong style="font-family: monospace; font-size: 1.1em; color: var(--accent-color);">
                                        <?php echo htmlspecialchars($code['code']); ?>
                                    </strong>
                                </td>
                                <td><?php echo htmlspecialchars($code['description'] ?: 'No description'); ?></td>
                                <td>
                                    <span class="discount-type-badge <?php echo $code['discount_type'] === 'percent' ? 'type-percent' : 'type-fixed'; ?>">
                                        <?php echo $code['discount_type'] === 'percent' ? 'Percentage' : 'Fixed'; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        <?php
                                        if ($code['discount_type'] === 'percent') {
                                            echo number_format($code['discount_value'], 1) . '%';
                                        } else {
                                            echo '₪' . number_format($code['discount_value'], 2);
                                        }
                                        ?>
                                    </strong>
                                </td>
                                <td>
                                    <span style="color: var(--text-color-light);">
                                        <?php echo $code['used_count']; ?>
                                        <?php if ($code['usage_limit']): ?>
                                            / <?php echo $code['usage_limit']; ?>
                                        <?php else: ?>
                                            / ∞
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($code['expiry_date']): ?>
                                        <?php
                                        $expiry = new DateTime($code['expiry_date']);
                                        $today = new DateTime();
                                        $is_expired = $expiry < $today;
                                        ?>
                                        <span style="color: <?php echo $is_expired ? 'var(--danger-color)' : 'var(--text-color)'; ?>">
                                            <?php echo $expiry->format('M d, Y'); ?>
                                            <?php if ($is_expired): ?>
                                                <br><small>(Expired)</small>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-color-light);">No expiry</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $code['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $code['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Toggle Status -->
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="code_id" value="<?php echo $code['id']; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo $code['active'] ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_status"
                                                    class="btn btn-sm <?php echo $code['active'] ? 'btn-warning' : 'btn-success'; ?>"
                                                    title="<?php echo $code['active'] ? 'Deactivate' : 'Activate'; ?>">
                                                <i class="fas <?php echo $code['active'] ? 'fa-pause' : 'fa-play'; ?>"></i>
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form method="POST" style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this discount code? This action cannot be undone.');">
                                            <input type="hidden" name="code_id" value="<?php echo $code['id']; ?>">
                                            <button type="submit" name="delete_code" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: rgba(102, 126, 234, 0.05); border-radius: 10px;">
                <h4 style="color: var(--accent-color); margin-bottom: 10px;">
                    <i class="fas fa-info-circle me-2"></i>Quick Stats
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <strong>Total Codes:</strong> <?php echo count($discount_codes); ?>
                    </div>
                    <div>
                        <strong>Active Codes:</strong>
                        <?php echo count(array_filter($discount_codes, function($c) { return $c['active']; })); ?>
                    </div>
                    <div>
                        <strong>Total Usage:</strong>
                        <?php echo array_sum(array_column($discount_codes, 'used_count')); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Add some JavaScript for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format discount code input to uppercase
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }

    // Update placeholder text based on discount type
    const typeSelect = document.getElementById('discount_type');
    const valueInput = document.getElementById('discount_value');

    if (typeSelect && valueInput) {
        typeSelect.addEventListener('change', function() {
            if (this.value === 'percent') {
                valueInput.placeholder = 'e.g., 20 (for 20%)';
                valueInput.max = '100';
            } else if (this.value === 'fixed') {
                valueInput.placeholder = 'e.g., 50.00 (for ₪50)';
                valueInput.removeAttribute('max');
            }
        });
    }
});
</script>

</body>
</html>

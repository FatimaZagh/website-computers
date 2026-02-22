<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once("vars.php");

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid request.',
    'discounted_price' => 0,
    'discount_value' => 0,
    'discount_amount' => 0,
    'discount_type' => ''
];

// Check if request is POST and has required parameters
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['code']) || !isset($_POST['price'])) {
    echo json_encode($response);
    exit;
}

$code = trim($_POST['code']);
$price = floatval($_POST['price']);

// Validate input
if (empty($code) || $price <= 0) {
    $response['message'] = 'Invalid discount code or price.';
    echo json_encode($response);
    exit;
}

try {
    // Connect to database
    $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
    if (!$connection) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    $today = date("Y-m-d");
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($connection, "
        SELECT id, discount_type, discount_value, usage_limit, used_count, expiry_date, description 
        FROM discount_codes 
        WHERE code = ? AND active = 1 AND (expiry_date IS NULL OR expiry_date >= ?) AND (usage_limit IS NULL OR used_count < usage_limit)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $code, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Valid discount code found
        $discount_type = $row['discount_type'];
        $discount_value = floatval($row['discount_value']);
        
        // Calculate discount amount and final price
        if ($discount_type === 'percent') {
            $discount_amount = $price * ($discount_value / 100);
            $discounted_price = $price - $discount_amount;
        } else { // fixed
            $discount_amount = min($discount_value, $price); // Don't allow discount to exceed price
            $discounted_price = max($price - $discount_amount, 0);
        }
        
        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Discount code applied successfully.',
            'discounted_price' => number_format($discounted_price, 2, '.', ''),
            'discount_value' => $discount_value,
            'discount_amount' => number_format($discount_amount, 2, '.', ''),
            'discount_type' => $discount_type,
            'description' => $row['description'] ?: ''
        ];
        
        // Add usage information for display
        if ($row['usage_limit']) {
            $remaining_uses = $row['usage_limit'] - $row['used_count'];
            $response['usage_info'] = "Uses remaining: $remaining_uses";
        }
        
        if ($row['expiry_date']) {
            $expiry_date = new DateTime($row['expiry_date']);
            $response['expiry_info'] = "Expires: " . $expiry_date->format('M d, Y');
        }
        
    } else {
        // Check if code exists but is inactive/expired/used up
        $check_stmt = mysqli_prepare($connection, "SELECT active, expiry_date, usage_limit, used_count FROM discount_codes WHERE code = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $code);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if ($check_row = mysqli_fetch_assoc($check_result)) {
            // Code exists but has issues
            if (!$check_row['active']) {
                $response['message'] = 'This discount code is currently inactive.';
            } elseif ($check_row['expiry_date'] && $check_row['expiry_date'] < $today) {
                $response['message'] = 'This discount code has expired.';
            } elseif ($check_row['usage_limit'] && $check_row['used_count'] >= $check_row['usage_limit']) {
                $response['message'] = 'This discount code has reached its usage limit.';
            } else {
                $response['message'] = 'This discount code is not valid at this time.';
            }
        } else {
            // Code doesn't exist
            $response['message'] = 'Invalid discount code. Please check and try again.';
        }
    }
    
    mysqli_close($connection);
    
} catch (Exception $e) {
    // Log error (in production, you might want to log this to a file)
    error_log("Discount validation error: " . $e->getMessage());
    $response['message'] = 'An error occurred while validating the discount code. Please try again.';
}

// Return JSON response
echo json_encode($response);
?>

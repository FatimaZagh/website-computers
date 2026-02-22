<?php
session_start();
header('Content-Type: application/json');
include 'conn.php'; // make sure this is the correct path to your DB connection

$code = isset($_POST['code']) ? trim($_POST['code']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

$response = [
    'success' => false,
    'message' => 'Invalid request.',
    'discounted_price' => $price,
    'discount_value' => 0
];

if ($code === '' || $price <= 0) {
    $response['message'] = 'Invalid discount code or price.';
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM discount_codes WHERE code = ? AND active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $usage_limit = $row['usage_limit'];
    $used_count = $row['used_count'];

    if (!is_null($usage_limit) && $used_count >= $usage_limit) {
        $response['message'] = 'This discount code has reached its usage limit.';
    } else {
        $discount_type = $row['discount_type'];
        $discount_value = floatval($row['discount_value']);

        if ($discount_type === 'percent') {
            $discount_amount = $price * ($discount_value / 100);
        } else { // fixed
            $discount_amount = $discount_value;
        }

        $discounted_price = max($price - $discount_amount, 0);

        $_SESSION['discount_code'] = $code;
        $_SESSION['discount_value'] = $discount_value;
        $_SESSION['discount_type'] = $discount_type;

        $response = [
            'success' => true,
            'message' => 'Discount code applied successfully.',
            'discounted_price' => number_format($discounted_price, 2),
            'discount_value' => number_format($discount_amount, 2),
            'discount_type' => $discount_type
        ];
    }
} else {
    $response['message'] = 'Invalid or expired discount code.';
}

echo json_encode($response);
?>

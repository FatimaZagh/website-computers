<?php
/**
 * API Endpoint: Save Recently Viewed Product
 * 
 * This endpoint handles AJAX requests to save a product to the user's
 * recently viewed list in the database.
 * 
 * Method: POST
 * Content-Type: application/json
 * 
 * Required: User must be logged in (session)
 * 
 * Input JSON:
 * {
 *   "product_id": 123,
 *   "product_name": "Product Name",
 *   "product_image": "image.jpg",
 *   "product_price": 299.99,
 *   "product_discount": 10
 * }
 * 
 * Output JSON:
 * {
 *   "success": true/false,
 *   "message": "Success/Error message",
 *   "error": "Error details (if any)"
 * }
 */

// Start session and set headers
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['userprimid']) || empty($_SESSION['userprimid'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in'
    ]);
    exit;
}

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON input: ' . json_last_error_msg()
        ]);
        exit;
    }
    
    // Validate required fields
    $required_fields = ['product_id', 'product_name', 'product_image', 'product_price'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing required fields: ' . implode(', ', $missing_fields)
        ]);
        exit;
    }
    
    // Validate data types
    if (!is_numeric($data['product_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'product_id must be numeric'
        ]);
        exit;
    }
    
    if (!is_numeric($data['product_price'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'product_price must be numeric'
        ]);
        exit;
    }
    
    // Set default discount if not provided
    $discount = isset($data['product_discount']) ? (int)$data['product_discount'] : 0;
    
    // Get user email from session
    $userEmail = $_SESSION['userprimid'];
    
    // Include the recently viewed functions
    require_once('../functions/recently_viewed.php');
    
    // Add product to recently viewed
    $result = addToRecentlyViewed(
        $userEmail,
        (int)$data['product_id'],
        $data['product_name'],
        $data['product_image'],
        (float)$data['product_price'],
        $discount
    );
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Product added to recently viewed successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to save product to recently viewed'
        ]);
    }
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Recently Viewed API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error'
    ]);
}
?>

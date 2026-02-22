<?php
/**
 * API Endpoint: Remove Recently Viewed Product
 * 
 * This endpoint handles AJAX requests to remove a specific product
 * from the user's recently viewed list.
 * 
 * Method: POST
 * Content-Type: application/json
 * 
 * Required: User must be logged in (session)
 * 
 * Input JSON:
 * {
 *   "product_id": 123
 * }
 * 
 * OR for clearing all:
 * {
 *   "clear_all": true
 * }
 * 
 * Output JSON:
 * {
 *   "success": true/false,
 *   "message": "Success message",
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
    
    // Get user email from session
    $userEmail = $_SESSION['userprimid'];
    
    // Include the recently viewed functions
    require_once('../functions/recently_viewed.php');
    
    // Check if this is a clear all request
    if (isset($data['clear_all']) && $data['clear_all'] === true) {
        $result = clearAllRecentlyViewed($userEmail);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'All recently viewed products cleared successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to clear recently viewed products'
            ]);
        }
        exit;
    }
    
    // Check for product_id
    if (!isset($data['product_id']) || $data['product_id'] === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing required field: product_id'
        ]);
        exit;
    }
    
    // Validate product_id
    if (!is_numeric($data['product_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'product_id must be numeric'
        ]);
        exit;
    }
    
    // Remove specific product from recently viewed
    $result = removeFromRecentlyViewed($userEmail, (int)$data['product_id']);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Product removed from recently viewed successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to remove product from recently viewed'
        ]);
    }
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Recently Viewed Remove API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error'
    ]);
}
?>

<?php
/**
 * API Endpoint: Get Recently Viewed Products
 * 
 * This endpoint handles AJAX requests to retrieve the user's
 * recently viewed products from the database.
 * 
 * Method: GET
 * 
 * Required: User must be logged in (session)
 * 
 * Query Parameters:
 * - limit: Number of products to return (default: 10, max: 20)
 * 
 * Output JSON:
 * {
 *   "success": true/false,
 *   "products": [
 *     {
 *       "id": 123,
 *       "name": "Product Name",
 *       "image": "image.jpg",
 *       "price": 299.99,
 *       "discount": 10,
 *       "viewedAt": "2024-01-15 10:30:00"
 *     }
 *   ],
 *   "count": 5,
 *   "error": "Error details (if any)"
 * }
 */

// Start session and set headers
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use GET.'
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['userprimid']) || empty($_SESSION['userprimid'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in',
        'products' => [],
        'count' => 0
    ]);
    exit;
}

try {
    // Get limit parameter from query string
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // Validate limit (max 20 for performance)
    if ($limit < 1) {
        $limit = 10;
    } elseif ($limit > 20) {
        $limit = 20;
    }
    
    // Get user email from session
    $userEmail = $_SESSION['userprimid'];
    
    // Include the recently viewed functions
    require_once('../functions/recently_viewed.php');
    
    // Get recently viewed products
    $products = getRecentlyViewed($userEmail, $limit);
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'user_email' => $userEmail, // For debugging (remove in production)
        'limit' => $limit
    ]);
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Recently Viewed Get API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'products' => [],
        'count' => 0
    ]);
}
?>

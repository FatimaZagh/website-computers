<?php
/**
 * Recently Viewed Products - Database Functions
 * 
 * This file contains functions for managing recently viewed products
 * in the database for logged-in users.
 * 
 * Features:
 * - Add products to recently viewed
 * - Get recently viewed products
 * - Automatic cleanup of old entries
 * - Duplicate prevention
 * - Error handling and fallbacks
 */

/**
 * Add a product to user's recently viewed list
 * 
 * @param string $userEmail User's email (from session)
 * @param int $productId Product ID
 * @param string $productName Product name
 * @param string $productImage Product image filename
 * @param float $productPrice Product price
 * @param int $productDiscount Product discount percentage
 * @return bool Success status
 */
function addToRecentlyViewed($userEmail, $productId, $productName, $productImage, $productPrice, $productDiscount = 0) {
    // Validate inputs
    if (empty($userEmail) || empty($productId) || empty($productName)) {
        error_log("Recently Viewed: Invalid input parameters");
        return false;
    }
    
    try {
        require_once(__DIR__ . "/../vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
        
        if (!$connection) {
            error_log("Recently Viewed: Database connection failed - " . mysqli_connect_error());
            return false;
        }
        
        // Sanitize inputs
        $userEmail = mysqli_real_escape_string($connection, $userEmail);
        $productId = (int)$productId;
        $productName = mysqli_real_escape_string($connection, $productName);
        $productImage = mysqli_real_escape_string($connection, $productImage);
        $productPrice = (float)$productPrice;
        $productDiscount = (int)$productDiscount;
        
        // Use INSERT ... ON DUPLICATE KEY UPDATE to handle duplicates
        // This will update the viewed_at timestamp if the product already exists
        $query = "INSERT INTO user_recently_viewed 
                  (user_email, product_id, product_name, product_image, product_price, product_discount, viewed_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                  ON DUPLICATE KEY UPDATE 
                  viewed_at = NOW(),
                  product_name = VALUES(product_name),
                  product_image = VALUES(product_image),
                  product_price = VALUES(product_price),
                  product_discount = VALUES(product_discount)";
        
        $stmt = mysqli_prepare($connection, $query);
        
        if (!$stmt) {
            error_log("Recently Viewed: Prepare statement failed - " . mysqli_error($connection));
            mysqli_close($connection);
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "sissdi", 
            $userEmail, $productId, $productName, $productImage, $productPrice, $productDiscount);
        
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            error_log("Recently Viewed: Execute failed - " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            mysqli_close($connection);
            return false;
        }
        
        mysqli_stmt_close($stmt);
        
        // Clean up old entries for this user (keep only last 15)
        cleanupOldEntries($userEmail, $connection);
        
        mysqli_close($connection);
        return true;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in addToRecentlyViewed - " . $e->getMessage());
        return false;
    }
}

/**
 * Get recently viewed products for a user
 * 
 * @param string $userEmail User's email
 * @param int $limit Maximum number of products to return
 * @return array Array of recently viewed products
 */
function getRecentlyViewed($userEmail, $limit = 10) {
    // Validate inputs
    if (empty($userEmail)) {
        return [];
    }
    
    try {
        require_once(__DIR__ . "/../vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
        
        if (!$connection) {
            error_log("Recently Viewed: Database connection failed - " . mysqli_connect_error());
            return [];
        }
        
        $userEmail = mysqli_real_escape_string($connection, $userEmail);
        $limit = (int)$limit;
        
        $query = "SELECT product_id, product_name, product_image, product_price, product_discount, viewed_at 
                  FROM user_recently_viewed 
                  WHERE user_email = ? 
                  ORDER BY viewed_at DESC 
                  LIMIT ?";
        
        $stmt = mysqli_prepare($connection, $query);
        
        if (!$stmt) {
            error_log("Recently Viewed: Prepare statement failed - " . mysqli_error($connection));
            mysqli_close($connection);
            return [];
        }
        
        mysqli_stmt_bind_param($stmt, "si", $userEmail, $limit);
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            error_log("Recently Viewed: Execute failed - " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            mysqli_close($connection);
            return [];
        }
        
        $resultSet = mysqli_stmt_get_result($stmt);
        $products = [];
        
        while ($row = mysqli_fetch_assoc($resultSet)) {
            $products[] = [
                'id' => $row['product_id'],
                'name' => $row['product_name'],
                'image' => $row['product_image'],
                'price' => (float)$row['product_price'],
                'discount' => (int)$row['product_discount'],
                'viewedAt' => $row['viewed_at']
            ];
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        
        return $products;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in getRecentlyViewed - " . $e->getMessage());
        return [];
    }
}

/**
 * Remove a specific product from user's recently viewed list
 * 
 * @param string $userEmail User's email
 * @param int $productId Product ID to remove
 * @return bool Success status
 */
function removeFromRecentlyViewed($userEmail, $productId) {
    if (empty($userEmail) || empty($productId)) {
        return false;
    }
    
    try {
        require_once(__DIR__ . "/../vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
        
        if (!$connection) {
            return false;
        }
        
        $query = "DELETE FROM user_recently_viewed WHERE user_email = ? AND product_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        
        if (!$stmt) {
            mysqli_close($connection);
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "si", $userEmail, $productId);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in removeFromRecentlyViewed - " . $e->getMessage());
        return false;
    }
}

/**
 * Clear all recently viewed products for a user
 * 
 * @param string $userEmail User's email
 * @return bool Success status
 */
function clearAllRecentlyViewed($userEmail) {
    if (empty($userEmail)) {
        return false;
    }
    
    try {
        require_once(__DIR__ . "/../vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
        
        if (!$connection) {
            return false;
        }
        
        $query = "DELETE FROM user_recently_viewed WHERE user_email = ?";
        $stmt = mysqli_prepare($connection, $query);
        
        if (!$stmt) {
            mysqli_close($connection);
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in clearAllRecentlyViewed - " . $e->getMessage());
        return false;
    }
}

/**
 * Clean up old entries for a user (keep only the most recent ones)
 * 
 * @param string $userEmail User's email
 * @param resource $connection Database connection (optional)
 * @param int $keepCount Number of entries to keep
 * @return bool Success status
 */
function cleanupOldEntries($userEmail, $connection = null, $keepCount = 15) {
    $shouldCloseConnection = false;
    
    try {
        if ($connection === null) {
            require_once(__DIR__ . "/../vars.php");
            $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
            $shouldCloseConnection = true;
            
            if (!$connection) {
                return false;
            }
        }
        
        // Delete entries beyond the keep count
        $cleanup_query = "DELETE FROM user_recently_viewed 
                          WHERE user_email = ? 
                          AND id NOT IN (
                              SELECT id FROM (
                                  SELECT id FROM user_recently_viewed 
                                  WHERE user_email = ? 
                                  ORDER BY viewed_at DESC 
                                  LIMIT ?
                              ) AS recent
                          )";
        
        $stmt = mysqli_prepare($connection, $cleanup_query);
        
        if (!$stmt) {
            if ($shouldCloseConnection) mysqli_close($connection);
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "ssi", $userEmail, $userEmail, $keepCount);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        
        if ($shouldCloseConnection) {
            mysqli_close($connection);
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in cleanupOldEntries - " . $e->getMessage());
        if ($shouldCloseConnection && $connection) {
            mysqli_close($connection);
        }
        return false;
    }
}

/**
 * Check if the recently viewed table exists and create it if it doesn't
 * 
 * @return bool Success status
 */
function ensureRecentlyViewedTableExists() {
    try {
        require_once(__DIR__ . "/../vars.php");
        $connection = mysqli_connect(dbhost, dbuname, dbpass, dbname);
        
        if (!$connection) {
            return false;
        }
        
        // Check if table exists
        $check_query = "SHOW TABLES LIKE 'user_recently_viewed'";
        $result = mysqli_query($connection, $check_query);
        
        if (mysqli_num_rows($result) == 0) {
            // Table doesn't exist, create it
            $create_query = "CREATE TABLE user_recently_viewed (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_email VARCHAR(100) NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(500) NOT NULL,
                product_image VARCHAR(500) NOT NULL,
                product_price DECIMAL(10,2) NOT NULL,
                product_discount INT DEFAULT 0,
                viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_email (user_email),
                INDEX idx_product_id (product_id),
                INDEX idx_viewed_at (viewed_at),
                UNIQUE KEY unique_user_product (user_email, product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            $create_result = mysqli_query($connection, $create_query);
            mysqli_close($connection);
            
            return $create_result;
        }
        
        mysqli_close($connection);
        return true;
        
    } catch (Exception $e) {
        error_log("Recently Viewed: Exception in ensureRecentlyViewedTableExists - " . $e->getMessage());
        return false;
    }
}

// Auto-create table if it doesn't exist when this file is included
ensureRecentlyViewedTableExists();

?>

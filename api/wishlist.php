<?php
/**
 * Wishlist API
 * Handles wishlist operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/models/Wishlist.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'get';

$wishlistModel = new Wishlist();

try {
    // All wishlist actions require authentication
    if (!isset($_SESSION['user_id'])) {
        errorResponse('Authentication required', [], 401);
    }
    
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'get':
            $items = $wishlistModel->getItems($userId);
            
            successResponse([
                'items' => $items,
                'count' => count($items),
            ]);
            break;
            
        case 'add':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['product_id'])) {
                errorResponse('Product ID is required', [], 400);
            }
            
            $wishlistModel->add($userId, (int)$data['product_id']);
            
            $items = $wishlistModel->getItems($userId);
            
            successResponse([
                'items' => $items,
                'count' => count($items),
            ], 'Product added to wishlist');
            break;
            
        case 'remove':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['product_id'])) {
                errorResponse('Product ID is required', [], 400);
            }
            
            $wishlistModel->remove($userId, (int)$data['product_id']);
            
            $items = $wishlistModel->getItems($userId);
            
            successResponse([
                'items' => $items,
                'count' => count($items),
            ], 'Product removed from wishlist');
            break;
            
        case 'check':
            $productId = $_GET['product_id'] ?? null;
            
            if (!$productId) {
                errorResponse('Product ID is required', [], 400);
            }
            
            $isInWishlist = $wishlistModel->isInWishlist($userId, (int)$productId);
            
            successResponse([
                'in_wishlist' => $isInWishlist,
            ]);
            break;
            
        case 'count':
            $count = $wishlistModel->getCount($userId);
            
            successResponse(['count' => $count]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

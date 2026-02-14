<?php
/**
 * Cart API
 * Handles shopping cart operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/models/Cart.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'get';

$cartModel = new Cart();

// Get session ID for guest users
if (!isset($_SESSION['cart_id'])) {
    $_SESSION['cart_id'] = session_id();
}

$userId = $_SESSION['user_id'] ?? null;
$sessionId = $_SESSION['cart_id'];

try {
    switch ($action) {
        case 'get':
            $items = $cartModel->getItems($userId, $sessionId);
            $totals = $cartModel->calculateTotals($userId, $sessionId);
            
            successResponse([
                'items' => $items,
                'totals' => $totals,
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
            
            $cartData = [
                'product_id' => (int)$data['product_id'],
                'variant_id' => !empty($data['variant_id']) ? (int)$data['variant_id'] : null,
                'quantity' => (int)($data['quantity'] ?? 1),
            ];
            
            if ($userId) {
                $cartData['user_id'] = $userId;
            } else {
                $cartData['session_id'] = $sessionId;
            }
            
            $cartModel->addItem($cartData);
            
            $items = $cartModel->getItems($userId, $sessionId);
            $totals = $cartModel->calculateTotals($userId, $sessionId);
            
            successResponse([
                'items' => $items,
                'totals' => $totals,
            ], 'Item added to cart');
            break;
            
        case 'update':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id']) || empty($data['quantity'])) {
                errorResponse('Cart item ID and quantity are required', [], 400);
            }
            
            $cartModel->updateQuantity((int)$data['id'], (int)$data['quantity'], $userId, $sessionId);
            
            $items = $cartModel->getItems($userId, $sessionId);
            $totals = $cartModel->calculateTotals($userId, $sessionId);
            
            successResponse([
                'items' => $items,
                'totals' => $totals,
            ], 'Cart updated');
            break;
            
        case 'remove':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id'])) {
                errorResponse('Cart item ID is required', [], 400);
            }
            
            $cartModel->removeItem((int)$data['id'], $userId, $sessionId);
            
            $items = $cartModel->getItems($userId, $sessionId);
            $totals = $cartModel->calculateTotals($userId, $sessionId);
            
            successResponse([
                'items' => $items,
                'totals' => $totals,
            ], 'Item removed from cart');
            break;
            
        case 'clear':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $cartModel->clear($userId, $sessionId);
            
            successResponse([], 'Cart cleared');
            break;
            
        case 'count':
            $count = $cartModel->getCount($userId, $sessionId);
            
            successResponse(['count' => $count]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

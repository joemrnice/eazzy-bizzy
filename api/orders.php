<?php
/**
 * Orders API
 * Handles order operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Cart.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Coupon.php';
require_once __DIR__ . '/../app/models/Database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$orderModel = new Order();

try {
    switch ($action) {
        case 'list':
            if (!isset($_SESSION['user_id'])) {
                errorResponse('Authentication required', [], 401);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 10);
            
            $orders = $orderModel->getUserOrders($_SESSION['user_id'], $page, $perPage);
            $total = $orderModel->countUserOrders($_SESSION['user_id']);
            $pagination = paginate($total, $perPage, $page);
            
            successResponse([
                'orders' => $orders,
                'pagination' => $pagination,
            ]);
            break;
            
        case 'detail':
            if (!isset($_SESSION['user_id'])) {
                errorResponse('Authentication required', [], 401);
            }
            
            $orderNumber = $_GET['order_number'] ?? null;
            
            if (!$orderNumber) {
                errorResponse('Order number is required', [], 400);
            }
            
            $order = $orderModel->findByOrderNumber($orderNumber);
            
            if (!$order) {
                errorResponse('Order not found', [], 404);
            }
            
            // Verify ownership
            if ($order['user_id'] != $_SESSION['user_id']) {
                errorResponse('Access denied', [], 403);
            }
            
            $order['items'] = $orderModel->getItems($order['id']);
            
            successResponse(['order' => $order]);
            break;
            
        case 'place':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['email', 'payment_method'];
            $errors = validateRequired($data, $required);
            
            if (!empty($errors)) {
                errorResponse('Validation failed', $errors, 422);
            }
            
            // Validate email
            if (!validateEmail($data['email'])) {
                errorResponse('Invalid email address', [], 422);
            }
            
            // Get cart items
            $cartModel = new Cart();
            $userId = $_SESSION['user_id'] ?? null;
            $sessionId = $_SESSION['cart_id'] ?? null;
            
            $cartItems = $cartModel->getItems($userId, $sessionId);
            
            if (empty($cartItems)) {
                errorResponse('Cart is empty', [], 400);
            }
            
            // Validate stock availability and calculate totals
            $productModel = new Product();
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($cartItems as $item) {
                $product = $productModel->findById($item['product_id']);
                
                if (!$product || !$product['is_active']) {
                    errorResponse("Product '{$item['name']}' is no longer available", [], 400);
                }
                
                if ($product['stock'] < $item['quantity']) {
                    errorResponse("Insufficient stock for '{$item['name']}'", [], 400);
                }
                
                $price = $item['variant_price'] ?? $item['sale_price'] ?? $item['price'];
                $itemTotal = $price * $item['quantity'];
                $subtotal += $itemTotal;
                
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemTotal,
                ];
            }
            
            // Apply coupon if provided
            $discount = 0;
            $couponId = null;
            
            if (!empty($data['coupon_code'])) {
                $couponModel = new Coupon();
                $couponResult = $couponModel->validate($data['coupon_code'], $subtotal, $userId);
                
                if (!$couponResult['valid']) {
                    errorResponse($couponResult['message'], [], 400);
                }
                
                $discount = $couponResult['discount'];
                $couponId = $couponResult['coupon']['id'];
            }
            
            // Calculate totals
            $shippingFee = (float)($data['shipping_fee'] ?? 0);
            $tax = 0; // Calculate tax based on business logic
            $total = $subtotal - $discount + $shippingFee + $tax;
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'guest_email' => $userId ? null : sanitize($data['email']),
                'status' => 'pending',
                'payment_method' => sanitize($data['payment_method']),
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shippingFee,
                'tax' => $tax,
                'total' => $total,
                'notes' => sanitize($data['notes'] ?? ''),
            ];
            
            $orderId = $orderModel->create($orderData);
            
            // Add order items and deduct stock
            $db = Database::getInstance();
            foreach ($orderItems as $item) {
                $orderModel->addItem($orderId, $item);
                
                // Deduct stock
                $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $db->query($sql, [$item['quantity'], $item['product_id']]);
            }
            
            // Record coupon usage
            if ($couponId && $userId) {
                $couponModel->recordUsage($couponId, $userId, $orderId);
            }
            
            // Clear cart
            $cartModel->clear($userId, $sessionId);
            
            // Get order details
            $order = $orderModel->findById($orderId);
            
            // Send order confirmation email
            $emailBody = getEmailTemplate("
                <h2>Order Confirmation</h2>
                <p>Thank you for your order!</p>
                <p><strong>Order Number:</strong> {$order['order_number']}</p>
                <p><strong>Total Amount:</strong> " . formatCurrency($total) . "</p>
                <p>We'll send you a shipping confirmation email when your order ships.</p>
                <p><a href='" . $_ENV['APP_URL'] . "/orders/{$order['order_number']}' class='button'>View Order</a></p>
            ");
            sendEmail($data['email'], 'Order Confirmation - ' . $order['order_number'], $emailBody);
            
            successResponse([
                'order' => $order,
            ], 'Order placed successfully', 201);
            break;
            
        case 'cancel':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            if (!isset($_SESSION['user_id'])) {
                errorResponse('Authentication required', [], 401);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['order_number'])) {
                errorResponse('Order number is required', [], 400);
            }
            
            $order = $orderModel->findByOrderNumber($data['order_number']);
            
            if (!$order) {
                errorResponse('Order not found', [], 404);
            }
            
            // Verify ownership
            if ($order['user_id'] != $_SESSION['user_id']) {
                errorResponse('Access denied', [], 403);
            }
            
            // Check if order can be cancelled
            if (!in_array($order['status'], ['pending', 'processing'])) {
                errorResponse('Order cannot be cancelled', [], 400);
            }
            
            // Update order status
            $orderModel->updateStatus($order['id'], 'cancelled');
            
            // Restore stock
            $items = $orderModel->getItems($order['id']);
            $db = Database::getInstance();
            
            foreach ($items as $item) {
                $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
                $db->query($sql, [$item['quantity'], $item['product_id']]);
            }
            
            successResponse([], 'Order cancelled successfully');
            break;
            
        case 'track':
            $orderNumber = $_GET['order_number'] ?? null;
            
            if (!$orderNumber) {
                errorResponse('Order number is required', [], 400);
            }
            
            $order = $orderModel->findByOrderNumber($orderNumber);
            
            if (!$order) {
                errorResponse('Order not found', [], 404);
            }
            
            // If user is logged in, verify ownership
            if (isset($_SESSION['user_id']) && $order['user_id'] != $_SESSION['user_id']) {
                errorResponse('Access denied', [], 403);
            }
            
            successResponse([
                'order_number' => $order['order_number'],
                'status' => $order['status'],
                'payment_status' => $order['payment_status'],
                'created_at' => $order['created_at'],
            ]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

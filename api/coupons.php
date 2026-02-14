<?php
/**
 * Coupons API
 * Handles coupon validation
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Coupon.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'validate';

$couponModel = new Coupon();

try {
    switch ($action) {
        case 'validate':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['code'])) {
                errorResponse('Coupon code is required', [], 400);
            }
            
            if (empty($data['order_amount'])) {
                errorResponse('Order amount is required', [], 400);
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            $result = $couponModel->validate($data['code'], (float)$data['order_amount'], $userId);
            
            if (!$result['valid']) {
                errorResponse($result['message'], [], 400);
            }
            
            successResponse([
                'discount' => $result['discount'],
                'coupon' => [
                    'code' => $result['coupon']['code'],
                    'type' => $result['coupon']['type'],
                    'value' => $result['coupon']['value'],
                    'description' => $result['coupon']['description'],
                ],
            ], 'Coupon applied successfully');
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

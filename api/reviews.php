<?php
/**
 * Reviews API
 * Handles product review operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/models/Review.php';
require_once __DIR__ . '/../app/models/Product.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$reviewModel = new Review();

try {
    switch ($action) {
        case 'list':
            $productId = $_GET['product_id'] ?? null;
            
            if (!$productId) {
                errorResponse('Product ID is required', [], 400);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 10);
            
            $reviews = $reviewModel->getProductReviews($productId, 'approved', $page, $perPage);
            $total = $reviewModel->countProductReviews($productId, 'approved');
            $pagination = paginate($total, $perPage, $page);
            
            // Get rating breakdown
            $rating = $reviewModel->getProductRating($productId);
            $breakdown = $reviewModel->getRatingBreakdown($productId);
            
            successResponse([
                'reviews' => $reviews,
                'pagination' => $pagination,
                'rating' => [
                    'average' => round($rating['avg_rating'] ?? 0, 1),
                    'count' => $rating['total_reviews'] ?? 0,
                ],
                'breakdown' => $breakdown,
            ]);
            break;
            
        case 'submit':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            if (!isset($_SESSION['user_id'])) {
                errorResponse('Authentication required', [], 401);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['product_id', 'rating', 'body'];
            $errors = validateRequired($data, $required);
            
            if (!empty($errors)) {
                errorResponse('Validation failed', $errors, 422);
            }
            
            // Validate rating
            $rating = (int)$data['rating'];
            if ($rating < 1 || $rating > 5) {
                errorResponse('Rating must be between 1 and 5', [], 422);
            }
            
            // Check if product exists
            $productModel = new Product();
            $product = $productModel->findById($data['product_id']);
            
            if (!$product) {
                errorResponse('Product not found', [], 404);
            }
            
            // Create review
            $reviewData = [
                'user_id' => $_SESSION['user_id'],
                'product_id' => (int)$data['product_id'],
                'rating' => $rating,
                'title' => sanitize($data['title'] ?? ''),
                'body' => sanitize($data['body']),
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            
            $reviewId = $reviewModel->create($reviewData);
            
            successResponse([
                'review_id' => $reviewId,
            ], 'Review submitted successfully. It will be visible after approval.', 201);
            break;
            
        case 'helpful':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['review_id'])) {
                errorResponse('Review ID is required', [], 400);
            }
            
            $reviewModel->incrementHelpful((int)$data['review_id']);
            
            successResponse([], 'Thank you for your feedback');
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

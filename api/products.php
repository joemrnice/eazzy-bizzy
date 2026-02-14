<?php
/**
 * Products API
 * Handles product listing, filtering, search, and details
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Review.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$productModel = new Product();
$reviewModel = new Review();

try {
    switch ($action) {
        case 'list':
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 12);
            
            $filters = [
                'category_id' => $_GET['category'] ?? null,
                'brand_id' => $_GET['brand'] ?? null,
                'min_price' => $_GET['min_price'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'search' => $_GET['search'] ?? null,
                'sort' => $_GET['sort'] ?? 'newest',
            ];
            
            $products = $productModel->getAll($filters, $page, $perPage);
            $total = $productModel->count($filters);
            $pagination = paginate($total, $perPage, $page);
            
            successResponse([
                'products' => $products,
                'pagination' => $pagination,
            ]);
            break;
            
        case 'detail':
            $slug = $_GET['slug'] ?? null;
            
            if (!$slug) {
                errorResponse('Product slug is required', [], 400);
            }
            
            $product = $productModel->findBySlug($slug);
            
            if (!$product) {
                errorResponse('Product not found', [], 404);
            }
            
            // Get additional data
            $product['images'] = $productModel->getImages($product['id']);
            $product['attributes'] = $productModel->getAttributes($product['id']);
            $product['variants'] = $productModel->getVariants($product['id']);
            
            // Get rating
            $rating = $reviewModel->getProductRating($product['id']);
            $product['rating'] = [
                'average' => round($rating['avg_rating'] ?? 0, 1),
                'count' => $rating['total_reviews'] ?? 0,
            ];
            
            // Get related products
            $product['related'] = $productModel->getRelated($product['id'], $product['category_id'], 4);
            
            successResponse(['product' => $product]);
            break;
            
        case 'featured':
            $limit = (int)($_GET['limit'] ?? 8);
            $products = $productModel->getFeatured($limit);
            
            successResponse(['products' => $products]);
            break;
            
        case 'search':
            $query = $_GET['q'] ?? '';
            
            if (strlen($query) < 2) {
                errorResponse('Search query must be at least 2 characters', [], 400);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 12);
            
            $filters = ['search' => $query];
            $products = $productModel->getAll($filters, $page, $perPage);
            $total = $productModel->count($filters);
            $pagination = paginate($total, $perPage, $page);
            
            successResponse([
                'query' => $query,
                'products' => $products,
                'pagination' => $pagination,
            ]);
            break;
            
        case 'by-category':
            $categorySlug = $_GET['slug'] ?? null;
            
            if (!$categorySlug) {
                errorResponse('Category slug is required', [], 400);
            }
            
            require_once __DIR__ . '/../app/models/Category.php';
            $categoryModel = new Category();
            $category = $categoryModel->findBySlug($categorySlug);
            
            if (!$category) {
                errorResponse('Category not found', [], 404);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 12);
            
            $filters = ['category_id' => $category['id']];
            $products = $productModel->getAll($filters, $page, $perPage);
            $total = $productModel->count($filters);
            $pagination = paginate($total, $perPage, $page);
            
            successResponse([
                'category' => $category,
                'products' => $products,
                'pagination' => $pagination,
            ]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

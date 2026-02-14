<?php
/**
 * Brands API
 * Handles brand operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Brand.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$brandModel = new Brand();

try {
    switch ($action) {
        case 'list':
            $brands = $brandModel->getBrandsWithProductCount();
            
            successResponse([
                'brands' => $brands,
            ]);
            break;
            
        case 'all':
            $activeOnly = isset($_GET['active_only']) ? (bool)$_GET['active_only'] : true;
            $brands = $brandModel->getAll($activeOnly);
            
            successResponse([
                'brands' => $brands,
            ]);
            break;
            
        case 'detail':
            $slug = $_GET['slug'] ?? null;
            
            if (!$slug) {
                errorResponse('Brand slug is required', [], 400);
            }
            
            $brand = $brandModel->findBySlug($slug);
            
            if (!$brand) {
                errorResponse('Brand not found', [], 404);
            }
            
            successResponse(['brand' => $brand]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

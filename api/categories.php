<?php
/**
 * Categories API
 * Handles category operations
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Category.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'tree';

$categoryModel = new Category();

try {
    switch ($action) {
        case 'tree':
            $tree = $categoryModel->getCategoryTree();
            
            successResponse([
                'categories' => $tree,
            ]);
            break;
            
        case 'list':
            $categories = $categoryModel->getCategoriesWithProductCount();
            
            successResponse([
                'categories' => $categories,
            ]);
            break;
            
        case 'all':
            $activeOnly = isset($_GET['active_only']) ? (bool)$_GET['active_only'] : true;
            $categories = $categoryModel->getAll($activeOnly);
            
            successResponse([
                'categories' => $categories,
            ]);
            break;
            
        case 'detail':
            $slug = $_GET['slug'] ?? null;
            
            if (!$slug) {
                errorResponse('Category slug is required', [], 400);
            }
            
            $category = $categoryModel->findBySlug($slug);
            
            if (!$category) {
                errorResponse('Category not found', [], 404);
            }
            
            // Get children if any
            $category['children'] = $categoryModel->getChildCategories($category['id']);
            
            successResponse(['category' => $category]);
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}

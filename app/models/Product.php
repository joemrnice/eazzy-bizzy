<?php
/**
 * Product Model
 * Handles all product-related database operations
 */

require_once __DIR__ . '/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function findBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.slug = ? AND p.is_active = 1";
        return $this->db->query($sql, [$slug])->fetch();
    }
    
    public function getAll($filters = [], $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $where = ['p.is_active = 1'];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = ?';
            $params[] = $filters['brand_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= ?';
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= ?';
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = 'p.price ASC';
                    break;
                case 'price_desc':
                    $orderBy = 'p.price DESC';
                    break;
                case 'name_asc':
                    $orderBy = 'p.name ASC';
                    break;
                case 'name_desc':
                    $orderBy = 'p.name DESC';
                    break;
            }
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function count($filters = []) {
        $where = ['p.is_active = 1'];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = ?';
            $params[] = $filters['brand_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= ?';
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= ?';
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
        $result = $this->db->query($sql, $params)->fetch();
        return $result['total'];
    }
    
    public function getFeatured($limit = 8) {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.is_active = 1 AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        return $this->db->query($sql, [$limit])->fetchAll();
    }
    
    public function getImages($productId) {
        $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC, is_primary DESC";
        return $this->db->query($sql, [$productId])->fetchAll();
    }
    
    public function getAttributes($productId) {
        $sql = "SELECT * FROM product_attributes WHERE product_id = ? ORDER BY attribute_name ASC";
        return $this->db->query($sql, [$productId])->fetchAll();
    }
    
    public function getVariants($productId) {
        $sql = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY variant_name ASC";
        return $this->db->query($sql, [$productId])->fetchAll();
    }
    
    public function getRelated($productId, $categoryId, $limit = 4) {
        $sql = "SELECT p.*, 
                (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
                ORDER BY RAND()
                LIMIT ?";
        return $this->db->query($sql, [$categoryId, $productId, $limit])->fetchAll();
    }
    
    public function create($data) {
        return $this->db->insert('products', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('products', $data, ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('products', ['id' => $id]);
    }
    
    public function updateStock($id, $quantity) {
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        return $this->db->query($sql, [$quantity, $id])->rowCount();
    }
    
    public function getLowStock($threshold = 5) {
        $sql = "SELECT * FROM products WHERE stock <= ? AND is_active = 1 ORDER BY stock ASC LIMIT 20";
        return $this->db->query($sql, [$threshold])->fetchAll();
    }
}

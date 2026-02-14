<?php
/**
 * Wishlist Model
 * Handles wishlist operations
 */

require_once __DIR__ . '/Database.php';

class Wishlist {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getItems($userId) {
        $sql = "SELECT w.*, p.name, p.slug, p.price, p.sale_price, p.stock,
                (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image,
                b.name as brand_name
                FROM wishlists w
                JOIN products p ON w.product_id = p.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
        
        return $this->db->query($sql, [$userId])->fetchAll();
    }
    
    public function add($userId, $productId) {
        // Check if already exists
        $existing = $this->db->selectOne('wishlists', [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        
        if ($existing) {
            return $existing['id'];
        }
        
        return $this->db->insert('wishlists', [
            'user_id' => $userId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function remove($userId, $productId) {
        return $this->db->delete('wishlists', [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }
    
    public function isInWishlist($userId, $productId) {
        $item = $this->db->selectOne('wishlists', [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        
        return !empty($item);
    }
    
    public function getCount($userId) {
        $sql = "SELECT COUNT(*) as total FROM wishlists WHERE user_id = ?";
        $result = $this->db->query($sql, [$userId])->fetch();
        return $result['total'] ?? 0;
    }
}

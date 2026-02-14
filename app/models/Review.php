<?php
/**
 * Review Model
 */

require_once __DIR__ . '/Database.php';

class Review {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getProductReviews($productId, $status = 'approved', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT r.*, u.name as user_name, u.avatar
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ? AND r.status = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->query($sql, [$productId, $status, $perPage, $offset])->fetchAll();
    }
    
    public function countProductReviews($productId, $status = 'approved') {
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE product_id = ? AND status = ?";
        $result = $this->db->query($sql, [$productId, $status])->fetch();
        return $result['total'];
    }
    
    public function getProductRating($productId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                FROM reviews
                WHERE product_id = ? AND status = 'approved'";
        return $this->db->query($sql, [$productId])->fetch();
    }
    
    public function getRatingBreakdown($productId) {
        $sql = "SELECT rating, COUNT(*) as count
                FROM reviews
                WHERE product_id = ? AND status = 'approved'
                GROUP BY rating
                ORDER BY rating DESC";
        return $this->db->query($sql, [$productId])->fetchAll();
    }
    
    public function create($data) {
        return $this->db->insert('reviews', $data);
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('reviews', ['status' => $status], ['id' => $id]);
    }
    
    public function incrementHelpful($id) {
        $sql = "UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount();
    }
}

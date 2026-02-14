<?php
/**
 * Order Model
 * Handles all order-related operations
 */

require_once __DIR__ . '/Database.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->selectOne('orders', ['id' => $id]);
    }
    
    public function findByOrderNumber($orderNumber) {
        return $this->db->selectOne('orders', ['order_number' => $orderNumber]);
    }
    
    public function create($data) {
        $data['order_number'] = $this->generateOrderNumber();
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('orders', $data);
    }
    
    public function addItem($orderId, $item) {
        $item['order_id'] = $orderId;
        $item['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('order_items', $item);
    }
    
    public function getItems($orderId) {
        $sql = "SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC";
        return $this->db->query($sql, [$orderId])->fetchAll();
    }
    
    public function getUserOrders($userId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->query($sql, [$userId, $perPage, $offset])->fetchAll();
    }
    
    public function countUserOrders($userId) {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
        $result = $this->db->query($sql, [$userId])->fetch();
        return $result['total'];
    }
    
    public function getAll($filters = [], $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $where[] = 'payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(order_number LIKE ? OR guest_email LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = empty($where) ? '1=1' : implode(' AND ', $where);
        
        $sql = "SELECT o.*,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                WHERE {$whereClause}
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function count($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $where[] = 'payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        
        $whereClause = empty($where) ? '1=1' : implode(' AND ', $where);
        
        $sql = "SELECT COUNT(*) as total FROM orders WHERE {$whereClause}";
        $result = $this->db->query($sql, $params)->fetch();
        return $result['total'];
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('orders', ['status' => $status], ['id' => $id]);
    }
    
    public function updatePaymentStatus($id, $paymentStatus) {
        return $this->db->update('orders', ['payment_status' => $paymentStatus], ['id' => $id]);
    }
    
    public function getTodayOrders() {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()";
        $result = $this->db->query($sql)->fetch();
        return $result['total'];
    }
    
    public function getTodayRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM orders 
                WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'";
        $result = $this->db->query($sql)->fetch();
        return $result['revenue'] ?? 0;
    }
    
    public function getRecentOrders($limit = 10) {
        $sql = "SELECT o.*, u.name as user_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT ?";
        return $this->db->query($sql, [$limit])->fetchAll();
    }
    
    public function getSalesData($days = 30) {
        $sql = "SELECT DATE(created_at) as date, 
                SUM(total) as revenue,
                COUNT(*) as order_count
                FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND payment_status = 'paid'
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        return $this->db->query($sql, [$days])->fetchAll();
    }
    
    private function generateOrderNumber() {
        return 'ORD-' . strtoupper(uniqid()) . '-' . date('YmdHis');
    }
}

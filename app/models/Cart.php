<?php
/**
 * Cart Model
 * Handles shopping cart operations
 */

require_once __DIR__ . '/Database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getItems($userId = null, $sessionId = null) {
        $where = [];
        $params = [];
        
        if ($userId) {
            $where[] = 'c.user_id = ?';
            $params[] = $userId;
        } elseif ($sessionId) {
            $where[] = 'c.session_id = ? AND c.user_id IS NULL';
            $params[] = $sessionId;
        } else {
            return [];
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT c.*, p.name, p.slug, p.price, p.sale_price, p.stock,
                (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image,
                v.variant_name, v.price as variant_price
                FROM carts c
                JOIN products p ON c.product_id = p.id
                LEFT JOIN product_variants v ON c.variant_id = v.id
                WHERE {$whereClause}";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function addItem($data) {
        // Check if item already exists
        $where = ['product_id' => $data['product_id']];
        
        if (!empty($data['user_id'])) {
            $where['user_id'] = $data['user_id'];
        } else {
            $where['session_id'] = $data['session_id'];
            $where['user_id'] = null;
        }
        
        if (!empty($data['variant_id'])) {
            $where['variant_id'] = $data['variant_id'];
        }
        
        $existing = $this->db->selectOne('carts', $where);
        
        if ($existing) {
            // Update quantity
            return $this->db->update('carts', [
                'quantity' => $existing['quantity'] + ($data['quantity'] ?? 1)
            ], ['id' => $existing['id']]);
        }
        
        // Insert new item
        return $this->db->insert('carts', $data);
    }
    
    public function updateQuantity($id, $quantity, $userId = null, $sessionId = null) {
        $where = ['id' => $id];
        
        if ($userId) {
            $where['user_id'] = $userId;
        } elseif ($sessionId) {
            $where['session_id'] = $sessionId;
        }
        
        return $this->db->update('carts', ['quantity' => $quantity], $where);
    }
    
    public function removeItem($id, $userId = null, $sessionId = null) {
        $where = ['id' => $id];
        
        if ($userId) {
            $where['user_id'] = $userId;
        } elseif ($sessionId) {
            $where['session_id'] = $sessionId;
        }
        
        return $this->db->delete('carts', $where);
    }
    
    public function clear($userId = null, $sessionId = null) {
        if ($userId) {
            return $this->db->delete('carts', ['user_id' => $userId]);
        } elseif ($sessionId) {
            $sql = "DELETE FROM carts WHERE session_id = ? AND user_id IS NULL";
            return $this->db->query($sql, [$sessionId])->rowCount();
        }
        return 0;
    }
    
    public function mergeGuestCart($sessionId, $userId) {
        // Get guest cart items
        $sql = "SELECT * FROM carts WHERE session_id = ? AND user_id IS NULL";
        $guestItems = $this->db->query($sql, [$sessionId])->fetchAll();
        
        foreach ($guestItems as $item) {
            // Check if user already has this item
            $where = [
                'user_id' => $userId,
                'product_id' => $item['product_id']
            ];
            
            if ($item['variant_id']) {
                $where['variant_id'] = $item['variant_id'];
            }
            
            $existing = $this->db->selectOne('carts', $where);
            
            if ($existing) {
                // Update quantity
                $this->db->update('carts', [
                    'quantity' => $existing['quantity'] + $item['quantity']
                ], ['id' => $existing['id']]);
                
                // Delete guest item
                $this->db->delete('carts', ['id' => $item['id']]);
            } else {
                // Update guest item to user
                $this->db->update('carts', [
                    'user_id' => $userId,
                    'session_id' => null
                ], ['id' => $item['id']]);
            }
        }
    }
    
    public function getCount($userId = null, $sessionId = null) {
        if ($userId) {
            $sql = "SELECT SUM(quantity) as total FROM carts WHERE user_id = ?";
            $result = $this->db->query($sql, [$userId])->fetch();
        } elseif ($sessionId) {
            $sql = "SELECT SUM(quantity) as total FROM carts WHERE session_id = ? AND user_id IS NULL";
            $result = $this->db->query($sql, [$sessionId])->fetch();
        } else {
            return 0;
        }
        
        return $result['total'] ?? 0;
    }
    
    public function calculateTotals($userId = null, $sessionId = null) {
        $items = $this->getItems($userId, $sessionId);
        $subtotal = 0;
        
        foreach ($items as $item) {
            $price = $item['variant_price'] ?? $item['sale_price'] ?? $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        
        return [
            'subtotal' => $subtotal,
            'item_count' => count($items),
            'quantity' => array_sum(array_column($items, 'quantity'))
        ];
    }
}

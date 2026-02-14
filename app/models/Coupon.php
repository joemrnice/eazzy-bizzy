<?php
/**
 * Coupon Model
 */

require_once __DIR__ . '/Database.php';

class Coupon {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findByCode($code) {
        return $this->db->selectOne('coupons', ['code' => $code]);
    }
    
    public function validate($code, $orderAmount, $userId = null) {
        $coupon = $this->findByCode($code);
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }
        
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is not active'];
        }
        
        // Check expiry
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }
        
        // Check start date
        if ($coupon['starts_at'] && strtotime($coupon['starts_at']) > time()) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid'];
        }
        
        // Check minimum order amount
        if ($orderAmount < $coupon['min_order_amount']) {
            return ['valid' => false, 'message' => 'Minimum order amount not met'];
        }
        
        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }
        
        // Check per-user limit
        if ($userId && $coupon['per_user_limit']) {
            $userUsage = $this->getUserUsageCount($coupon['id'], $userId);
            if ($userUsage >= $coupon['per_user_limit']) {
                return ['valid' => false, 'message' => 'You have already used this coupon'];
            }
        }
        
        // Calculate discount
        $discount = 0;
        if ($coupon['type'] === 'fixed') {
            $discount = $coupon['value'];
        } else {
            $discount = ($orderAmount * $coupon['value']) / 100;
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        }
        
        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount
        ];
    }
    
    public function getUserUsageCount($couponId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM coupon_usage WHERE coupon_id = ? AND user_id = ?";
        $result = $this->db->query($sql, [$couponId, $userId])->fetch();
        return $result['count'];
    }
    
    public function recordUsage($couponId, $userId, $orderId) {
        // Insert usage record
        $this->db->insert('coupon_usage', [
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'order_id' => $orderId
        ]);
        
        // Increment used count
        $sql = "UPDATE coupons SET used_count = used_count + 1 WHERE id = ?";
        $this->db->query($sql, [$couponId]);
    }
    
    public function getAll($activeOnly = false) {
        $where = $activeOnly ? ['is_active' => 1] : [];
        return $this->db->select('coupons', $where, 'created_at DESC');
    }
    
    public function create($data) {
        return $this->db->insert('coupons', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('coupons', $data, ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('coupons', ['id' => $id]);
    }
}

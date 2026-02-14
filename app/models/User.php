<?php
/**
 * User Model
 * Handles all user-related database operations
 */

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->selectOne('users', ['id' => $id]);
    }
    
    public function findByEmail($email) {
        return $this->db->selectOne('users', ['email' => $email]);
    }
    
    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['verification_token'] = bin2hex(random_bytes(32));
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('users', $data);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('users', $data, ['id' => $id]);
    }
    
    public function verifyEmail($token) {
        $sql = "UPDATE users SET email_verified = 1, verification_token = NULL WHERE verification_token = ?";
        return $this->db->query($sql, [$token])->rowCount() > 0;
    }
    
    public function setResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->update('users', [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ], ['email' => $email]);
        
        return $token;
    }
    
    public function resetPassword($token, $newPassword) {
        $sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()";
        $result = $this->db->query($sql, [$token])->fetch();
        
        if (!$result) {
            return false;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $this->db->update('users', [
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_token_expires' => null
        ], ['id' => $result['id']]);
        
        return true;
    }
    
    public function getAll($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->query($sql, [$perPage, $offset])->fetchAll();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $this->db->query($sql)->fetch();
        return $result['total'];
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('users', ['status' => $status], ['id' => $id]);
    }
}

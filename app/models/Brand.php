<?php
/**
 * Brand Model
 */

require_once __DIR__ . '/Database.php';

class Brand {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->selectOne('brands', ['id' => $id]);
    }
    
    public function findBySlug($slug) {
        return $this->db->selectOne('brands', ['slug' => $slug]);
    }
    
    public function getAll($activeOnly = true) {
        $where = $activeOnly ? ['is_active' => 1] : [];
        return $this->db->select('brands', $where, 'name ASC');
    }
    
    public function getBrandsWithProductCount() {
        $sql = "SELECT b.*, COUNT(p.id) as product_count
                FROM brands b
                LEFT JOIN products p ON b.id = p.brand_id AND p.is_active = 1
                WHERE b.is_active = 1
                GROUP BY b.id
                ORDER BY b.name ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function create($data) {
        return $this->db->insert('brands', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('brands', $data, ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('brands', ['id' => $id]);
    }
}

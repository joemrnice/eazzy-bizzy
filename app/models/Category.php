<?php
/**
 * Category Model
 * Handles category operations
 */

require_once __DIR__ . '/Database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->selectOne('categories', ['id' => $id]);
    }
    
    public function findBySlug($slug) {
        return $this->db->selectOne('categories', ['slug' => $slug, 'is_active' => 1]);
    }
    
    public function getAll($activeOnly = true) {
        $where = $activeOnly ? ['is_active' => 1] : [];
        return $this->db->select('categories', $where, 'display_order ASC, name ASC');
    }
    
    public function getParentCategories() {
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY display_order ASC, name ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getChildCategories($parentId) {
        $sql = "SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY display_order ASC, name ASC";
        return $this->db->query($sql, [$parentId])->fetchAll();
    }
    
    public function getCategoryTree() {
        $parents = $this->getParentCategories();
        $tree = [];
        
        foreach ($parents as $parent) {
            $parent['children'] = $this->getChildCategories($parent['id']);
            $tree[] = $parent;
        }
        
        return $tree;
    }
    
    public function getCategoriesWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function create($data) {
        return $this->db->insert('categories', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('categories', $data, ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete('categories', ['id' => $id]);
    }
}

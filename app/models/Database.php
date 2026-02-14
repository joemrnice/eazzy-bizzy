<?php
/**
 * Database Class
 * PDO wrapper with query builder and transaction support
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $config;
    
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/database.php';
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['database'],
                $this->config['charset']
            );
            
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            die('Database connection failed');
        }
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError($e->getMessage() . ' | SQL: ' . $sql);
            throw $e;
        }
    }
    
    public function select($table, $where = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$table}";
        
        if (!empty($where)) {
            $conditions = [];
            foreach (array_keys($where) as $key) {
                $conditions[] = "{$key} = :{$key}";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->query($sql, $where)->fetchAll();
    }
    
    public function selectOne($table, $where = []) {
        $result = $this->select($table, $where, null, 1);
        return $result[0] ?? null;
    }
    
    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "{$key} = :set_{$key}";
        }
        
        $whereClause = [];
        foreach (array_keys($where) as $key) {
            $whereClause[] = "{$key} = :where_{$key}";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . 
               " WHERE " . implode(' AND ', $whereClause);
        
        $params = [];
        foreach ($data as $key => $value) {
            $params["set_{$key}"] = $value;
        }
        foreach ($where as $key => $value) {
            $params["where_{$key}"] = $value;
        }
        
        return $this->query($sql, $params)->rowCount();
    }
    
    public function delete($table, $where) {
        $conditions = [];
        foreach (array_keys($where) as $key) {
            $conditions[] = "{$key} = :{$key}";
        }
        
        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $conditions);
        return $this->query($sql, $where)->rowCount();
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollBack();
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    private function logError($message) {
        $logFile = __DIR__ . '/../../logs/database.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}\n";
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

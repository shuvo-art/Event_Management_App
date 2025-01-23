<?php
require_once 'utils.php';

class Event {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create($title, $description, $date, $location, $capacity, $organizer_id) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO events (title, description, date, location, capacity, organizer_id) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            return $stmt->execute([
                Utils::sanitizeInput($title),
                Utils::sanitizeInput($description),
                $date,
                Utils::sanitizeInput($location),
                (int)$capacity,
                (int)$organizer_id
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function update($id, $title, $description, $date, $location, $capacity) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE events 
                 SET title = ?, description = ?, date = ?, location = ?, capacity = ? 
                 WHERE id = ?"
            );
            
            return $stmt->execute([
                Utils::sanitizeInput($title),
                Utils::sanitizeInput($description),
                $date,
                Utils::sanitizeInput($location),
                (int)$capacity,
                (int)$id
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getAll($page = 1, $limit = 10, $search = '') {
        try {
            $offset = ($page - 1) * $limit;
            $search = "%{$search}%";
            
            $stmt = $this->conn->prepare(
                "SELECT e.*, u.name as organizer_name,
                 (SELECT COUNT(*) FROM registrations WHERE event_id = e.id) as registration_count 
                 FROM events e 
                 LEFT JOIN users u ON e.organizer_id = u.id 
                 WHERE e.title LIKE ? OR e.description LIKE ? 
                 ORDER BY e.date ASC 
                 LIMIT ? OFFSET ?"
            );
            
            $stmt->execute([$search, $search, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT e.*, u.name as organizer_name 
                 FROM events e 
                 LEFT JOIN users u ON e.organizer_id = u.id 
                 WHERE e.id = ?"
            );
            
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function search($term) {
        try {
            $term = "%{$term}%";
            
            $stmt = $this->conn->prepare(
                "SELECT e.*, u.name as organizer_name 
                 FROM events e 
                 LEFT JOIN users u ON e.organizer_id = u.id 
                 WHERE e.title LIKE ? OR e.description LIKE ? 
                 ORDER BY e.date ASC"
            );
            
            $stmt->execute([$term, $term]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
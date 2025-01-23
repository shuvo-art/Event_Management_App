<?php
require_once 'utils.php';

class Registration {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function register($event_id, $user_id) {
        try {
            // Check if event exists and has capacity
            $stmt = $this->conn->prepare(
                "SELECT capacity, 
                 (SELECT COUNT(*) FROM registrations WHERE event_id = ?) as current_registrations 
                 FROM events WHERE id = ?"
            );
            $stmt->execute([(int)$event_id, (int)$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                throw new Exception("Event not found");
            }
            
            if ($event['current_registrations'] >= $event['capacity']) {
                throw new Exception("Event is full");
            }
            
            // Check if already registered
            $stmt = $this->conn->prepare(
                "SELECT id FROM registrations WHERE event_id = ? AND user_id = ?"
            );
            $stmt->execute([(int)$event_id, (int)$user_id]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Already registered");
            }
            
            // Register
            $stmt = $this->conn->prepare(
                "INSERT INTO registrations (event_id, user_id, status) VALUES (?, ?, 'confirmed')"
            );
            return $stmt->execute([(int)$event_id, (int)$user_id]);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getAttendees($event_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT u.name, u.email, r.created_at, r.status 
                 FROM registrations r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.event_id = ? AND r.status = 'confirmed'"
            );
            
            $stmt->execute([(int)$event_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function cancel($event_id, $user_id) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE registrations 
                 SET status = 'cancelled' 
                 WHERE event_id = ? AND user_id = ?"
            );
            return $stmt->execute([(int)$event_id, (int)$user_id]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
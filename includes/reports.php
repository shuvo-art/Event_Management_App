<?php
require_once 'utils.php';

class Reports {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateAttendeeList($event_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT u.name, u.email, r.created_at 
                 FROM registrations r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.event_id = ?"
            );
            $stmt->execute([(int)$event_id]);
            $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($attendees) === 0) {
                throw new Exception("No attendees found for this event.");
            }

            // Generate CSV
            Utils::generateCSV($attendees, "attendees_event_{$event_id}.csv");
        } catch (Exception $e) {
            Utils::jsonResponse(['message' => $e->getMessage()], 400);
        }
    }
}

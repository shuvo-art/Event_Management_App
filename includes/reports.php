<?php
require_once 'utils.php';

class Reports {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateAttendeeList($event_id) {
        try {
            // Fetch attendees
            $stmt = $this->conn->prepare(
                "SELECT u.name, u.email, r.created_at 
                 FROM registrations r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.event_id = ? AND r.status = 'confirmed'"
            );
            $stmt->execute([(int)$event_id]);
            $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($attendees) === 0) {
                throw new Exception("No attendees found for this event.");
            }

            // Generate CSV file
            $filename = "attendees_event_{$event_id}.csv";
            Utils::generateCSV($attendees, $filename);
        } catch (Exception $e) {
            Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
?>

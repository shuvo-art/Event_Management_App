<?php
require_once 'utils.php';

class Auth {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function register($name, $email, $password) {
        try {
            // Validate input
            $name = Utils::sanitizeInput($name);
            $email = Utils::sanitizeInput($email);
            
            if (empty($name) || empty($email) || empty($password)) {
                throw new Exception("All fields are required");
            }
            
            if (!Utils::validateEmail($email)) {
                throw new Exception("Invalid email format");
            }
            
            // Check if email exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Email already exists");
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->conn->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function login($email, $password) {
        try {
            $email = Utils::sanitizeInput($email);
            
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function logout() {
        session_destroy();
        Utils::redirect('/login.php');
    }
    
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<?php
// Database Configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL password is empty
define('DB_NAME', 'barebloom_auth');

// Site Configuration
define('SITE_URL', 'http://localhost/barebloom/'); // Adjust based on your folder structure
define('SITE_NAME', 'Bare Bloom');

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_RESET_EXPIRY', 1800); // 30 minutes

// Database Connection Class
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Helper Functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateRandomCode($length = 4) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function sendEmail($to, $subject, $message) {
    // In a real application, use PHPMailer or similar
    // For demo purposes, we'll just log the email
    error_log("Email to {$to}: {$subject} - {$message}");
    return true; // Simulate successful sending
}

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
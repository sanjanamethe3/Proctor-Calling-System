<?php
/**
 * Authentication API
 * Handles login, logout, and user authentication
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class AuthAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Login User
     */
    public function login($email, $password) {
        // Find user by email
        $user = $this->db->findOne('users', ['email' => $email]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid password'
            ];
        }
        
        // Create session
        session_start();
        $_SESSION['user_id'] = (string)$user['_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => (string)$user['_id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ]
        ];
    }
    
    /**
     * Register User
     */
    public function register($email, $password, $name, $role) {
        // Check if user exists
        $existingUser = $this->db->findOne('users', ['email' => $email]);
        
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'User already exists'
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $userData = [
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'role' => $role,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('users', $userData);
    }
    
    /**
     * Logout User
     */
    public function logout() {
        session_start();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }
    
    /**
     * Verify Token
     */
    public function verifyToken($token) {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Not authenticated'
            ];
        }
        
        return [
            'success' => true,
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ];
    }
}

// Handle API Request
try {
    $auth = new AuthAPI();
    
    // Get action from request
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? $_GET['action'] ?? null;
    
    switch ($action) {
        case 'login':
            $email = $input['email'] ?? null;
            $password = $input['password'] ?? null;
            
            if (!$email || !$password) {
                echo json_encode(['success' => false, 'message' => 'Email and password required']);
            } else {
                echo json_encode($auth->login($email, $password));
            }
            break;
            
        case 'register':
            $email = $input['email'] ?? null;
            $password = $input['password'] ?? null;
            $name = $input['name'] ?? null;
            $role = $input['role'] ?? null;
            
            if (!$email || !$password || !$name || !$role) {
                echo json_encode(['success' => false, 'message' => 'All fields required']);
            } else {
                echo json_encode($auth->register($email, $password, $name, $role));
            }
            break;
            
        case 'logout':
            echo json_encode($auth->logout());
            break;
            
        case 'verify':
            $token = $input['token'] ?? $_COOKIE['auth_token'] ?? null;
            echo json_encode($auth->verifyToken($token));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

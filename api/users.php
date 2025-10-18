<?php
/**
 * User Login and Sign Up API
 * 
 * What this does:
 * - POST /users/login - Let users log in and get a token
 * - GET /users/profile - Show user info and their classes
 * - POST /users/register - Let new users sign up
 */

require_once 'base.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = ltrim($path, '/');

// Remove 'api/' from the URL path
$apiPath = substr($path, 4); // Remove 'api/'

// Figure out which page to show
if (strpos($apiPath, 'users/') === 0) {
    $endpoint = substr($apiPath, 6); // Remove 'users/'
    
    switch ($endpoint) {
        case 'login':
            if ($method === 'POST') {
                handleLogin();
            } else {
                sendError('Method not allowed', 405);
            }
            break;
            
        case 'profile':
            if ($method === 'GET') {
                handleProfile();
            } else {
                sendError('Method not allowed', 405);
            }
            break;
            
        case 'register':
            if ($method === 'POST') {
                handleRegister();
            } else {
                sendError('Method not allowed', 405);
            }
            break;
            
        default:
            sendError('Endpoint not found', 404);
    }
} else {
    sendError('Invalid API path', 404);
}

/**
 * Handle user login
 * POST /users/login
 */
function handleLogin() {
    $data = getRequestData();
    validateRequired($data, ['username', 'password']);
    
    try {
        $pdo = getDBConnection();
        
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$data['username'], $data['username']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            sendError('Invalid credentials', 401);
        }
        
        // Verify password (using password_verify for hashed passwords)
        if (!password_verify($data['password'], $user['password_hash'])) {
            sendError('Invalid credentials', 401);
        }
        
        // Generate token
        $token = generateToken($user['id'], $user['username'], $user['role']);
        
        // Return success response
        sendResponse([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role']
            ]
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle user profile retrieval
 * GET /users/profile
 */
function handleProfile() {
    $user = requireAuth();
    
    try {
        $pdo = getDBConnection();
        
        // Get user details
        $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$user['user_id']]);
        $userData = $stmt->fetch();
        
        if (!$userData) {
            sendError('User not found', 404);
        }
        
        // Get user's enrollments if student
        $enrollments = [];
        if ($userData['role'] === 'student') {
            $stmt = $pdo->prepare("
                SELECT c.id, c.title, c.course_code, c.credits, c.semester, c.year, e.enrolled_at, e.status
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE e.student_id = ? AND e.status = 'active'
            ");
            $stmt->execute([$userData['id']]);
            $enrollments = $stmt->fetchAll();
        }
        
        // Get courses taught if instructor
        $coursesTaught = [];
        if ($userData['role'] === 'instructor') {
            $stmt = $pdo->prepare("SELECT id, title, course_code, credits, semester, year FROM courses WHERE instructor_id = ?");
            $stmt->execute([$userData['id']]);
            $coursesTaught = $stmt->fetchAll();
        }
        
        sendResponse([
            'success' => true,
            'user' => $userData,
            'enrollments' => $enrollments,
            'courses_taught' => $coursesTaught
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle user registration
 * POST /users/register
 */
function handleRegister() {
    $data = getRequestData();
    validateRequired($data, ['username', 'email', 'password', 'first_name', 'last_name']);
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format', 400);
    }
    
    // Validate password strength
    if (strlen($data['password']) < 6) {
        sendError('Password must be at least 6 characters long', 400);
    }
    
    try {
        $pdo = getDBConnection();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$data['username'], $data['email']]);
        if ($stmt->fetch()) {
            sendError('Username or email already exists', 409);
        }
        
        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, first_name, last_name, role) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $role = $data['role'] ?? 'student';
        $stmt->execute([
            $data['username'],
            $data['email'],
            $passwordHash,
            $data['first_name'],
            $data['last_name'],
            $role
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Generate token for immediate login
        $token = generateToken($userId, $data['username'], $role);
        
        sendResponse([
            'success' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => [
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'role' => $role
            ]
        ], 201);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}
?>

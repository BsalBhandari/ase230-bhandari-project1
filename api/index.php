<?php
/**
 * Main API Router
 * 
 * This file routes all API requests to the appropriate handlers
 */

// Enable CORS for all origins (adjust for production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get the request path
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = ltrim($path, '/');

// Remove 'api/' prefix from path
$apiPath = substr($path, 4); // Remove 'api/'

// Route to appropriate API handler
if (strpos($apiPath, 'users/') === 0) {
    require_once 'users.php';
} elseif (strpos($apiPath, 'courses') === 0) {
    require_once 'courses.php';
} elseif (strpos($apiPath, 'enrollments') === 0) {
    require_once 'enrollments.php';
} elseif ($apiPath === 'debug.php') {
    require_once 'debug.php';
} elseif ($apiPath === 'test.php') {
    require_once 'test.php';
} else {
    // API documentation endpoint
    if ($apiPath === '' || $apiPath === '/') {
        showApiDocumentation();
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found', 'path' => $apiPath]);
    }
}

/**
 * Show API documentation
 */
function showApiDocumentation() {
    $documentation = [
        'title' => 'ASE230 Project 1 - LMS API',
        'version' => '1.0.0',
        'description' => 'Learning Management System API',
        'endpoints' => [
            'authentication' => [
                'POST /api/users/login' => [
                    'description' => 'Authenticate user and return JWT token',
                    'security' => 'None',
                    'body' => ['username', 'password'],
                    'response' => 'JWT token + user data'
                ],
                'GET /api/users/profile' => [
                    'description' => 'Get authenticated user profile',
                    'security' => 'Bearer token required',
                    'response' => 'User profile + enrollments/courses'
                ]
            ],
            'user_management' => [
                'POST /api/users/register' => [
                    'description' => 'Register new user',
                    'security' => 'None',
                    'body' => ['username', 'email', 'password', 'first_name', 'last_name'],
                    'response' => 'JWT token + user data'
                ]
            ],
            'course_management' => [
                'POST /api/courses' => [
                    'description' => 'Create new course',
                    'security' => 'Bearer token (instructor/admin)',
                    'body' => ['title', 'course_code', 'description', 'credits', 'semester', 'year'],
                    'response' => 'Created course data'
                ],
                'GET /api/courses' => [
                    'description' => 'List all courses',
                    'security' => 'None',
                    'query_params' => ['semester', 'year', 'instructor'],
                    'response' => 'Array of courses'
                ],
                'PUT /api/courses/{id}' => [
                    'description' => 'Update course',
                    'security' => 'Bearer token (instructor/admin)',
                    'body' => ['title', 'description', 'course_code', 'credits', 'semester', 'year'],
                    'response' => 'Updated course data'
                ]
            ],
            'enrollment_management' => [
                'POST /api/enrollments' => [
                    'description' => 'Enroll user in course',
                    'security' => 'Bearer token required',
                    'body' => ['course_id'],
                    'response' => 'Enrollment data'
                ],
                'GET /api/enrollments' => [
                    'description' => 'List user enrollments',
                    'security' => 'Bearer token required',
                    'query_params' => ['student_id', 'course_id', 'status'],
                    'response' => 'Array of enrollments'
                ]
            ]
        ],
        'authentication' => [
            'type' => 'Bearer Token (JWT)',
            'header' => 'Authorization: Bearer <token>',
            'token_expiry' => '24 hours'
        ],
        'sample_requests' => [
            'login' => [
                'url' => 'POST /api/users/login',
                'body' => '{"username": "student1", "password": "password"}'
            ],
            'get_profile' => [
                'url' => 'GET /api/users/profile',
                'header' => 'Authorization: Bearer <token>'
            ],
            'create_course' => [
                'url' => 'POST /api/courses',
                'header' => 'Authorization: Bearer <token>',
                'body' => '{"title": "Advanced PHP", "course_code": "ASE350", "description": "Advanced PHP concepts"}'
            ]
        ]
    ];
    
    echo json_encode($documentation, JSON_PRETTY_PRINT);
}
?>

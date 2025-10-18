<?php
/**
 * Classes API
 * 
 * What this does:
 * - POST /courses - Create a new class
 * - GET /courses - Show all classes
 * - PUT /courses/{id} - Change a class
 */

require_once 'base.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = ltrim($path, '/');

// Remove 'api/' from the URL path
$apiPath = substr($path, 4); // Remove 'api/'

// Route handling
if (strpos($apiPath, 'courses') === 0) {
    $endpoint = substr($apiPath, 8); // Remove 'courses'
    $endpoint = ltrim($endpoint, '/'); // Remove leading slash
    
    if ($endpoint === '' || $endpoint === '/') {
        // Handle /courses endpoint
        switch ($method) {
            case 'POST':
                handleCreateCourse();
                break;
            case 'GET':
                handleListCourses();
                break;
            default:
                sendError('Method not allowed', 405);
        }
    } elseif (preg_match('/^(\d+)$/', $endpoint, $matches)) {
        // Handle /courses/{id} endpoint
        $courseId = $matches[1];
        switch ($method) {
            case 'PUT':
                handleUpdateCourse($courseId);
                break;
            case 'GET':
                handleGetCourse($courseId);
                break;
            case 'DELETE':
                handleDeleteCourse($courseId);
                break;
            default:
                sendError('Method not allowed', 405);
        }
    } else {
        sendError('Invalid course endpoint', 404);
    }
} else {
    sendError('Invalid API path', 404);
}

/**
 * Handle course creation
 * POST /courses
 */
function handleCreateCourse() {
    $user = requireAuth();
    
    // Only instructors and admins can create courses
    if (!in_array($user['role'], ['instructor', 'admin'])) {
        sendError('Insufficient permissions', 403);
    }
    
    $data = getRequestData();
    validateRequired($data, ['title', 'course_code']);
    
    try {
        $pdo = getDBConnection();
        
        // Check if course code already exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_code = ?");
        $stmt->execute([$data['course_code']]);
        if ($stmt->fetch()) {
            sendError('Course code already exists', 409);
        }
        
        // Insert new course
        $stmt = $pdo->prepare("
            INSERT INTO courses (title, description, instructor_id, course_code, credits, semester, year) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $instructorId = $user['role'] === 'admin' ? ($data['instructor_id'] ?? $user['user_id']) : $user['user_id'];
        
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $instructorId,
            $data['course_code'],
            $data['credits'] ?? 3,
            $data['semester'] ?? null,
            $data['year'] ?? date('Y')
        ]);
        
        $courseId = $pdo->lastInsertId();
        
        // Get the created course with instructor details
        $stmt = $pdo->prepare("
            SELECT c.*, u.first_name, u.last_name, u.username
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        sendResponse([
            'success' => true,
            'message' => 'Course created successfully',
            'course' => $course
        ], 201);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle course listing
 * GET /courses
 */
function handleListCourses() {
    try {
        $pdo = getDBConnection();
        
        // Get query parameters
        $semester = $_GET['semester'] ?? null;
        $year = $_GET['year'] ?? null;
        $instructor = $_GET['instructor'] ?? null;
        
        // Build query
        $sql = "
            SELECT c.*, u.first_name, u.last_name, u.username as instructor_username,
                   COUNT(e.id) as enrollment_count
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            LEFT JOIN enrollments e ON c.id = e.course_id AND e.status = 'active'
        ";
        
        $conditions = [];
        $params = [];
        
        if ($semester) {
            $conditions[] = "c.semester = ?";
            $params[] = $semester;
        }
        
        if ($year) {
            $conditions[] = "c.year = ?";
            $params[] = $year;
        }
        
        if ($instructor) {
            $conditions[] = "c.instructor_id = ?";
            $params[] = $instructor;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();
        
        sendResponse([
            'success' => true,
            'courses' => $courses,
            'count' => count($courses)
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle course update
 * PUT /courses/{id}
 */
function handleUpdateCourse($courseId) {
    $user = requireAuth();
    
    try {
        $pdo = getDBConnection();
        
        // Check if course exists and user has permission
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        if (!$course) {
            sendError('Course not found', 404);
        }
        
        // Check permissions
        if ($user['role'] !== 'admin' && $course['instructor_id'] != $user['user_id']) {
            sendError('Insufficient permissions', 403);
        }
        
        $data = getRequestData();
        
        // Build update query dynamically
        $updateFields = [];
        $params = [];
        
        $allowedFields = ['title', 'description', 'course_code', 'credits', 'semester', 'year'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        // Admin can change instructor
        if ($user['role'] === 'admin' && isset($data['instructor_id'])) {
            $updateFields[] = "instructor_id = ?";
            $params[] = $data['instructor_id'];
        }
        
        if (empty($updateFields)) {
            sendError('No valid fields to update', 400);
        }
        
        $params[] = $courseId;
        
        $sql = "UPDATE courses SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Get updated course
        $stmt = $pdo->prepare("
            SELECT c.*, u.first_name, u.last_name, u.username
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $updatedCourse = $stmt->fetch();
        
        sendResponse([
            'success' => true,
            'message' => 'Course updated successfully',
            'course' => $updatedCourse
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle get single course
 * GET /courses/{id}
 */
function handleGetCourse($courseId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT c.*, u.first_name, u.last_name, u.username as instructor_username,
                   COUNT(e.id) as enrollment_count
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            LEFT JOIN enrollments e ON c.id = e.course_id AND e.status = 'active'
            WHERE c.id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        if (!$course) {
            sendError('Course not found', 404);
        }
        
        sendResponse([
            'success' => true,
            'course' => $course
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle course deletion
 * DELETE /courses/{id}
 */
function handleDeleteCourse($courseId) {
    $user = requireAuth();
    
    // Only admins can delete courses
    if ($user['role'] !== 'admin') {
        sendError('Insufficient permissions', 403);
    }
    
    try {
        $pdo = getDBConnection();
        
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        if (!$stmt->fetch()) {
            sendError('Course not found', 404);
        }
        
        // Delete course (cascade will handle related records)
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        
        sendResponse([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}
?>

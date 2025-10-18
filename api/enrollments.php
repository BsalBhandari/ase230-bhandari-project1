<?php
/**
 * Joining Classes API
 * 
 * What this does:
 * - POST /enrollments - Let user join a class
 * - GET /enrollments - Show user's classes
 */

require_once 'base.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = ltrim($path, '/');

// Remove 'api/' prefix from path
$apiPath = substr($path, 4); // Remove 'api/'

// Route handling
if (strpos($apiPath, 'enrollments') === 0) {
    $endpoint = substr($apiPath, 12); // Remove 'enrollments'
    $endpoint = ltrim($endpoint, '/'); // Remove leading slash
    
    if ($endpoint === '' || $endpoint === '/') {
        // Handle /enrollments endpoint
        switch ($method) {
            case 'POST':
                handleEnrollUser();
                break;
            case 'GET':
                handleListEnrollments();
                break;
            default:
                sendError('Method not allowed', 405);
        }
    } elseif (preg_match('/^(\d+)$/', $endpoint, $matches)) {
        // Handle /enrollments/{id} endpoint
        $enrollmentId = $matches[1];
        switch ($method) {
            case 'PUT':
                handleUpdateEnrollment($enrollmentId);
                break;
            case 'DELETE':
                handleDropEnrollment($enrollmentId);
                break;
            default:
                sendError('Method not allowed', 405);
        }
    } else {
        sendError('Invalid enrollment endpoint', 404);
    }
} else {
    sendError('Invalid API path', 404);
}

/**
 * Handle user enrollment
 * POST /enrollments
 */
function handleEnrollUser() {
    $user = requireAuth();
    
    $data = getRequestData();
    validateRequired($data, ['course_id']);
    
    try {
        $pdo = getDBConnection();
        
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id, title FROM courses WHERE id = ?");
        $stmt->execute([$data['course_id']]);
        $course = $stmt->fetch();
        
        if (!$course) {
            sendError('Course not found', 404);
        }
        
        // Check if already enrolled
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
        $stmt->execute([$user['user_id'], $data['course_id']]);
        if ($stmt->fetch()) {
            sendError('Already enrolled in this course', 409);
        }
        
        // Insert enrollment
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (student_id, course_id, status) 
            VALUES (?, ?, ?)
        ");
        
        $status = $data['status'] ?? 'active';
        $stmt->execute([$user['user_id'], $data['course_id'], $status]);
        
        $enrollmentId = $pdo->lastInsertId();
        
        // Get enrollment details with course info
        $stmt = $pdo->prepare("
            SELECT e.*, c.title, c.course_code, c.credits, c.semester, c.year,
                   u.first_name, u.last_name as instructor_name
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            JOIN users u ON c.instructor_id = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$enrollmentId]);
        $enrollment = $stmt->fetch();
        
        // Create notification for instructor
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type) 
            VALUES (?, ?, ?, ?)
        ");
        $instructorId = $pdo->prepare("SELECT instructor_id FROM courses WHERE id = ?");
        $instructorId->execute([$data['course_id']]);
        $instructor = $instructorId->fetch();
        
        if ($instructor) {
            $stmt->execute([
                $instructor['instructor_id'],
                'New Student Enrollment',
                "A new student has enrolled in {$course['title']}",
                'info'
            ]);
        }
        
        sendResponse([
            'success' => true,
            'message' => 'Successfully enrolled in course',
            'enrollment' => $enrollment
        ], 201);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle enrollment listing
 * GET /enrollments
 */
function handleListEnrollments() {
    $user = requireAuth();
    
    try {
        $pdo = getDBConnection();
        
        // Get query parameters
        $studentId = $_GET['student_id'] ?? null;
        $courseId = $_GET['course_id'] ?? null;
        $status = $_GET['status'] ?? 'active';
        
        // Build query based on user role
        if ($user['role'] === 'admin') {
            // Admin can see all enrollments
            $sql = "
                SELECT e.*, c.title, c.course_code, c.credits, c.semester, c.year,
                       s.first_name as student_first_name, s.last_name as student_last_name,
                       s.username as student_username, s.email as student_email,
                       i.first_name as instructor_first_name, i.last_name as instructor_last_name
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                JOIN users s ON e.student_id = s.id
                JOIN users i ON c.instructor_id = i.id
            ";
            
            $conditions = [];
            $params = [];
            
            if ($studentId) {
                $conditions[] = "e.student_id = ?";
                $params[] = $studentId;
            }
            
            if ($courseId) {
                $conditions[] = "e.course_id = ?";
                $params[] = $courseId;
            }
            
            if ($status) {
                $conditions[] = "e.status = ?";
                $params[] = $status;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
        } elseif ($user['role'] === 'instructor') {
            // Instructor can see enrollments for their courses
            $sql = "
                SELECT e.*, c.title, c.course_code, c.credits, c.semester, c.year,
                       s.first_name as student_first_name, s.last_name as student_last_name,
                       s.username as student_username, s.email as student_email
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                JOIN users s ON e.student_id = s.id
                WHERE c.instructor_id = ?
            ";
            
            $params = [$user['user_id']];
            
            if ($courseId) {
                $sql .= " AND e.course_id = ?";
                $params[] = $courseId;
            }
            
            if ($status) {
                $sql .= " AND e.status = ?";
                $params[] = $status;
            }
            
        } else {
            // Student can only see their own enrollments
            $sql = "
                SELECT e.*, c.title, c.course_code, c.credits, c.semester, c.year,
                       i.first_name as instructor_first_name, i.last_name as instructor_last_name
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                JOIN users i ON c.instructor_id = i.id
                WHERE e.student_id = ?
            ";
            
            $params = [$user['user_id']];
            
            if ($status) {
                $sql .= " AND e.status = ?";
                $params[] = $status;
            }
        }
        
        $sql .= " ORDER BY e.enrolled_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $enrollments = $stmt->fetchAll();
        
        sendResponse([
            'success' => true,
            'enrollments' => $enrollments,
            'count' => count($enrollments)
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle enrollment update
 * PUT /enrollments/{id}
 */
function handleUpdateEnrollment($enrollmentId) {
    $user = requireAuth();
    
    try {
        $pdo = getDBConnection();
        
        // Check if enrollment exists and user has permission
        $stmt = $pdo->prepare("
            SELECT e.*, c.instructor_id 
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.id = ?
        ");
        $stmt->execute([$enrollmentId]);
        $enrollment = $stmt->fetch();
        
        if (!$enrollment) {
            sendError('Enrollment not found', 404);
        }
        
        // Check permissions
        $canUpdate = false;
        if ($user['role'] === 'admin') {
            $canUpdate = true;
        } elseif ($user['role'] === 'instructor' && $enrollment['instructor_id'] == $user['user_id']) {
            $canUpdate = true;
        } elseif ($user['role'] === 'student' && $enrollment['student_id'] == $user['user_id']) {
            $canUpdate = true;
        }
        
        if (!$canUpdate) {
            sendError('Insufficient permissions', 403);
        }
        
        $data = getRequestData();
        
        // Only allow status updates
        if (!isset($data['status'])) {
            sendError('Only status can be updated', 400);
        }
        
        $allowedStatuses = ['active', 'dropped', 'completed'];
        if (!in_array($data['status'], $allowedStatuses)) {
            sendError('Invalid status. Allowed: ' . implode(', ', $allowedStatuses), 400);
        }
        
        $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $enrollmentId]);
        
        // Get updated enrollment
        $stmt = $pdo->prepare("
            SELECT e.*, c.title, c.course_code
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.id = ?
        ");
        $stmt->execute([$enrollmentId]);
        $updatedEnrollment = $stmt->fetch();
        
        sendResponse([
            'success' => true,
            'message' => 'Enrollment updated successfully',
            'enrollment' => $updatedEnrollment
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Handle enrollment drop
 * DELETE /enrollments/{id}
 */
function handleDropEnrollment($enrollmentId) {
    $user = requireAuth();
    
    try {
        $pdo = getDBConnection();
        
        // Check if enrollment exists and user has permission
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE id = ?");
        $stmt->execute([$enrollmentId]);
        $enrollment = $stmt->fetch();
        
        if (!$enrollment) {
            sendError('Enrollment not found', 404);
        }
        
        // Check permissions
        $canDrop = false;
        if ($user['role'] === 'admin') {
            $canDrop = true;
        } elseif ($user['role'] === 'student' && $enrollment['student_id'] == $user['user_id']) {
            $canDrop = true;
        }
        
        if (!$canDrop) {
            sendError('Insufficient permissions', 403);
        }
        
        // Update status to dropped instead of deleting
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'dropped' WHERE id = ?");
        $stmt->execute([$enrollmentId]);
        
        sendResponse([
            'success' => true,
            'message' => 'Enrollment dropped successfully'
        ]);
        
    } catch (Exception $e) {
        sendError('Database error: ' . $e->getMessage(), 500);
    }
}
?>

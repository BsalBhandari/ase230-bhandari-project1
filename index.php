<?php
/**
 * ASE230 Project 1 - Main Entry Point
 * 
 * This is the main entry point for the application.
 * It handles routing and initializes the application.
 */

// Start session
session_start();

// Include configuration
require_once 'config/db.php';

// Test database connection
try {
    $pdo = getDBConnection();
    $dbStatus = "✅ Database connected successfully!";
    $dbError = null;
} catch (Exception $e) {
    $dbStatus = "❌ Database connection failed!";
    $dbError = $e->getMessage();
}

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Route handling
if (strpos($path, 'api') === 0) {
    // Handle API routes
    require_once 'api/index.php';
} else {
    switch ($path) {
        case '':
        case 'index.php':
            echo "<h1>Welcome to ASE230 Project 1</h1>";
            echo "<p>This is the main page of your application.</p>";
            echo "<hr>";
            echo "<h2>Database Status</h2>";
            echo "<p><strong>$dbStatus</strong></p>";
            if ($dbError) {
                echo "<p style='color: red;'>Error: $dbError</p>";
            }
            
            // Show database info if connected
            if (!$dbError) {
                echo "<h3>Database Information</h3>";
                echo "<ul>";
                echo "<li><strong>Database:</strong> " . DB_NAME . "</li>";
                echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
                echo "<li><strong>User:</strong> " . DB_USER . "</li>";
                echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
                echo "<li><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
                echo "</ul>";
                
                // Test a simple query
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
                    $result = $stmt->fetch();
                    echo "<p><strong>Users in database:</strong> " . $result['user_count'] . "</p>";
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as course_count FROM courses");
                    $result = $stmt->fetch();
                    echo "<p><strong>Courses in database:</strong> " . $result['course_count'] . "</p>";
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>Query test failed: " . $e->getMessage() . "</p>";
                }
                
                echo "<hr>";
                echo "<h3>API Endpoints</h3>";
                echo "<p><a href='/api' target='_blank'>View API Documentation</a></p>";
                echo "<p><strong>Available Endpoints:</strong></p>";
                echo "<ul>";
                echo "<li>POST /api/users/login - User authentication</li>";
                echo "<li>GET /api/users/profile - User profile (requires auth)</li>";
                echo "<li>POST /api/users/register - User registration</li>";
                echo "<li>POST /api/courses - Create course (requires auth)</li>";
                echo "<li>GET /api/courses - List courses</li>";
                echo "<li>PUT /api/courses/{id} - Update course (requires auth)</li>";
                echo "<li>POST /api/enrollments - Enroll in course (requires auth)</li>";
                echo "<li>GET /api/enrollments - List enrollments (requires auth)</li>";
                echo "</ul>";
            }
            break;
        
        default:
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
            break;
    }
}
?>

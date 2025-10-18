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

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Route handling
switch ($path) {
    case '':
    case 'index.php':
        echo "<h1>Welcome to ASE230 Project 1</h1>";
        echo "<p>This is the main page of your application.</p>";
        break;
    
    case 'api':
        echo "<h2>API Endpoints</h2>";
        echo "<p>API endpoints will be available here.</p>";
        break;
    
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        break;
}
?>

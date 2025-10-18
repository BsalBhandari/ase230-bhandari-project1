<?php
/**
 * Database Configuration
 * 
 * This file contains database connection settings.
 * Update these values according to your database setup.
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ase230_project1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Create database connection
 * 
 * @return PDO Database connection object
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please check your configuration.");
    }
}

// Test connection (optional - remove in production)
try {
    $pdo = getDBConnection();
    // Connection successful
} catch (Exception $e) {
    // Connection failed - handle appropriately
}
?>

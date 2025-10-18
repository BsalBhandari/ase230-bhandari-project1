<?php
/**
 * Simple test file to verify PHP is working
 */
echo "<h1>✅ PHP is Working!</h1>";
echo "<p>NGINX and PHP-FPM are properly configured.</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
?>

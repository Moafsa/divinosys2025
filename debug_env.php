<?php
// Debug script to check environment variables
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Environment Variables Debug</h1>";
echo "<h2>Database Variables:</h2>";

$db_vars = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_PORT'];
foreach ($db_vars as $var) {
    $value = getenv($var);
    echo "<p><strong>{$var}:</strong> " . ($value ? $value : '<span style="color:red">NOT SET</span>') . "</p>";
}

echo "<h2>All Environment Variables:</h2>";
echo "<pre>";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'DB_') === 0 || strpos($key, 'APP_') === 0 || strpos($key, 'SERVICE_') === 0) {
        echo "{$key} = {$value}\n";
    }
}
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
$server_vars = ['HTTP_HOST', 'HTTPS', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_FORWARDED_SSL', 'SERVER_PORT'];
foreach ($server_vars as $var) {
    $value = $_SERVER[$var] ?? 'not set';
    echo "{$var} = {$value}\n";
}
echo "</pre>";

echo "<h2>Test Database Connection:</h2>";
try {
    $host = getenv('DB_HOST') ?: 'db';
    $user = getenv('DB_USER') ?: 'divino';
    $pass = getenv('DB_PASS') ?: 'divino123';
    $db = getenv('DB_NAME') ?: 'divinosys';
    
    echo "<p>Attempting connection with:</p>";
    echo "<ul>";
    echo "<li>Host: {$host}</li>";
    echo "<li>User: {$user}</li>";
    echo "<li>Database: {$db}</li>";
    echo "</ul>";
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✓ Database connection successful!</p>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
}
?>

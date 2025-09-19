<?php
require_once "mvc/model/config.php";

echo "<h1>Environment Configuration Test</h1>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "APP_PROTOCOL: " . (getenv('APP_PROTOCOL') ?: 'not set') . "\n";
echo "FORCE_HTTPS: " . (getenv('FORCE_HTTPS') ?: 'not set') . "\n";
echo "IS_PRODUCTION: " . (getenv('IS_PRODUCTION') ?: 'not set') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ?: 'not set') . "\n";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "\n";
echo "APP_NAME: " . (getenv('APP_NAME') ?: 'not set') . "\n";
echo "APP_VERSION: " . (getenv('APP_VERSION') ?: 'not set') . "\n";
echo "SESSION_SECURE: " . (getenv('SESSION_SECURE') ?: 'not set') . "\n";
echo "SESSION_HTTPONLY: " . (getenv('SESSION_HTTPONLY') ?: 'not set') . "\n";
echo "SESSION_LIFETIME: " . (getenv('SESSION_LIFETIME') ?: 'not set') . "\n";
echo "UPLOAD_MAX_SIZE: " . (getenv('UPLOAD_MAX_SIZE') ?: 'not set') . "\n";
echo "UPLOAD_ALLOWED_TYPES: " . (getenv('UPLOAD_ALLOWED_TYPES') ?: 'not set') . "\n";
echo "TZ: " . (getenv('TZ') ?: 'not set') . "\n";
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'not set') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set') . "\n";
echo "HTTP_X_FORWARDED_SSL: " . ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'not set') . "\n";
echo "SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'not set') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'not set') . "\n";
echo "</pre>";

echo "<h2>Protocol Detection Logic:</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8081';

echo "<p>Host: <strong>{$host}</strong></p>";

// Get protocol from environment variables
$protocol = getenv('APP_PROTOCOL') ?: 'http';

// Check if we should force HTTPS
$force_https = getenv('FORCE_HTTPS') === 'true';
$is_production = getenv('IS_PRODUCTION') === 'true';

echo "<p>APP_PROTOCOL: <strong>{$protocol}</strong></p>";
echo "<p>FORCE_HTTPS: <strong>" . ($force_https ? 'YES' : 'NO') . "</strong></p>";
echo "<p>IS_PRODUCTION: <strong>" . ($is_production ? 'YES' : 'NO') . "</strong></p>";

// If force HTTPS is enabled, use HTTPS
if ($force_https) {
    $protocol = 'https';
} elseif ($is_production) {
    // If it's production, use HTTPS
    $protocol = 'https';
} else {
    // Check standard HTTPS indicators for other environments
    $https_indicators = [
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https',
        isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on',
        isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] === '443',
        isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443'
    ];
    
    if (in_array(true, $https_indicators, true)) {
        $protocol = 'https';
    }
}

echo "<p>Final Protocol: <strong style='color: green;'>{$protocol}</strong></p>";

echo "<h2>Config Class Test:</h2>";
$config = Config::getInstance();
echo "<p>Base URL: <strong>" . $config->getBaseUrl() . "</strong></p>";
echo "<p>URL Test: <strong>" . $config->url('test') . "</strong></p>";
echo "<p>Assets Test: <strong>" . $config->assets('css/style.css') . "</strong></p>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='" . $config->url('index.php') . "'>Go to Index</a></p>";
echo "<p><a href='" . $config->url('?view=Dashboard1') . "'>Go to Dashboard</a></p>";

echo "<h2>Configuration Summary:</h2>";
echo "<p><strong>Environment:</strong> " . (getenv('APP_ENV') ?: 'development') . "</p>";
echo "<p><strong>Protocol:</strong> {$protocol}</p>";
echo "<p><strong>Host:</strong> {$host}</p>";
echo "<p><strong>Full URL:</strong> {$protocol}://{$host}</p>";
?>

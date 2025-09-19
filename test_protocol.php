<?php
// Test protocol detection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Protocol Detection Test</h1>";

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
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';

// Check if this is a production domain that should use HTTPS
$production_domains = ['conext.click', 'coolify', 'app', 'divinosys'];
$is_production_domain = false;

foreach ($production_domains as $domain) {
    if (strpos($host, $domain) !== false) {
        $is_production_domain = true;
        break;
    }
}

echo "<p>Is Production Domain: " . ($is_production_domain ? 'YES' : 'NO') . "</p>";

$protocol = 'http';
if ($is_production_domain) {
    $protocol = 'https';
} else {
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

echo "<p>Detected Protocol: <strong>{$protocol}</strong></p>";
echo "<p>Generated URL: <strong>{$protocol}://{$host}/mvc/common/css/style.css</strong></p>";

echo "<h2>Current Page URL:</h2>";
echo "<p>Current URL: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$host}" . $_SERVER['REQUEST_URI'] . "</p>";
?>

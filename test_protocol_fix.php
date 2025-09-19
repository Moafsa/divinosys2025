<?php
require_once "mvc/model/config.php";

echo "<h1>Protocol Detection Test - FIXED</h1>";

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

// Check if this is a local development environment
$is_local = (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '192.168.') !== false ||
    strpos($host, '10.0.') !== false ||
    strpos($host, '172.') !== false
);

echo "<p>Host: <strong>{$host}</strong></p>";
echo "<p>Is Local: <strong>" . ($is_local ? 'YES' : 'NO') . "</strong></p>";

$protocol = 'http';
if ($is_local) {
    $protocol = 'http';
} else {
    // Check if this is a production domain that should use HTTPS
    $production_domains = ['conext.click', 'coolify', 'app', 'divinosys'];
    $is_production_domain = false;
    
    foreach ($production_domains as $domain) {
        if (strpos($host, $domain) !== false) {
            $is_production_domain = true;
            break;
        }
    }
    
    if ($is_production_domain) {
        $protocol = 'https';
    }
}

echo "<p>Detected Protocol: <strong style='color: green;'>{$protocol}</strong></p>";

echo "<h2>Config Class Test:</h2>";
$config = Config::getInstance();
echo "<p>Base URL: <strong>" . $config->getBaseUrl() . "</strong></p>";
echo "<p>URL Test: <strong>" . $config->url('test') . "</strong></p>";
echo "<p>Assets Test: <strong>" . $config->assets('css/style.css') . "</strong></p>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='" . $config->url('index.php') . "'>Go to Index</a></p>";
echo "<p><a href='" . $config->url('?view=Dashboard1') . "'>Go to Dashboard</a></p>";
?>

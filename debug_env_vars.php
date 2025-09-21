<?php
// Debug script to check environment variables
header('Content-Type: text/plain');

echo "=== ENVIRONMENT VARIABLES DEBUG ===\n\n";

// Check all relevant environment variables
$env_vars = [
    'APP_PROTOCOL',
    'APP_URL', 
    'FORCE_HTTPS',
    'IS_PRODUCTION',
    'APP_ENV',
    'DB_HOST',
    'DB_USER',
    'DB_NAME'
];

foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "getenv('{$var}'): " . ($value ?: 'NOT SET') . "\n";
}

echo "\n=== SERVER VARIABLES ===\n";
$server_vars = [
    'HTTP_HOST',
    'HTTPS',
    'HTTP_X_FORWARDED_PROTO',
    'HTTP_X_FORWARDED_SSL',
    'HTTP_X_FORWARDED_PORT',
    'SERVER_PORT',
    'REQUEST_SCHEME'
];

foreach ($server_vars as $var) {
    $value = $_SERVER[$var] ?? 'NOT SET';
    echo "\$_SERVER['{$var}']: {$value}\n";
}

echo "\n=== ENV VARIABLES (alternative method) ===\n";
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    echo "\$_ENV['{$var}']: {$value}\n";
}

echo "\n=== TESTING LOGIC ===\n";

// Test the same logic as config.php
$protocol = getenv('APP_PROTOCOL') ?: 'http';
$app_url = getenv('APP_URL');
$force_https = getenv('FORCE_HTTPS') === 'true';
$is_production = getenv('IS_PRODUCTION') === 'true';

echo "Initial protocol from APP_PROTOCOL: {$protocol}\n";
echo "APP_URL: " . ($app_url ?: 'NOT SET') . "\n";
echo "FORCE_HTTPS: " . ($force_https ? 'true' : 'false') . "\n";
echo "IS_PRODUCTION: " . ($is_production ? 'true' : 'false') . "\n";

if ($app_url) {
    $parsed_url = parse_url($app_url);
    echo "Parsed APP_URL scheme: " . ($parsed_url['scheme'] ?? 'NOT SET') . "\n";
    $protocol = $parsed_url['scheme'] ?? $protocol;
}

if ($force_https || $is_production) {
    $protocol = 'https';
    echo "Protocol forced to HTTPS due to FORCE_HTTPS or IS_PRODUCTION\n";
}

echo "Final protocol: {$protocol}\n";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = "{$protocol}://{$host}";
echo "Final base URL: {$baseUrl}\n";

echo "\n=== ALL ENV VARIABLES ===\n";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'APP_') === 0 || strpos($key, 'DB_') === 0 || strpos($key, 'FORCE_') === 0 || strpos($key, 'IS_') === 0) {
        echo "{$key}: {$value}\n";
    }
}
?>

<?php
// Simple debug script
header('Content-Type: text/plain');

echo "=== SIMPLE DEBUG ===\n\n";

// Test environment variables
echo "APP_URL: " . (getenv('APP_URL') ?: 'NOT SET') . "\n";
echo "APP_PROTOCOL: " . (getenv('APP_PROTOCOL') ?: 'NOT SET') . "\n";
echo "FORCE_HTTPS: " . (getenv('FORCE_HTTPS') ?: 'NOT SET') . "\n";
echo "IS_PRODUCTION: " . (getenv('IS_PRODUCTION') ?: 'NOT SET') . "\n";

echo "\n=== SERVER INFO ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'NOT SET') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'NOT SET') . "\n";

echo "\n=== FORCE HTTPS TEST ===\n";
$force_https = getenv('FORCE_HTTPS') === 'true';
$is_production = getenv('IS_PRODUCTION') === 'true';
echo "FORCE_HTTPS === 'true': " . ($force_https ? 'YES' : 'NO') . "\n";
echo "IS_PRODUCTION === 'true': " . ($is_production ? 'YES' : 'NO') . "\n";

echo "\n=== FINAL RESULT ===\n";
if ($force_https || $is_production) {
    echo "SHOULD USE HTTPS: YES\n";
    echo "FINAL URL: https://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n";
} else {
    echo "SHOULD USE HTTPS: NO\n";
    echo "FINAL URL: http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n";
}
?>

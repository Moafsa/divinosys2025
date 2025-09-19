<?php
require_once "mvc/model/config.php";

echo "<h1>Final Fix Test - Mixed Content Resolution</h1>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "APP_PROTOCOL: " . (getenv('APP_PROTOCOL') ?: 'not set') . "\n";
echo "FORCE_HTTPS: " . (getenv('FORCE_HTTPS') ?: 'not set') . "\n";
echo "IS_PRODUCTION: " . (getenv('IS_PRODUCTION') ?: 'not set') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ?: 'not set') . "\n";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "\n";
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'not set') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set') . "\n";
echo "SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'not set') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'not set') . "\n";
echo "</pre>";

echo "<h2>URL Generation Test:</h2>";
$config = Config::getInstance();

echo "<p><strong>Base URL:</strong> " . $config->getBaseUrl() . "</p>";
echo "<p><strong>URL Test:</strong> " . $config->url('test') . "</p>";
echo "<p><strong>Assets Test:</strong> " . $config->assets('css/style.css') . "</p>";

echo "<h2>Generated URLs (Should be relative):</h2>";
echo "<p><strong>CSS:</strong> " . assets('css/bootstrap.min.css') . "</p>";
echo "<p><strong>JS:</strong> " . assets('js/jquery.min.js') . "</p>";
echo "<p><strong>Image:</strong> " . assets('img/beer.png') . "</p>";
echo "<p><strong>URL:</strong> " . url('index.php') . "</p>";
echo "<p><strong>URL with view:</strong> " . url('?view=Dashboard1') . "</p>";

echo "<h2>Test Links (Should work without Mixed Content):</h2>";
echo "<p><a href='" . url('index.php') . "'>Go to Index</a></p>";
echo "<p><a href='" . url('?view=Dashboard1') . "'>Go to Dashboard</a></p>";

echo "<h2>HTML Test (Copy this to see if it loads without Mixed Content):</h2>";
echo "<pre>";
echo "&lt;link href='" . assets('css/bootstrap.min.css') . "' rel='stylesheet'&gt;\n";
echo "&lt;script src='" . assets('js/jquery.min.js') . "'&gt;&lt;/script&gt;\n";
echo "&lt;img src='" . assets('img/beer.png') . "' alt='Logo'&gt;\n";
echo "</pre>";

echo "<h2>Configuration Summary:</h2>";
echo "<p><strong>Environment:</strong> " . (getenv('APP_ENV') ?: 'development') . "</p>";
echo "<p><strong>Protocol:</strong> " . (getenv('APP_PROTOCOL') ?: 'http') . "</p>";
echo "<p><strong>Force HTTPS:</strong> " . (getenv('FORCE_HTTPS') ?: 'false') . "</p>";
echo "<p><strong>Is Production:</strong> " . (getenv('IS_PRODUCTION') ?: 'false') . "</p>";
echo "<p><strong>Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "</p>";

echo "<h2>Expected Result:</h2>";
echo "<p style='color: green;'><strong>✅ All URLs should be relative (no http:// or https://)</strong></p>";
echo "<p style='color: green;'><strong>✅ No Mixed Content errors should occur</strong></p>";
echo "<p style='color: green;'><strong>✅ System should work both locally and online</strong></p>";
?>

<?php
require_once "mvc/model/config.php";

echo "<h1>Test Assets Function</h1>";
echo "<p>assets('css/bootstrap.min.css'): " . assets('css/bootstrap.min.css') . "</p>";
echo "<p>url('mvc/common/css/bootstrap.min.css'): " . url('mvc/common/css/bootstrap.min.css') . "</p>";

echo "<h2>Environment Variables:</h2>";
echo "<p>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "</p>";
echo "<p>HTTPS: " . ($_SERVER['HTTPS'] ?? 'not set') . "</p>";
echo "<p>REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'not set') . "</p>";

echo "<h2>Config Debug:</h2>";
$config = Config::getInstance();
echo "<p>Base URL: " . $config->getBaseUrl() . "</p>";
?>

<?php
require_once dirname(__FILE__) . '/config.php';

session_start();
session_destroy();
unset($_SESSION);

$config = Config::getInstance();
header("Location: " . $config->url(''));
exit;
?>
<?php
// Prevent any output before headers
ob_start();

// Detect if this is a JSON endpoint
$is_json_endpoint = defined('IS_JSON_ENDPOINT') || 
    stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false || 
    stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false ||
    $_SERVER['REQUEST_METHOD'] === 'POST' ||
    $_SERVER['REQUEST_METHOD'] === 'DELETE' ||
    $_SERVER['REQUEST_METHOD'] === 'PUT';

// Configure error display based on endpoint type
if ($is_json_endpoint) {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Set default timezone
date_default_timezone_set('America/Sao_Paulo');

// Database connection settings - Use environment variables with fallbacks
function getEnvVar($name, $default = null) {
    // Try getenv() first
    $value = getenv($name);
    if ($value !== false) {
        return $value;
    }
    
    // Try $_ENV
    if (isset($_ENV[$name])) {
        return $_ENV[$name];
    }
    
    // Try $_SERVER
    if (isset($_SERVER[$name])) {
        return $_SERVER[$name];
    }
    
    return $default;
}

$servidor = getEnvVar('DB_HOST');
$usuario = getEnvVar('DB_USER');
$senha = getEnvVar('DB_PASS');
$dbname = getEnvVar('DB_NAME');

// Log the values being used (without password)
error_log("Database connection attempt:");
error_log("Host: {$servidor}");
error_log("User: {$usuario}");
error_log("Database: {$dbname}");
error_log("Password: " . (empty($senha) ? 'EMPTY' : 'SET'));

// Validate required database variables
if (!$servidor || !$usuario || !$dbname) {
    error_log("ERROR: Missing required database environment variables");
    error_log("Available env vars: " . print_r($_ENV, true));
    // Don't die here, but don't hardcode credentials either
    // The connection will fail gracefully and the system will show appropriate error
    error_log("Database connection will fail - environment variables not set");
}

// Debug: Print environment variables
error_log("=== DEBUG: Variáveis de Ambiente ===");
error_log("DB_HOST: " . ($servidor ?: 'não definido'));
error_log("DB_USER: " . ($usuario ?: 'não definido'));
error_log("DB_NAME: " . ($dbname ?: 'não definido'));
error_log("DB_PASS: " . (empty($senha) ? 'vazio' : 'definido'));

// Log connection attempt
error_log("=== Iniciando nova tentativa de conexão ===");
error_log("Servidor: " . $servidor);
error_log("Banco: " . $dbname);

// Database connection with timeout settings
global $conn;

// Only attempt connection if we have valid credentials
if ($servidor && $usuario && $dbname) {
    $conn = new mysqli($servidor, $usuario, $senha, $dbname);

    // Set connection timeout
    if ($conn) {
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        $conn->options(MYSQLI_OPT_READ_TIMEOUT, 30);
    }

    // If connection fails, display error message
    if ($conn->connect_error) {
        error_log("Main connection failure: " . $conn->connect_error);
        $conn = null; // Set to null so other parts can check
    } else {
    // Set charset to UTF-8
    mysqli_set_charset($conn, "utf8");

    // Set timezone to UTC in MySQL and handle conversion in PHP
    try {
        mysqli_query($conn, "SET time_zone = '+00:00'");
        error_log("Timezone do MySQL configurado para UTC com sucesso!");
    } catch (Exception $e) {
        error_log("Aviso: Usando timezone padrão do MySQL: " . $e->getMessage());
    }

    // Test if we can actually execute queries
    $test_query = mysqli_query($conn, "SELECT 1");
    if (!$test_query) {
        throw new Exception("Erro ao executar query de teste: " . mysqli_error($conn));
    }
    error_log("Query de teste executada com sucesso!");

    // Define constant to indicate successful connection
    define('DB_CONNECTION_SUCCESS', true);
    error_log("=== Conexão estabelecida com sucesso! ===");
    }
} else {
    error_log("Database connection skipped - missing environment variables");
    $conn = null;
}

// Function to safely close database connection
function closeConnection() {
    global $conn;
    static $already_closed = false;
    
    if (!$already_closed && isset($conn) && $conn instanceof mysqli && !($conn->connect_errno)) {
        try {
            @mysqli_close($conn);
            $already_closed = true;
            unset($conn);
        } catch (Exception $e) {
            error_log("Erro ao fechar conexão: " . $e->getMessage());
        }
    }
}
?>
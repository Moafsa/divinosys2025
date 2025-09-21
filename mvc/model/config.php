<?php
// Prevent any output before headers
if (ob_get_level()) ob_end_clean();
ob_start();

// Configurações do Sistema
class Config {
    private static $instance = null;
    private $config = [];
    private $baseUrl;

    private function __construct() {
        // Detecta o ambiente automaticamente
        $this->detectEnvironment();
        
        // Configurações do banco de dados - Use environment variables only
        $this->config['db'] = [
            'host' => getenv('DB_HOST'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS'),
            'name' => getenv('DB_NAME')
        ];

        // Validate required database environment variables
        if (!$this->config['db']['host'] || !$this->config['db']['user'] || 
            !$this->config['db']['pass'] || !$this->config['db']['name']) {
            error_log("ERROR: Missing required database environment variables in config.php");
            // Set empty values - connection will handle the error gracefully
            $this->config['db'] = [
                'host' => null,
                'user' => null, 
                'pass' => null,
                'name' => null
            ];
        }

        // Configurações de URL
        $this->baseUrl = $this->detectBaseUrl();
        $this->config['assets_url'] = '/assets';
        
        // Log para debug
        error_log("Base URL detectada: " . $this->baseUrl);
    }

    private function detectEnvironment() {
        $server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $this->config['environment'] = ($server_name === 'localhost' || $server_name === '127.0.0.1') 
            ? 'development' 
            : 'production';
    }

    private function detectBaseUrl() {
        // Get host from environment or server
        $app_url = getenv('APP_URL');
        if ($app_url) {
            $parsed_url = parse_url($app_url);
            $host = $parsed_url['host'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
            if (isset($parsed_url['port'])) {
                $host .= ':' . $parsed_url['port'];
            }
        } else {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        }
        
        // Determine protocol - prioritize environment variables
        $protocol = 'http'; // Default
        
        // Check environment variables first - try multiple methods
        $force_https = (getenv('FORCE_HTTPS') === 'true') || ($_ENV['FORCE_HTTPS'] ?? '') === 'true';
        $is_production = (getenv('IS_PRODUCTION') === 'true') || ($_ENV['IS_PRODUCTION'] ?? '') === 'true';
        $app_protocol = getenv('APP_PROTOCOL') ?: ($_ENV['APP_PROTOCOL'] ?? '');
        
        // Additional debug for environment variables
        error_log("=== ENV VARIABLES DEBUG ===");
        error_log("getenv('FORCE_HTTPS'): " . (getenv('FORCE_HTTPS') ?: 'NOT SET'));
        error_log("\$_ENV['FORCE_HTTPS']: " . ($_ENV['FORCE_HTTPS'] ?? 'NOT SET'));
        error_log("getenv('IS_PRODUCTION'): " . (getenv('IS_PRODUCTION') ?: 'NOT SET'));
        error_log("\$_ENV['IS_PRODUCTION']: " . ($_ENV['IS_PRODUCTION'] ?? 'NOT SET'));
        error_log("getenv('APP_PROTOCOL'): " . (getenv('APP_PROTOCOL') ?: 'NOT SET'));
        error_log("\$_ENV['APP_PROTOCOL']: " . ($_ENV['APP_PROTOCOL'] ?? 'NOT SET'));
        error_log("Final force_https: " . ($force_https ? 'true' : 'false'));
        error_log("Final is_production: " . ($is_production ? 'true' : 'false'));
        
        // If APP_URL is set with https scheme, use it
        if ($app_url && isset($parsed_url['scheme']) && $parsed_url['scheme'] === 'https') {
            $protocol = 'https';
        }
        // If APP_PROTOCOL is explicitly set to https, use it
        elseif ($app_protocol === 'https') {
            $protocol = 'https';
        }
        // If FORCE_HTTPS is true, force HTTPS
        elseif ($force_https) {
            $protocol = 'https';
        }
        // If IS_PRODUCTION is true, force HTTPS
        elseif ($is_production) {
            $protocol = 'https';
        }
        // Otherwise, check standard HTTPS indicators
        else {
            $https_indicators = [
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https',
                isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on',
                isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] === '443',
                isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443',
                isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https'
            ];
            
            if (in_array(true, $https_indicators, true)) {
                $protocol = 'https';
            }
        }
        
        // Build base URL
        $baseUrl = "{$protocol}://{$host}";
        
        // Debug logs
        error_log("=== CONFIG DEBUG START ===");
        error_log("APP_URL: " . ($app_url ?: 'not set'));
        error_log("APP_PROTOCOL: " . ($app_protocol ?: 'not set'));
        error_log("FORCE_HTTPS: " . ($force_https ? 'YES' : 'NO'));
        error_log("IS_PRODUCTION: " . ($is_production ? 'YES' : 'NO'));
        error_log("HTTPS Server Var: " . ($_SERVER['HTTPS'] ?? 'not set'));
        error_log("X-Forwarded-Proto: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set'));
        error_log("Final Protocol: {$protocol}");
        error_log("Host detected: {$host}");
        error_log("Base URL: {$baseUrl}");
        error_log("=== CONFIG DEBUG END ===");
        
        return $baseUrl;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get($key = null) {
        $instance = self::getInstance();
        if ($key === null) {
            return $instance->config;
        }
        return $instance->config[$key] ?? null;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function url($path = '') {
        // Remove barras iniciais e finais
        $path = trim($path, '/');
        
        // Se o caminho estiver vazio, retorna apenas a barra
        if (empty($path)) {
            return '/';
        }
        
        // Use absolute URL with current protocol to avoid Mixed Content issues
        $url = $this->baseUrl . '/' . $path;
        
        // Log para debug
        error_log("URL Gerada para {$path}: {$url}");
        
        return $url;
    }

    public function redirect($path = '') {
        header('Location: ' . $this->url($path));
        exit;
    }

    // URLs específicas do sistema
    public function getDashboardUrl() {
        return $this->url('index.php?view=Dashboard1');
    }

    public function getConfigUrl() {
    return $this->url('mvc/model/configuracao.php');
    }

    public function getLoginUrl() {
        return $this->url('index.php');
    }

    public function getAssetsUrl($path = '') {
        $path = trim($path, '/');
        // Use absolute URL with current protocol to avoid Mixed Content issues
        return $this->baseUrl . '/mvc/common/' . $path;
    }

    public static function assets($path = '') {
        return self::getInstance()->getAssetsUrl($path);
    }
}

// Função global para facilitar o uso
if (!function_exists('url')) {
    function url($path = '') {
        return Config::getInstance()->url($path);
    }
}

// Função global para assets
if (!function_exists('assets')) {
    function assets($path = '') {
        return Config::getInstance()->assets($path);
    }
}

// Inicializa a configuração
Config::getInstance();
?> 
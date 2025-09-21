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
        // Host (domain) with port - get from environment or server
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_URL') ? parse_url(getenv('APP_URL'), PHP_URL_HOST) . (parse_url(getenv('APP_URL'), PHP_URL_PORT) ? ':' . parse_url(getenv('APP_URL'), PHP_URL_PORT) : '') : 'localhost');
        
        // Get protocol from environment variables
        $protocol = getenv('APP_PROTOCOL') ?: 'http';
        
        // Check if we should force HTTPS
        $force_https = getenv('FORCE_HTTPS') === 'true';
        $is_production = getenv('IS_PRODUCTION') === 'true';
        
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
        
        // Base path
        $baseUrl = "{$protocol}://{$host}";
        
        // Debug logs
        error_log("Environment: " . $this->config['environment']);
        error_log("Force HTTPS: " . ($force_https ? 'YES' : 'NO'));
        error_log("Is Production: " . ($is_production ? 'YES' : 'NO'));
        error_log("HTTPS Server Var: " . ($_SERVER['HTTPS'] ?? 'not set'));
        error_log("X-Forwarded-Proto: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set'));
        error_log("X-Forwarded-SSL: " . ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'not set'));
        error_log("Server Port: " . ($_SERVER['SERVER_PORT'] ?? 'not set'));
        error_log("Protocol: {$protocol}");
        error_log("Host detected: {$host}");
        error_log("Base URL: {$baseUrl}");
        
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
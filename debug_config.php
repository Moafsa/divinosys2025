<?php
// Script de debug para verificar configurações
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug de Configuração</h1>";

// Testar carregamento do config.php
echo "<h2>1. Testando config.php</h2>";
try {
    require_once 'mvc/model/config.php';
    $config = Config::getInstance();
    echo "<p style='color: green;'>✓ Config.php carregado com sucesso</p>";
    
    echo "<h3>URLs geradas:</h3>";
    echo "<p>Base URL: " . $config->getBaseUrl() . "</p>";
    echo "<p>Assets URL (css/style.css): " . $config->getAssetsUrl('css/style.css') . "</p>";
    echo "<p>URL (index.php): " . $config->url('index.php') . "</p>";
    echo "<p>URL (mvc/model/login.php): " . $config->url('mvc/model/login.php') . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro ao carregar config.php: " . $e->getMessage() . "</p>";
}

// Testar variáveis de ambiente
echo "<h2>2. Variáveis de Ambiente</h2>";
echo "<p>APP_PROTOCOL: " . (getenv('APP_PROTOCOL') ?: 'não definido') . "</p>";
echo "<p>FORCE_HTTPS: " . (getenv('FORCE_HTTPS') ?: 'não definido') . "</p>";
echo "<p>IS_PRODUCTION: " . (getenv('IS_PRODUCTION') ?: 'não definido') . "</p>";
echo "<p>APP_URL: " . (getenv('APP_URL') ?: 'não definido') . "</p>";

// Testar variáveis do servidor
echo "<h2>3. Variáveis do Servidor</h2>";
echo "<p>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'não definido') . "</p>";
echo "<p>HTTPS: " . ($_SERVER['HTTPS'] ?? 'não definido') . "</p>";
echo "<p>HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'não definido') . "</p>";
echo "<p>SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'não definido') . "</p>";
echo "<p>REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'não definido') . "</p>";

// Testar funções globais
echo "<h2>4. Funções Globais</h2>";
if (function_exists('url')) {
    echo "<p style='color: green;'>✓ Função url() existe</p>";
    echo "<p>url('index.php'): " . url('index.php') . "</p>";
    echo "<p>url('mvc/model/login.php'): " . url('mvc/model/login.php') . "</p>";
} else {
    echo "<p style='color: red;'>✗ Função url() não existe</p>";
}

if (function_exists('assets')) {
    echo "<p style='color: green;'>✓ Função assets() existe</p>";
    echo "<p>assets('css/style.css'): " . assets('css/style.css') . "</p>";
} else {
    echo "<p style='color: red;'>✗ Função assets() não existe</p>";
}

// Testar se há conflitos
echo "<h2>5. Verificação de Conflitos</h2>";
$files_to_check = [
    'mvc/model/config.php',
    'mvc/model/configuracao.php',
    'mvc/config/database.php',
    'config/database.php',
    'mvc/model/Database.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p>✓ Arquivo existe: {$file}</p>";
    } else {
        echo "<p>✗ Arquivo não existe: {$file}</p>";
    }
}
?>

<?php
// Este arquivo é incluído em todas as páginas de visualização
// para garantir que a conexão com o banco de dados esteja disponível

// Garantir que não há saída antes dos headers
if (headers_sent($filename, $linenum)) {
    // Log do erro
    error_log("Headers já foram enviados em $filename:$linenum");
    
    if (defined('IS_JSON_ENDPOINT') || 
        stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false || 
        stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno do servidor: headers já foram enviados'
        ]);
    } else {
        echo "Erro interno do servidor. Por favor, tente novamente.";
    }
    exit;
}

// Verificar se estamos em um endpoint JSON
$is_json_endpoint = defined('IS_JSON_ENDPOINT') || 
    stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false || 
    stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;

// Se for um endpoint JSON, garantir que não haverá saída HTML
if ($is_json_endpoint) {
    // Limpar qualquer saída anterior e iniciar novo buffer
    while (ob_get_level()) ob_end_clean();
    ob_start();
    
    // Desabilitar exibição de erros
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    
    // Definir headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Headers CORS para requisições AJAX
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
    
    // Tratar preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
} else {
    // Para páginas normais, permitir exibição de erros em desenvolvimento
    $config = Config::get();
    $is_development = isset($config['environment']) && $config['environment'] === 'development';
    
    if ($is_development) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
    }
}

// Garantir que a sessão está iniciada apenas para páginas normais
if (!$is_json_endpoint && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir arquivo de configuração
require_once(__DIR__ . "/../model/config.php");

try {
    // Usar a conexão global do arquivo conexao.php
    require_once(__DIR__ . "/../model/conexao.php");
    
    // Verificar se a conexão foi estabelecida
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Conexão com banco de dados não estabelecida");
    }

} catch (Exception $e) {
    // Log do erro
    error_log("Erro de conexão em " . __FILE__ . ": " . $e->getMessage());
    
    if ($is_json_endpoint) {
        // Limpar qualquer saída anterior
        if (ob_get_level()) ob_end_clean();
        
        // Retornar erro em formato JSON
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()
        ]);
        exit;
    } else {
        // Mensagem amigável para o usuário em HTML
        $_SESSION['msg'] = "<div class='alert alert-danger'>Erro de conexão com o banco de dados. Por favor, tente novamente mais tarde.</div>";
        header("Location: " . Config::getInstance()->getDashboardUrl());
        exit;
    }
}

// Se for um endpoint JSON, manter o buffer aberto para controle posterior
if (!$is_json_endpoint && ob_get_level()) {
    ob_end_clean();
}
?> 
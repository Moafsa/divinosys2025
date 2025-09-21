<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/php_errors.log');

// Log directory information
error_log("Current directory: " . __DIR__);
error_log("Document root: " . $_SERVER['DOCUMENT_ROOT']);

// Limpar qualquer output anterior e iniciar buffer
if (ob_get_level()) ob_end_clean();
ob_start();

// Configurações iniciais
ini_set('display_errors', 1);
ini_set('default_charset', 'UTF-8');
error_reporting(E_ALL);

// Definir constantes
define('DEFAULT_VIEW', 'Dashboard1');
define('SYSTEM_CLASS_PATH', 'mvc/classes/system.class.php');
define('ROOT_PATH', dirname(__FILE__));

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir arquivos necessários
require_once ROOT_PATH . "/mvc/model/config.php";

// DEBUG MODE - Add ?debug=1 to URL to see environment variables
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    header('Content-Type: text/plain');
    echo "=== ENVIRONMENT DEBUG ===\n\n";
    
    // Check environment variables
    $env_vars = ['APP_URL', 'APP_PROTOCOL', 'FORCE_HTTPS', 'IS_PRODUCTION', 'APP_ENV'];
    foreach ($env_vars as $var) {
        $value = getenv($var);
        echo "getenv('{$var}'): " . ($value ?: 'NOT SET') . "\n";
    }
    
    echo "\n=== SERVER VARIABLES ===\n";
    $server_vars = ['HTTP_HOST', 'HTTPS', 'REQUEST_SCHEME', 'HTTP_X_FORWARDED_PROTO'];
    foreach ($server_vars as $var) {
        $value = $_SERVER[$var] ?? 'NOT SET';
        echo "\$_SERVER['{$var}']: {$value}\n";
    }
    
    echo "\n=== CONFIG TEST ===\n";
    try {
        $config = Config::getInstance();
        echo "Config Base URL: " . $config->getBaseUrl() . "\n";
        echo "Config Assets URL: " . $config->getAssetsUrl() . "\n";
    } catch (Exception $e) {
        echo "Config Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== LOGIC TEST ===\n";
    $force_https = getenv('FORCE_HTTPS') === 'true';
    $is_production = getenv('IS_PRODUCTION') === 'true';
    $app_url = getenv('APP_URL');
    
    echo "FORCE_HTTPS: " . ($force_https ? 'true' : 'false') . "\n";
    echo "IS_PRODUCTION: " . ($is_production ? 'true' : 'false') . "\n";
    echo "APP_URL: " . ($app_url ?: 'NOT SET') . "\n";
    
    if ($force_https || $is_production) {
        echo "RESULT: Should use HTTPS\n";
        echo "FINAL URL: https://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n";
    } else {
        echo "RESULT: Should use HTTP\n";
        echo "FINAL URL: http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n";
    }
    
    exit();
}

// Tentar conectar ao banco, mas não falhar se não conseguir
try {
    require_once ROOT_PATH . "/mvc/model/conexao.php";
    
    if (isset($conn) && $conn instanceof mysqli) {
        // Definir o charset para UTF-8
        if (!mysqli_set_charset($conn, "utf8")) {
            error_log("Erro ao definir charset UTF-8");
        }

        // Consultar a cor do sistema usando prepared statement
        $select_table = "SELECT cor FROM cor WHERE id = ?";
        $stmt = mysqli_prepare($conn, $select_table);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $id = 1;
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $_SESSION['cor'] = ($row = mysqli_fetch_assoc($result)) ? strtolower($row['cor']) : 'danger';
            } else {
                $_SESSION['cor'] = 'danger';
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['cor'] = 'danger';
        }
    } else {
        $_SESSION['cor'] = 'danger';
    }
    
} catch (Exception $e) {
    error_log("Erro no index.php: " . $e->getMessage());
    $_SESSION['cor'] = 'danger';
}

// Verificar se é uma requisição de login
$is_login_request = empty($_GET['view']);

// Verificar se o usuário está logado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // Se não estiver logado e não for uma requisição de login, mostrar o formulário
    if (!$is_login_request) {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Acesso não autorizado! Faça login primeiro.</div>";
        header("Location: index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Divinosys 1.0</title>

  <!-- Bootstrap CSS -->
  <link href="<?php echo str_replace('http://', 'https://', assets('css/bootstrap.min.css')); ?>" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="<?php echo str_replace('http://', 'https://', assets('vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet">
  <!-- Custom Login CSS -->
  <link href="<?php echo str_replace('http://', 'https://', assets('css/login-style.css')); ?>" rel="stylesheet"/>
  <link rel="shortcut icon" href="<?php echo str_replace('http://', 'https://', assets('img/beer.png')); ?>">
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-logo">
        <h1>Divinosys 1.0</h1>
        <p>Sistema de Gestão de Pedidos</p>
      </div>
      
  <form method="POST" action="<?php echo str_replace('http://', 'https://', url('mvc/model/login.php')); ?>">
        <div class="form-group">
          <label for="login">Login</label>
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" class="form-control" name="login" id="login" placeholder="Digite seu login" required>
          </div>
        </div>
        
        <div class="form-group">
          <label for="senha">Senha</label>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" name="senha" id="senha" placeholder="Digite sua senha" required>
          </div>
        </div>

        <div class="text-center" id="mensagem">
          <?php 
          if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
          }
          ?>
        </div>

        <button type="submit" class="btn btn-login">
          Entrar
        </button>
      </form>

      <div class="login-links">
        <a href="<?php echo str_replace('http://', 'https://', url('recuperar_senha.php')); ?>">Esqueceu a senha?</a>
        <br>
        <a href="<?php echo str_replace('http://', 'https://', url('cadastrar_administrador.php')); ?>">Cadastrar Administrador</a>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="<?php echo str_replace('http://', 'https://', assets('js/jquery-3.4.0.min.js')); ?>"></script>
  <script src="<?php echo str_replace('http://', 'https://', assets('js/bootstrap.bundle.min.js')); ?>"></script>
</body>
</html>

<?php 
} else {
    try {
        // Usuário logado, carrega o sistema
        if (!file_exists(SYSTEM_CLASS_PATH)) {
            throw new Exception("Arquivo do sistema não encontrado: " . SYSTEM_CLASS_PATH);
        }
        
        require_once(SYSTEM_CLASS_PATH);

        // Validar e sanitizar o parâmetro view
        $view = DEFAULT_VIEW;
        if (isset($_GET['view'])) {
            $allowedViews = require_once(ROOT_PATH . "/mvc/config/views.php");
            $requestedView = trim(filter_var($_GET['view'], FILTER_SANITIZE_STRING));
            if (in_array($requestedView, $allowedViews)) {
                $view = $requestedView;
            }
        }

        $system = new System($view);
        
    } catch (Exception $e) {
        error_log("Erro ao carregar sistema: " . $e->getMessage());
        header("Location: " . url('error.php?msg=' . urlencode($e->getMessage())));
        exit;
    }
}
?>
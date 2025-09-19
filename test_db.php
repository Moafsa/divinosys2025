<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste do Sistema</h1>";

try {
    // Use environment variables for database connection
    $host = getenv('DB_HOST') ?: 'db';
    $user = getenv('DB_USER') ?: 'divino';
    $pass = getenv('DB_PASS') ?: 'divino123';
    $db = getenv('DB_NAME') ?: 'divinosys';
    
    $conn = mysqli_connect($host, $user, $pass, $db);
    echo "<p style='color: green'>✓ Conexão com banco de dados OK!</p>";
    
    $tables = mysqli_query($conn, "SHOW TABLES");
    if ($tables) {
        echo "<h2>Tabelas encontradas:</h2><ul>";
        while ($table = mysqli_fetch_array($tables)) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange'>⚠ Nenhuma tabela encontrada</p>";
    }
    
    mysqli_close($conn);
} catch (Exception $e) {
    echo "<p style='color: red'>✗ Erro: " . $e->getMessage() . "</p>";
}

phpinfo(); 
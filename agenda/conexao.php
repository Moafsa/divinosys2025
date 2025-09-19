<?php
	// Use environment variables for database connection
	$servidor = getenv('DB_HOST') ?: 'db';
	$usuario = getenv('DB_USER') ?: 'root';
	$senha = getenv('DB_PASS') ?: '';
	$dbname = getenv('DB_NAME') ?: 'divinosys';
	
	// Validate required environment variables
	if (!$servidor || !$usuario || !$dbname) {
		die("Database configuration error. Please check environment variables.");
	}
	
	//Criar a conexao
	$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);
	
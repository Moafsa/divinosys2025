<?php

class NetworkUtils {
    public static function getLocalIP() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $output = [];
            exec('ipconfig', $output);
            foreach ($output as $line) {
                if (strpos($line, 'IPv4') !== false) {
                    if (preg_match('/192\.168\.\d+\.\d+/', $line, $matches)) {
                        return $matches[0];
                    }
                }
            }
        } else {
            // Linux - usa ip addr para pegar o IP
            $output = [];
            exec('ip addr | grep "inet " | grep -v "127.0.0.1"', $output);
            foreach ($output as $line) {
                if (preg_match('/inet\s+(\d+\.\d+\.\d+\.\d+)/', $line, $matches)) {
                    if (strpos($matches[1], '192.168.') === 0) {
                        return $matches[1];
                    }
                }
            }
            // Se não encontrou IP 192.168.*, pega o primeiro IP não-localhost
            foreach ($output as $line) {
                if (preg_match('/inet\s+(\d+\.\d+\.\d+\.\d+)/', $line, $matches)) {
                    return $matches[1];
                }
            }
        }
        
        // Fallback para o IP do servidor
        return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }

    public static function generateAccessURL($type = 'default') {
        $ip = self::getLocalIP();
        $port = $_SERVER['SERVER_PORT'] ?? '8080';
        
        // Detect protocol based on environment
        $protocol = 'http';
        $host = $_SERVER['HTTP_HOST'] ?? "{$ip}:{$port}";
        
        // Check if this is a production domain that should use HTTPS
        $production_domains = ['conext.click', 'coolify', 'app', 'divinosys'];
        $is_production_domain = false;
        
        foreach ($production_domains as $domain) {
            if (strpos($host, $domain) !== false) {
                $is_production_domain = true;
                break;
            }
        }
        
        // Force HTTPS for production domains
        if ($is_production_domain) {
            $protocol = 'https';
        } else {
            // Check standard HTTPS indicators for local development
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
        
        // Generate specific URLs based on user type
        switch ($type) {
            case 'kitchen':
                return "{$protocol}://{$host}/?view=kitchen";
            case 'waiter':
                return "{$protocol}://{$host}/?view=waiter";
            case 'cashier':
                return "{$protocol}://{$host}/?view=Dashboard1";
            default:
                return "{$protocol}://{$host}/";
        }
    }
} 
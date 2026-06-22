<?php
// ================================================================
// Configuración global de la aplicación
// ================================================================

define('APP_NAME',    'Sistema de Control de Agencias de Loterías');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    'http://localhost/loterias');
define('ROOT_PATH',   dirname(__DIR__));

// Zona horaria
date_default_timezone_set('America/Caracas');

// Mostrar errores solo en desarrollo
define('ENV', 'development'); // 'production' para producción

if (ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// Configuración de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
session_start();

// Autoload de clases
spl_autoload_register(function (string $class): void {
    $paths = [
        ROOT_PATH . '/models/'      . $class . '.php',
        ROOT_PATH . '/controllers/' . $class . '.php',
        ROOT_PATH . '/helpers/'     . $class . '.php',
        ROOT_PATH . '/reports/'     . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Vendor (PhpSpreadsheet, etc.)
$autoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

require_once ROOT_PATH . '/config/database.php';

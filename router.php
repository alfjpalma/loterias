<?php
/**
 * Router simple — parsea la URL y despacha al controlador/acción
 * URL format: /loterias/controller/action/id
 */

// Obtener la URI sin el BASE_PATH
$basePath = parse_url(BASE_URL, PHP_URL_PATH); // /loterias
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri      = '/' . ltrim(str_replace($basePath, '', $uri), '/');
$uri      = rtrim($uri, '/') ?: '/';

$method = strtoupper($_SERVER['REQUEST_METHOD']);

// Parsear segmentos
$parts = array_values(array_filter(explode('/', $uri)));
$seg0  = $parts[0] ?? '';
$seg1  = $parts[1] ?? '';
$seg2  = isset($parts[2]) ? (int)$parts[2] : 0;

// ================================================================
// Tabla de rutas
// ================================================================
$routes = [
    // AUTH
    ['GET',  '',         'AuthController',        'login'],
    ['GET',  'login',    'AuthController',        'login'],
    ['POST', 'login',    'AuthController',        'doLogin'],
    ['POST', 'logout',   'AuthController',        'logout'],

    // DASHBOARD
    ['GET',  'dashboard', 'DashboardController',  'index'],

    // AGENCIAS
    ['GET',  'agencias',          'AgenciasController', 'index'],
    ['GET',  'agencias/create',   'AgenciasController', 'create'],
    ['POST', 'agencias/store',    'AgenciasController', 'store'],
    ['GET',  'agencias/edit',     'AgenciasController', 'edit'],      // /edit/{id}
    ['POST', 'agencias/update',   'AgenciasController', 'update'],    // /update/{id}
    ['POST', 'agencias/delete',   'AgenciasController', 'delete'],    // /delete/{id}
    ['POST', 'agencias/toggle',   'AgenciasController', 'toggleEstado'], // /toggle/{id}

    // TAQUILLAS
    ['GET',  'taquillas',         'TaquillasController', 'index'],
    ['GET',  'taquillas/create',  'TaquillasController', 'create'],
    ['POST', 'taquillas/store',   'TaquillasController', 'store'],
    ['GET',  'taquillas/edit',    'TaquillasController', 'edit'],
    ['POST', 'taquillas/update',  'TaquillasController', 'update'],
    ['POST', 'taquillas/delete',  'TaquillasController', 'delete'],

    // SISTEMAS
    ['GET',  'sistemas',          'SistemasController', 'index'],
    ['GET',  'sistemas/create',   'SistemasController', 'create'],
    ['POST', 'sistemas/store',    'SistemasController', 'store'],
    ['GET',  'sistemas/edit',     'SistemasController', 'edit'],
    ['POST', 'sistemas/update',   'SistemasController', 'update'],
    ['POST', 'sistemas/delete',   'SistemasController', 'delete'],

    // USUARIOS
    ['GET',  'usuarios',          'UsuariosController', 'index'],
    ['GET',  'usuarios/create',   'UsuariosController', 'create'],
    ['POST', 'usuarios/store',    'UsuariosController', 'store'],
    ['GET',  'usuarios/edit',     'UsuariosController', 'edit'],
    ['POST', 'usuarios/update',   'UsuariosController', 'update'],
    ['POST', 'usuarios/delete',   'UsuariosController', 'delete'],

    // VENTAS
    ['GET',  'ventas',            'VentasController',   'index'],
    ['GET',  'ventas/form',       'VentasController',   'form'],
    ['POST', 'ventas/store',      'VentasController',   'store'],

    // CUADRES
    ['GET',  'cuadres',           'CuadresController',  'index'],
    ['GET',  'cuadres/form',      'CuadresController',  'form'],
    ['POST', 'cuadres/store',     'CuadresController',  'store'],
    ['POST', 'cuadres/delete',    'CuadresController',  'delete'],

    // CONCILIACIÓN
    ['GET',  'conciliacion',      'ConciliacionController', 'index'],

    // REPORTES
    ['GET',  'reportes',          'ReportesController', 'index'],
    ['GET',  'reportes/generar',  'ReportesController', 'generar'],
];

// Normalizar la URI para matching (ej: /agencias/edit/3 → agencias/edit)
$routeKey = $seg2 > 0 ? $seg0 . '/' . $seg1 : ($seg1 ? $seg0 . '/' . $seg1 : $seg0);

foreach ($routes as [$routeMethod, $routePath, $controller, $action]) {
    if ($routeMethod === $method && $routePath === $routeKey) {
        $ctrl = new $controller();
        $ctrl->$action($seg2);
        exit;
    }
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="text-center">
<h1 class="display-1 text-muted">404</h1>
<p class="lead">Página no encontrada</p>
<a href="' . BASE_URL . '/dashboard" class="btn btn-primary">Ir al inicio</a>
</div></body></html>';

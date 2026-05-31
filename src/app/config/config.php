<?php
// Arrancamos el motor de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CARGAMOS LA BASE DE DATOS
require_once __DIR__ . '/db.php'; 

// =================================================================
// 1. CONSTANTES DE RUTAS (Para el Navegador / Cloudflare)
// =================================================================

// Usamos el dominio público para que las rutas funcionen vía Cloudflare
define('BASE_URL', 'https://paideia.cloud/');

// Definimos las rutas basadas en el dominio público
define('RUTA_CSS', BASE_URL . 'css/');
define('RUTA_JS', BASE_URL . 'js/');
define('RUTA_VISTAS', BASE_URL . 'vista/');
define('RUTA_IMAGENES', BASE_URL . 'assets/img/');
define('RUTA_INICIO', BASE_URL . 'index.php');

// =================================================================
// 2. AUTOLOADER (Para el Servidor / PHP)
// =================================================================

spl_autoload_register(function ($clase) {
    // Al usar ../ desde app/config/ bajamos a app/ y entramos directamente en modelo/ o controlador/
    $carpetas = [
        __DIR__ . '/../modelo/',
        __DIR__ . '/../controlador/'
    ];

    foreach ($carpetas as $carpeta) {
        $archivo = $carpeta . $clase . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
});
?>
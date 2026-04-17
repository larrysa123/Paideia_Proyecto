<?php
// Arrancamos el motor de sesiones (Tu control está perfecto)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CARGAMOS LA BASE DE DATOS PARA TODO EL PROYECTO
require_once __DIR__ . '/db.php'; 

// =================================================================
// 1. CONSTANTES DE RUTAS (Para el Navegador / HTML)
// =================================================================

// Ruta absoluta y segura para PHP y JavaScript
define('BASE_URL', 'http://localhost/PAIDEIA_PROYECTO/src/');

// Tus rutas derivadas (¡Están perfectas!)
define('RUTA_CSS', BASE_URL . 'public/css/');
define('RUTA_JS', BASE_URL . 'public/js/');
define('RUTA_VISTAS', BASE_URL . 'public/vista/');
define('RUTA_INICIO', BASE_URL . 'public/index.php');


// =================================================================
// 2. AUTOLOADER MÁGICO (Para el Servidor / PHP)
// =================================================================

spl_autoload_register(function ($clase) {
    // Definimos en qué carpetas están nuestras clases de PHP
    // __DIR__ nos sitúa en src/app/config/
    $carpetas = [
        __DIR__ . '/../modelo/',       // src/app/modelo/
        __DIR__ . '/../controlador/'   // src/app/controlador/
    ];

    // El Bibliotecario busca en cada carpeta
    foreach ($carpetas as $carpeta) {
        $archivo = $carpeta . $clase . '.php';
        
        // Si el archivo existe, le hace el require y termina de buscar
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
});
?>
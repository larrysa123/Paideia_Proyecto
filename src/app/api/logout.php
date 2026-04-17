<?php
// 1. Requerimos el Cerebro (arranca la sesión y el Autoloader)
require_once '../config/config.php';

// 2. Instanciamos al Jefe de Sala de accesos
$controlador = new AuthController();

// 3. Ejecutamos la salida (destruye la sesión y redirige)
$controlador->logout();
?>
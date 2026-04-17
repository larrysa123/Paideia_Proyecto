<?php
// 1. Requerimos el Cerebro (que trae el Autoloader y la DB)
require_once '../config/config.php';

header('Content-Type: application/json');

// ¡YA NO requerimos el controlador a mano! El Autoloader lo busca solo.


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Leemos el JSON que envía JavaScript (fetch)
    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);

    // 3. Instanciamos el Controlador (El Autoloader actúa aquí en silencio)
    $controlador = new UsuarioController();

    // 4. Le pasamos los datos al Controlador y recogemos su respuesta
    $respuesta = $controlador->registrarUsuario($datos);

    // 5. Se lo mandamos de vuelta a JavaScript
    echo json_encode($respuesta);

}else{
    echo json_encode([
        "status" => "error",
        "mensaje" => "Acceso denegado. Método no permitido"
    ]);
}

<?php 
//Requerimos el cerebro (Autoloader y la db)

require_once '../config/config.php';

//Decimos al navegador que va a recibir un json
header ('Content-Type: application/json');


// Comprobamos el método post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Leemos JSON que envia login.js
    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);

    // 5. Instanciamos el controlador y llamamos a la función
    $controlador = new AuthController();
    $respuesta = $controlador->loginUsuario($datos);

    // 6. Devolvemos la respuesta
    echo json_encode($respuesta);

} else {
    // Si alguien intenta acceder escribiendo la URL a mano (GET), le damos un error
    echo json_encode([
        "status" => "error",
        "mensaje" => "Acceso denegado. Método no permitido."
    ]);
}




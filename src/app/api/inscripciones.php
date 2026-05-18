<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new InscripcionController();

if ($metodo === 'GET') {
    echo json_encode($controlador->mostrarMisCursos());
} elseif ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarMatricula($datos));

} elseif ($metodo === 'PUT') {
    $datos = json_decode(file_get_contents('php://input'), true);
    if (isset($datos['accion']) && $datos['accion'] === 'actualizar_progreso') {
        echo json_encode($controlador->procesarAvanceProgreso($datos));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Acción PUT no reconocida"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}
?>
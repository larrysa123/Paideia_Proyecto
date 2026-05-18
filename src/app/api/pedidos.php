<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new PedidoController();

if ($metodo === 'GET') {
    if (isset($_GET['accion']) && $_GET['accion'] === 'historial') {
        echo json_encode($controlador->obtenerHistorial());
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Acción GET no reconocida."]);
    }
} elseif ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarPago($datos));
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}
?>
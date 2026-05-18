<?php
require_once '../config/config.php';
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new AdminController();

if ($metodo === 'GET') {
    if (isset($_GET['accion'])) {
        if ($_GET['accion'] === 'dashboard') {
            echo json_encode($controlador->dashboard());
        } elseif ($_GET['accion'] === 'detalle_usuario' && isset($_GET['id'])) {
            echo json_encode($controlador->obtenerDetalleUsuario($_GET['id']));
        } else {
            echo json_encode(["status" => "error", "mensaje" => "Acción GET no reconocida."]);
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Acción no especificada."]);
    }
    // Escuchar el método DELETE
} elseif ($metodo === 'DELETE') {
    if (isset($_GET['tipo']) && isset($_GET['id'])) {
        echo json_encode($controlador->procesarEliminacion($_GET['tipo'], $_GET['id']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Faltan parámetros para eliminar."]);
    }
} elseif ($metodo === 'PUT') {
    $datos = json_decode(file_get_contents('php://input'), true);
    if (isset($datos['accion'])) {
        if ($datos['accion'] === 'cambiar_estado') {
            echo json_encode($controlador->procesarCambioEstado($datos));
        } elseif ($datos['accion'] === 'editar_usuario') {
            echo json_encode($controlador->procesarEdicionUsuario($datos));
        } else {
            echo json_encode(["status" => "error", "mensaje" => "Acción PUT no reconocida."]);
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Faltan parámetros en la petición PUT."]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}
?>
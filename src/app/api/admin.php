<?php
require_once '../config/config.php';
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new AdminController();

if ($metodo === 'GET') {
    // Si la petición GET pide el 'dashboard', le devolvemos todo
    if (isset($_GET['accion']) && $_GET['accion'] === 'dashboard') {
        echo json_encode($controlador->dashboard());
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
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}

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
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}

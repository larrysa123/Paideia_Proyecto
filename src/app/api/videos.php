<?php
require_once '../config/config.php';
header('Content-Type: application/json');

// IMPORTANTE para depurar: Si algo falla, que nos lo diga
ini_set('display_errors', 1);
error_reporting(E_ALL);

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new VideoController();

if ($metodo === 'GET') {
    if (isset($_GET['id_curso'])) {
        echo json_encode($controlador->mostrarVideos($_GET['id_curso']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Falta el ID del curso."]);
    }

} elseif ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarCreacion($datos));

} elseif ($metodo === 'DELETE') {
    if (isset($_GET['id'])) {
        echo json_encode($controlador->procesarEliminacion($_GET['id']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Falta el ID del vídeo."]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}
?>
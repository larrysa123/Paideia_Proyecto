<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new ForoController();

if ($metodo === 'GET') {
    if (isset($_GET['id_video'])) {
        if (isset($_GET['accion']) && $_GET['accion'] == 'mivoto') {
            echo json_encode($controlador->cargarMiValoracionVideo($_GET['id_video']));
        } else {
            echo json_encode($controlador->cargarForoVideo($_GET['id_video']));
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Falta el ID del vídeo."]);
    }
} elseif ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if (isset($datos['accion']) && $datos['accion'] == 'valorar') {
        echo json_encode($controlador->procesarValoracionVideo($datos));
    } else {
        echo json_encode($controlador->procesarComentario($datos));
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}
?>
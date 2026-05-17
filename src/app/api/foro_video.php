<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new ForoController();

if ($metodo === 'GET') {
    // NUEVA RUTA PARA EL PANEL DEL PROFESOR
    if (isset($_GET['accion']) && $_GET['accion'] == 'mis_comentarios_profesor') {
        echo json_encode($controlador->cargarComentariosProfesor());
    } 
    // RUTAS ANTIGUAS DE LA SALA DE CLASES
    elseif (isset($_GET['id_video'])) {
        if (isset($_GET['accion']) && $_GET['accion'] == 'mivoto') {
            echo json_encode($controlador->cargarMiValoracionVideo($_GET['id_video']));
        } else {
            echo json_encode($controlador->cargarForoVideo($_GET['id_video']));
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Faltan parámetros."]);
    }
} elseif ($metodo === 'POST') {
    // ... (El bloque POST se queda exactamente igual que lo tenías)
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
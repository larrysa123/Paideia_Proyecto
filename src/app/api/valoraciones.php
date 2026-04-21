<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new ValoracionController();

if ($metodo === 'POST') {
    // Cuando el JS envíe las estrellas
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['id_curso']) && isset($data['estrellas'])) {
        echo json_encode($controlador->guardarValoracion($data['id_curso'], $data['estrellas']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Faltan datos."]);
    }

} elseif ($metodo === 'GET') {
    // Cuando el JS pregunte: "¿Cuántas estrellas le di yo a este curso?" al cargar la página
    if (isset($_GET['id_curso']) && isset($_GET['accion']) && $_GET['accion'] == 'mivoto') {
        echo json_encode($controlador->obtenerMiValoracion($_GET['id_curso']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Petición no válida."]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido."]);
}
?>
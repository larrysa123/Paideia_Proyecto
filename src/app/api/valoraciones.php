<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new ValoracionController();

if ($metodo === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // El texto ahora es opcional, así que comprobamos si existe. Si no, mandamos string vacío.
    if (isset($data['id_curso']) && isset($data['estrellas'])) {
        $texto = isset($data['texto']) ? $data['texto'] : '';
        echo json_encode($controlador->procesarResena($data['id_curso'], $data['estrellas'], $texto));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Faltan datos obligatorios (curso o estrellas)."]);
    }

} elseif ($metodo === 'GET') {
    if (isset($_GET['id_curso']) && isset($_GET['accion']) && $_GET['accion'] == 'miresena') {
        echo json_encode($controlador->cargarMiResena($_GET['id_curso']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Petición no válida."]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido."]);
}
?>
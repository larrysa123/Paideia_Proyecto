<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new InscripcionController();

if ($metodo === 'GET') {
    // Si el alumno pide ver sus cursos
    echo json_encode($controlador->mostrarMisCursos());

} elseif ($metodo === 'POST') {
    // Si el alumno se está inscribiendo (esto ya lo tenías)
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarMatricula($datos));
}
?>
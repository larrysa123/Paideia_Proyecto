<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$controlador = new CursoController();
if ($metodo === 'GET') {
    if (isset($_GET['id'])) {
        // Precarga para EDITAR (Privado - Pide sesión)
        echo json_encode($controlador->mostrarCurso($_GET['id']));
    } elseif (isset($_GET['id_publico'])) {
        // NUEVO: Detalle para ALUMNOS (Público - Sin sesión)
        echo json_encode($controlador->mostrarDetallePublico($_GET['id_publico']));
    } elseif (isset($_GET['mis_cursos'])) {
        // Panel del profesor
        echo json_encode($controlador->mostrarPanelProfesor());
    } else {
        // Catálogo general (index.php)
        echo json_encode($controlador->mostrarCatalogo());
    }
    // ... resto del archivo (POST, PUT, DELETE)

} elseif ($metodo === 'POST') {
    // Crear curso nuevo
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarCreacion($datos));
} elseif ($metodo === 'PUT') {
    // NUEVO: Actualizar curso existente
    $datos = json_decode(file_get_contents('php://input'), true);
    echo json_encode($controlador->procesarEdicion($datos));
} elseif ($metodo === 'DELETE') {
    // Eliminar curso
    if (isset($_GET['id'])) {
        echo json_encode($controlador->procesarEliminacion($_GET['id']));
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Falta el ID"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método no permitido"]);
}

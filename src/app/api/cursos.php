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
        // Detalle para ALUMNOS (Público - Sin sesión)
        echo json_encode($controlador->mostrarDetallePublico($_GET['id_publico']));
    } elseif (isset($_GET['mis_cursos'])) {
        // Panel del profesor
        echo json_encode($controlador->mostrarPanelProfesor());
    } else {
        // Catálogo general (index.php)
        echo json_encode($controlador->mostrarCatalogo());
    }

} elseif ($metodo === 'POST') {
    // Detectamos si es una petición JSON (para otras funciones) o un FormData (Subida de curso)
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($contentType, 'application/json') !== false) {
        $datos = json_decode(file_get_contents('php://input'), true);
    } else {
        // Es un formulario Multipart (FormData con archivo)
        $datos = $_POST;
        if (isset($_FILES['imagen'])) {
            $datos['archivo_imagen'] = $_FILES['imagen'];
        }
    }
    echo json_encode($controlador->procesarCreacion($datos));
    
} elseif ($metodo === 'PUT') {
    // Actualizar curso existente
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

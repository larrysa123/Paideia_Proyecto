<?php
class ForoController
{

    // COMENTARIOS 
    public function cargarForoVideo($id_video)
    {
        $modelo = new ForoVideo();
        $comentarios = $modelo->obtenerComentarios($id_video);
        return ["status" => "success", "data" => $comentarios];
    }

    public function procesarComentario($datos)
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No logueado."];

        if (isset($datos['id_video']) && isset($datos['texto']) && trim($datos['texto']) !== '') {
            $id_usuario = $_SESSION['user']['id_usuario'];
            $id_padre = isset($datos['id_padre']) ? $datos['id_padre'] : null;

            $modelo = new ForoVideo();
            $exito = $modelo->publicarComentario($id_usuario, $datos['id_video'], htmlspecialchars(trim($datos['texto'])), $id_padre);

            return $exito ? ["status" => "success", "mensaje" => "Comentario publicado."] : ["status" => "error", "mensaje" => "Error al guardar."];
        }
        return ["status" => "error", "mensaje" => "Faltan datos."];
    }

    // VALORACIONES
    public function cargarMiValoracionVideo($id_video)
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No logueado."];

        $modelo = new ForoVideo();
        $estrellas = $modelo->obtenerMiValoracionVideo($id_video, $_SESSION['user']['id_usuario']);
        return ["status" => "success", "data" => ["estrellas" => $estrellas]];
    }

    public function procesarValoracionVideo($datos)
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No logueado."];

        if (isset($datos['id_video']) && isset($datos['estrellas'])) {
            $estrellas = intval($datos['estrellas']);
            if ($estrellas < 1 || $estrellas > 5) return ["status" => "error", "mensaje" => "Valoración inválida."];

            $modelo = new ForoVideo();
            $exito = $modelo->valorarVideo($_SESSION['user']['id_usuario'], $datos['id_video'], $estrellas);

            return $exito ? ["status" => "success", "mensaje" => "Valoración guardada."] : ["status" => "error", "mensaje" => "Error al valorar."];
        }
        return ["status" => "error", "mensaje" => "Faltan datos."];
    }

    // CARGAR BANDEJA DE ENTRADA DEL PROFESOR 
    public function cargarComentariosProfesor()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
            return ["status" => "error", "mensaje" => "No autorizado."];
        }

        $modelo = new ForoVideo();
        $datos = $modelo->obtenerComentariosPorProfesor($_SESSION['user']['id_usuario']);
        return ["status" => "success", "data" => $datos];
    }

    // ELIMINAR COMENTARIO 
    public function procesarEliminarComentario($datos)
    {
        // Comprobamos que haya sesión iniciada
        if (!isset($_SESSION['user'])) {
            return ["status" => "error", "mensaje" => "No tienes sesión iniciada."];
        }

        if (isset($datos['id_comentario'])) {
            $modelo = new ForoVideo();
            $exito = $modelo->eliminarComentario($datos['id_comentario'], $_SESSION['user']['id_usuario']);

            if ($exito) {
                return ["status" => "success", "mensaje" => "Comentario eliminado."];
            } else {
                return ["status" => "error", "mensaje" => "Denegado por la Base de Datos (quizá no te pertenece)."];
            }
        }
        return ["status" => "error", "mensaje" => "Falta el ID del comentario."];
    }
}
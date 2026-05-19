<?php
class VideoController
{

    public function mostrarVideos($id_curso)
    {
        $videoModel = new Video();
        $lista = $videoModel->obtenerPorCurso($id_curso);
        return ["status" => "success", "data" => $lista];
    }

    public function procesarCreacion($datos)
    {
        // Solo profesores pueden subir vídeos
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
            return ["status" => "error", "mensaje" => "No autorizado. Solo profesores."];
        }

        if (isset($datos['id_curso'], $datos['titulo'], $datos['url_youtube'], $datos['orden'])) {
            $videoModel = new Video();
            $exito = $videoModel->insertar(
                $datos['id_curso'],
                htmlspecialchars(trim($datos['titulo'])),
                htmlspecialchars(trim($datos['descripcion'] ?? '')),
                trim($datos['url_youtube']),
                intval($datos['orden'])
            );
            return $exito ? ["status" => "success", "mensaje" => "Vídeo añadido correctamente."] : ["status" => "error", "mensaje" => "Error al guardar en la base de datos."];
        }
        return ["status" => "error", "mensaje" => "Faltan datos obligatorios del vídeo."];
    }

    public function procesarEliminacion($id_video)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
            return ["status" => "error", "mensaje" => "No autorizado."];
        }

        $videoModel = new Video();
        $exito = $videoModel->eliminar($id_video);
        return $exito ? ["status" => "success", "mensaje" => "Vídeo eliminado."] : ["status" => "error", "mensaje" => "No se pudo eliminar el vídeo."];
    }

    // Procesar la edición de un vídeo
    public function procesarEdicion($datos)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
            return ["status" => "error", "mensaje" => "No autorizado."];
        }

        if (isset($datos['id_video'], $datos['titulo'], $datos['url_youtube'])) {
            $videoModel = new Video();
            $exito = $videoModel->actualizar(
                $datos['id_video'],
                htmlspecialchars(trim($datos['titulo'])),
                trim($datos['url_youtube'])
            );
            return $exito ? ["status" => "success", "mensaje" => "Lección actualizada correctamente."] : ["status" => "error", "mensaje" => "Error al actualizar."];
        }
        return ["status" => "error", "mensaje" => "Faltan datos obligatorios."];
    }

    // Procesar el reordenamiento masivo
    public function procesarReorden($datos)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
            return ["status" => "error", "mensaje" => "No autorizado."];
        }

        if (isset($datos['orden_videos']) && is_array($datos['orden_videos'])) {
            $videoModel = new Video();
            // Recorremos el array y actualizamos el orden de cada vídeo
            foreach ($datos['orden_videos'] as $video) {
                $videoModel->actualizarOrden($video['id_video'], $video['orden']);
            }
            return ["status" => "success", "mensaje" => "Orden guardado."];
        }
        return ["status" => "error", "mensaje" => "Datos de reordenamiento inválidos."];
    }
}

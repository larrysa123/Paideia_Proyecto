<?php
class ValoracionController {
    
    public function guardarValoracion($id_curso, $estrellas) {
        // Solo los alumnos (rol 1) pueden valorar
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
            return ["status" => "error", "mensaje" => "No tienes permisos para valorar."];
        }

        if ($estrellas < 1 || $estrellas > 5) {
            return ["status" => "error", "mensaje" => "Valoración no válida."];
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        $modelo = new Valoracion();
        $exito = $modelo->guardarValoracion($id_curso, $id_usuario, $estrellas);

        if ($exito) {
            // Devolvemos la nueva media para que el JS actualice la pantalla sin recargar
            $nuevaMedia = $modelo->obtenerMediaCurso($id_curso);
            return ["status" => "success", "mensaje" => "¡Gracias por tu valoración!", "data" => $nuevaMedia];
        } else {
            return ["status" => "error", "mensaje" => "Error al guardar en la base de datos."];
        }
    }

    public function obtenerMiValoracion($id_curso) {
        if (!isset($_SESSION['user'])) return ["status" => "error"];
        
        $modelo = new Valoracion();
        $voto = $modelo->obtenerValoracionAlumno($id_curso, $_SESSION['user']['id_usuario']);
        
        return ["status" => "success", "data" => $voto];
    }
}
?>
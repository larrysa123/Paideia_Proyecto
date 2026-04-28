<?php
class ValoracionController {
    
    public function procesarResena($id_curso, $estrellas, $texto) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
            return ["status" => "error", "mensaje" => "Solo los alumnos matriculados pueden valorar."];
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        $modelo = new Valoracion();

        if (!$modelo->estaMatriculado($id_curso, $id_usuario)) {
            return ["status" => "error", "mensaje" => "No puedes valorar un curso en el que no estás matriculado."];
        }

        // Solo validamos las estrellas. El texto es libre (puede venir vacío).
        if ($estrellas < 1 || $estrellas > 5) {
            return ["status" => "error", "mensaje" => "Puntuación no válida."];
        }

        $exito = $modelo->guardarResenaCompleta($id_curso, $id_usuario, $estrellas, $texto);

        if ($exito) {
            $nuevaMedia = $modelo->obtenerMediaCurso($id_curso);
            return ["status" => "success", "mensaje" => "¡Valoración guardada con éxito!", "data" => $nuevaMedia];
        } else {
            return ["status" => "error", "mensaje" => "Hubo un error al guardar tu opinión."];
        }
    }

    public function cargarMiResena($id_curso) {
        if (!isset($_SESSION['user'])) return ["status" => "error"];
        
        $modelo = new Valoracion();
        $datos = $modelo->obtenerResenaAlumno($id_curso, $_SESSION['user']['id_usuario']);
        
        return ["status" => "success", "data" => $datos];
    }
}
?>
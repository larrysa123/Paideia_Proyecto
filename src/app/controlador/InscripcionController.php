<?php
class InscripcionController
{

    public function procesarMatricula($datos)
    {
        // 1. ¿Está logueado?
        if (!isset($_SESSION['user'])) {
            return ["status" => "error", "code" => "NO_LOGIN", "mensaje" => "Debes iniciar sesión para inscribirte."];
        }

        // 2. Opcional: ¿Es un alumno? (Rol 1)
        if ($_SESSION['user']['id_rol'] != 1) {
            return ["status" => "error", "mensaje" => "Solo las cuentas de alumno pueden matricularse en los cursos."];
        }

        if (!isset($datos['id_curso'])) {
            return ["status" => "error", "mensaje" => "Falta el ID del curso."];
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        $id_curso = $datos['id_curso'];

        $inscripcionModel = new Inscripcion();

        // 3. ¿Ya estaba inscrito?
        if ($inscripcionModel->verificar($id_usuario, $id_curso)) {
            return ["status" => "error", "code" => "YA_INSCRITO", "mensaje" => "Ya estás matriculado en este curso. Ve a 'Mis Cursos' para verlo."];
        }

        // 4. Inscribir en BD
        $exito = $inscripcionModel->matricular($id_usuario, $id_curso);

        if ($exito) {
            return ["status" => "success", "mensaje" => "¡Inscripción completada con éxito!"];
        } else {
            return ["status" => "error", "mensaje" => "Hubo un problema al procesar la matrícula en la base de datos."];
        }
    }

    public function mostrarMisCursos()
    {
        // 1. Verificamos sesión
        if (!isset($_SESSION['user'])) {
            return ["status" => "error", "mensaje" => "No has iniciado sesión."];
        }

        $id_usuario = $_SESSION['user']['id_usuario'];
        $inscripcionModel = new Inscripcion();

        // 2. Pedimos los datos al modelo
        $lista = $inscripcionModel->obtenerPorUsuario($id_usuario);

        return ["status" => "success", "data" => $lista];
    }
}

<?php
class AdminController
{

    private function verificarPermisos()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
            return false;
        }
        return true;
    }

    public function dashboard()
    {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "Acceso denegado."];
        }

        $adminModel = new Admin();

        // Empaquetamos todo en una sola respuesta
        return [
            "status" => "success",
            "data" => [
                "usuarios" => $adminModel->obtenerUsuarios(),
                "cursos" => $adminModel->obtenerCursos()
            ]
        ];
    }
    // NUEVO: Procesar borrado
    public function procesarEliminacion($tipo, $id)
    {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "No tienes permisos."];
        }

        $adminModel = new Admin();
        $exito = false;

        if ($tipo === 'usuario') {
            $exito = $adminModel->eliminarUsuario($id);
            $mensaje = $exito ? "Usuario eliminado correctamente." : "No se pudo eliminar el usuario (¿es tu propio usuario o tiene cursos asociados?).";
        } elseif ($tipo === 'curso') {
            $exito = $adminModel->eliminarCurso($id);
            $mensaje = $exito ? "Curso eliminado de la plataforma." : "Error al eliminar el curso.";
        } else {
            return ["status" => "error", "mensaje" => "Tipo no válido."];
        }

        return $exito ? ["status" => "success", "mensaje" => $mensaje] : ["status" => "error", "mensaje" => $mensaje];
    }
}

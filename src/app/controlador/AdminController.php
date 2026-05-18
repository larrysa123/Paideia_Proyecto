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
    // Procesar borrado
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

    // Procesar el cambio de estado
    public function procesarCambioEstado($datos)
    {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "No tienes permisos."];
        }

        if (isset($datos['id_curso']) && isset($datos['estado'])) {
            $adminModel = new Admin();
            $exito = $adminModel->cambiarEstadoCurso($datos['id_curso'], $datos['estado']);
            
            $textoEstado = $datos['estado'] === 'publicado' ? 'aprobado y publicado' : 'ocultado (ha pasado a pendiente)';
            return $exito ? ["status" => "success", "mensaje" => "Curso $textoEstado correctamente."] 
                          : ["status" => "error", "mensaje" => "Error al cambiar el estado del curso."];
        }
        return ["status" => "error", "mensaje" => "Faltan datos para el cambio de estado."];
    }

    // --- NUEVO: Recuperar detalles de un usuario en formato JSON ---
    public function obtenerDetalleUsuario($id_usuario)
    {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "No tienes permisos."];
        }

        $adminModel = new Admin();
        $usuario = $adminModel->obtenerUsuarioPorId($id_usuario);
        
        return $usuario ? ["status" => "success", "data" => $usuario] 
                        : ["status" => "error", "mensaje" => "Usuario no encontrado."];
    }

    // Validar y procesar los cambios editados
    public function procesarEdicionUsuario($datos)
    {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "No tienes permisos."];
        }

        if (isset($datos['id_usuario'], $datos['nombre'], $datos['apellidos'], $datos['email'], $datos['id_rol'])) {
            $adminModel = new Admin();
            
            $password = !empty($datos['password']) ? trim($datos['password']) : null;
            
            $exito = $adminModel->actualizarUsuario(
                $datos['id_usuario'],
                htmlspecialchars(trim($datos['nombre'])),
                htmlspecialchars(trim($datos['apellidos'])),
                htmlspecialchars(trim($datos['email'])),
                intval($datos['id_rol']),
                $password
            );

            return $exito ? ["status" => "success", "mensaje" => "Usuario actualizado correctamente."] 
                          : ["status" => "error", "mensaje" => "Error al actualizar (el email podría estar duplicado)."];
        }
        return ["status" => "error", "mensaje" => "Faltan campos obligatorios."];
    }
}

<?php
class CursoController {
    
    // Antes: obtenerCursos
    public function mostrarCatalogo() {
        $cursoModel = new Curso();
        $lista = $cursoModel->obtenerTodos(); 
        return $lista ? ["status" => "success", "data" => $lista] : ["status" => "error", "mensaje" => "No hay cursos."];
    }

    // Antes: obtenerMisCursos
    public function mostrarPanelProfesor() {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No logueado"];
        
        $cursoModel = new Curso();
        $lista = $cursoModel->obtenerPorProfesor($_SESSION['user']['id_usuario']);
        return ["status" => "success", "data" => $lista];
    }

    // Antes: crearCurso
    public function procesarCreacion($datos) {
        $id_profesor = $_SESSION['user']['id_usuario'];
        $cursoModel = new Curso();
        $exito = $cursoModel->insertar($id_profesor, $datos['titulo'], $datos['descripcion'], $datos['precio'], $datos['imagen']);
        return $exito ? ["status" => "success", "mensaje" => "Curso creado"] : ["status" => "error", "mensaje" => "Error al guardar"];
    }

    // NUEVA: El puente para eliminar
    public function procesarEliminacion($id_curso) {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No autorizado"];
        
        $cursoModel = new Curso();
        $exito = $cursoModel->eliminar($id_curso, $_SESSION['user']['id_usuario']);
        return $exito ? ["status" => "success", "mensaje" => "Curso eliminado correctamente"] : ["status" => "error", "mensaje" => "No se pudo eliminar"];
    }

    // Prepara los datos para el formulario de edición
    public function mostrarCurso($id_curso) {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No autorizado"];
        
        $cursoModel = new Curso();
        $curso = $cursoModel->obtenerPorId($id_curso, $_SESSION['user']['id_usuario']);
        
        return $curso ? ["status" => "success", "data" => $curso] : ["status" => "error", "mensaje" => "Curso no encontrado o no autorizado"];
    }

    // Recibe los datos nuevos y manda a guardar
    public function procesarEdicion($datos) {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No autorizado"];
        
        // Verificamos que al menos venga el ID del curso y los campos obligatorios
        if (isset($datos['id_curso'], $datos['titulo'], $datos['precio'])) {
            $cursoModel = new Curso();
            $exito = $cursoModel->actualizar(
                $datos['id_curso'], 
                $_SESSION['user']['id_usuario'], 
                htmlspecialchars(trim($datos['titulo'])),
                htmlspecialchars(trim($datos['descripcion'])),
                floatval($datos['precio']),
                trim($datos['imagen'])
            );
            return $exito ? ["status" => "success", "mensaje" => "¡Curso actualizado con éxito!"] : ["status" => "error", "mensaje" => "No se detectaron cambios o hubo un error."];
        }
        return ["status" => "error", "mensaje" => "Faltan campos obligatorios."];
    }



    // NUEVA: Muestra el curso al público sin pedir sesión
    public function mostrarDetallePublico($id_curso) {
        $cursoModel = new Curso();
        $curso = $cursoModel->obtenerDetallePublico($id_curso);
        
        return $curso ? ["status" => "success", "data" => $curso] : ["status" => "error", "mensaje" => "Este curso no existe o ya no está disponible."];
    }
}
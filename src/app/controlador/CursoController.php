<?php
class CursoController
{

    public function mostrarCatalogo()
    {
        // Miramos si hay alguien logueado. Si lo hay, guardamos su ID.
        $id_usuario = isset($_SESSION['user']) ? $_SESSION['user']['id_usuario'] : null;

        $cursoModel = new Curso();
        $lista = $cursoModel->obtenerTodos($id_usuario);

        return $lista ? ["status" => "success", "data" => $lista] : ["status" => "error", "mensaje" => "No hay cursos."];
    }


    public function mostrarPanelProfesor()
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No logueado"];

        $cursoModel = new Curso();
        $lista = $cursoModel->obtenerPorProfesor($_SESSION['user']['id_usuario']);
        return ["status" => "success", "data" => $lista];
    }


    public function procesarCreacion($datos)
    {
        $id_profesor = $_SESSION['user']['id_usuario'];
        $nombre_imagen = null;

        // LÓGICA DE SUBIDA DE IMAGEN
        if (isset($datos['archivo_imagen']) && $datos['archivo_imagen']['error'] === UPLOAD_ERR_OK) {
            $archivo = $datos['archivo_imagen'];

            // Sacamos la extensión (jpg, png...)
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

            // Creamos un nombre único: "curso_IdDelProfesor_Milisegundos.jpg"
            $nombre_imagen = 'curso_' . $id_profesor . '_' . time() . '.' . $extension;

            // Ruta donde se va a guardar físicamente
            $ruta_destino = __DIR__ . '/../../public/assets/img/cursos/' . $nombre_imagen;

            if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                return ["status" => "error", "mensaje" => "Error al mover la imagen al servidor."];
            }
        }

        $cursoModel = new Curso();
        // Insertamos usando el nombre único generado
        $exito = $cursoModel->insertar($id_profesor, $datos['titulo'], $datos['descripcion'], $datos['precio'], $nombre_imagen);

        return $exito ? ["status" => "success", "mensaje" => "¡Curso y portada subidos con éxito!"]
            : ["status" => "error", "mensaje" => "Error al guardar el registro en la base de datos."];
    }

    // El puente para eliminar
    public function procesarEliminacion($id_curso)
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No autorizado"];

        $cursoModel = new Curso();
        $exito = $cursoModel->eliminar($id_curso, $_SESSION['user']['id_usuario']);
        return $exito ? ["status" => "success", "mensaje" => "Curso eliminado correctamente"] : ["status" => "error", "mensaje" => "No se pudo eliminar"];
    }

    // Prepara los datos para el formulario de edición
    public function mostrarCurso($id_curso)
    {
        if (!isset($_SESSION['user'])) return ["status" => "error", "mensaje" => "No autorizado"];

        $cursoModel = new Curso();
        $curso = $cursoModel->obtenerPorId($id_curso, $_SESSION['user']['id_usuario']);

        return $curso ? ["status" => "success", "data" => $curso] : ["status" => "error", "mensaje" => "Curso no encontrado o no autorizado"];
    }

    // Recibe los datos nuevos y manda a guardar
    public function procesarEdicion($datos)
    {
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



    // Muestra el curso al público sin pedir sesión
    public function mostrarDetallePublico($id_curso)
    {
        $cursoModel = new Curso();
        $curso = $cursoModel->obtenerDetallePublico($id_curso);

        return $curso ? ["status" => "success", "data" => $curso] : ["status" => "error", "mensaje" => "Este curso no existe o ya no está disponible."];
    }
}

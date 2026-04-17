<?php
class Curso {
    private $db;
    private $table = 'Curso'; 

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // Antes: obtenerCursos
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 'publicado'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Antes: obtenerCursosPorProfesor
    public function obtenerPorProfesor($id_profesor) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_profesor = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Antes: registrar
    public function insertar($id_profesor, $titulo, $descripcion, $precio, $imagen) {
        try {
            $sql = "INSERT INTO " . $this->table . " (id_profesor, titulo, descripcion, precio, imagen, estado) 
                    VALUES (?, ?, ?, ?, ?, 'publicado')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_profesor, $titulo, $descripcion, $precio, $imagen]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // NUEVA: Para el botón de la papelera
    public function eliminar($id_curso, $id_profesor) {
        // Por seguridad, pedimos el id_profesor para que nadie borre cursos ajenos
        $query = "DELETE FROM " . $this->table . " WHERE id_curso = ? AND id_profesor = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_curso, $id_profesor]);
    }

    // Obtener los datos de un solo curso para rellenar el formulario
    public function obtenerPorId($id_curso, $id_profesor) {
        // Pedimos el id_profesor para que nadie pueda editar cursos de otros
        $query = "SELECT * FROM " . $this->table . " WHERE id_curso = ? AND id_profesor = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_profesor]);
        // Usamos fetch() normal porque solo queremos UN resultado, no una lista
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Sobrescribir los datos con el UPDATE
    public function actualizar($id_curso, $id_profesor, $titulo, $descripcion, $precio, $imagen) {
        try {
            $sql = "UPDATE " . $this->table . " 
                    SET titulo = ?, descripcion = ?, precio = ?, imagen = ? 
                    WHERE id_curso = ? AND id_profesor = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$titulo, $descripcion, $precio, $imagen, $id_curso, $id_profesor]);
        } catch (PDOException $e) {
            return false;
        }
    }



    // NUEVA: Para que cualquier alumno vea los detalles (solo si está publicado)
    public function obtenerDetallePublico($id_curso) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_curso = ? AND estado = 'publicado'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
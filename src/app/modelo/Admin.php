<?php
class Admin {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // 1. Obtener todos los usuarios (con su rol)
    public function obtenerUsuarios() {
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.email, r.nombre_rol 
                  FROM Usuario u 
                  INNER JOIN Rol r ON u.id_rol = r.id_rol 
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Obtener todos los cursos (con el nombre del profesor)
    public function obtenerCursos() {
        $query = "SELECT c.id_curso, c.titulo, c.precio, c.estado, u.nombre AS profesor 
                  FROM Curso c 
                  INNER JOIN Usuario u ON c.id_profesor = u.id_usuario 
                  ORDER BY c.fecha_creacion DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
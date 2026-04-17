<?php
class Inscripcion
{
    private $db;
    private $table = 'Inscripcion';

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    // 1. Comprobar si ya está matriculado
    public function verificar($id_usuario, $id_curso)
    {
        // Buscamos si existe esa pareja exacta
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE id_usuario = ? AND id_curso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_usuario, $id_curso]);
        return $stmt->fetch();
    }

    // 2. Apuntar al alumno
    public function matricular($id_usuario, $id_curso)
    {
        try {
            // No hace falta insertar fecha_alta ni progreso porque tienen valor DEFAULT en tu BD
            $query = "INSERT INTO " . $this->table . " (id_usuario, id_curso) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_usuario, $id_curso]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener todos los cursos de un alumno específico
    public function obtenerPorUsuario($id_usuario)
    {
        // Unimos las tablas para traer el título y la imagen del curso
        $query = "SELECT c.id_curso, c.titulo, c.descripcion, c.imagen, i.fecha_alta, i.progreso 
              FROM " . $this->table . " i 
              INNER JOIN Curso c ON i.id_curso = c.id_curso 
              WHERE i.id_usuario = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

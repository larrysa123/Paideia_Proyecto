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
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE id_usuario = ? AND id_curso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_usuario, $id_curso]);
        return $stmt->fetch();
    }

    // 2. Apuntar al alumno
    public function matricular($id_usuario, $id_curso)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (id_usuario, id_curso) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_usuario, $id_curso]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 3. Obtener todos los cursos de un alumno específico (CON SUS ESTRELLAS GUARDADAS)
    public function obtenerPorUsuario($id_usuario)
    {
        // Añadimos el LEFT JOIN con valoracion_curso para recuperar "mi_nota"
        $query = "SELECT c.id_curso, c.titulo, c.descripcion, c.imagen, i.fecha_alta, i.progreso, v.estrellas AS mi_nota
              FROM " . $this->table . " i 
              INNER JOIN Curso c ON i.id_curso = c.id_curso 
              LEFT JOIN valoracion_curso v ON (c.id_curso = v.id_curso AND v.id_usuario = i.id_usuario)
              WHERE i.id_usuario = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
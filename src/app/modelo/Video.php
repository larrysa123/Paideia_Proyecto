<?php
class Video {
    private $db;
    private $table = 'Video';

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // 1. Leer los vídeos de un curso en concreto, ordenados por el campo 'orden'
    public function obtenerPorCurso($id_curso) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_curso = ? ORDER BY orden ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Añadir un vídeo nuevo
    public function insertar($id_curso, $titulo, $descripcion, $url_youtube, $orden) {
        try {
            $query = "INSERT INTO " . $this->table . " (id_curso, titulo, descripcion, url_youtube, orden) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_curso, $titulo, $descripcion, $url_youtube, $orden]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 3. Eliminar un vídeo
    public function eliminar($id_video) {
        $query = "DELETE FROM " . $this->table . " WHERE id_video = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_video]);
    }
}
?>
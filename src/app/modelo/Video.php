<?php
class Video {
    private $db;
    private $table = 'Video';

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // Leer los vídeos de un curso en concreto, ordenados por el campo 'orden'
    public function obtenerPorCurso($id_curso) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_curso = ? ORDER BY orden ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir un vídeo nuevo
    public function insertar($id_curso, $titulo, $descripcion, $url_youtube, $orden) {
        try {
            $query = "INSERT INTO " . $this->table . " (id_curso, titulo, descripcion, url_youtube, orden) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_curso, $titulo, $descripcion, $url_youtube, $orden]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Eliminar un vídeo
    public function eliminar($id_video) {
        $query = "DELETE FROM " . $this->table . " WHERE id_video = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_video]);
    }

    // Actualizar los detalles de un vídeo existente
    public function actualizar($id_video, $titulo, $url_youtube) {
        try {
            $query = "UPDATE " . $this->table . " SET titulo = ?, url_youtube = ? WHERE id_video = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$titulo, $url_youtube, $id_video]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Actualizar solo el número de orden (Drag & Drop)
    public function actualizarOrden($id_video, $nuevo_orden) {
        try {
            $query = "UPDATE " . $this->table . " SET orden = ? WHERE id_video = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$nuevo_orden, $id_video]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
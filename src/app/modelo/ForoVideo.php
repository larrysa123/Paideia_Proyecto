<?php
class ForoVideo {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // --- SECCIÓN: COMENTARIOS (FORO) ---
    public function obtenerComentarios($id_video) {
        $query = "SELECT c.*, u.nombre, u.apellidos, u.foto, r.nombre_rol 
                  FROM Comentario_Video c
                  JOIN Usuario u ON c.id_usuario = u.id_usuario
                  JOIN Rol r ON u.id_rol = r.id_rol
                  WHERE c.id_video = ?
                  ORDER BY c.fecha ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_video]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupamos en padres e hijos (hilos)
        $comentarios = [];
        $respuestas = [];

        foreach ($resultados as $row) {
            if ($row['id_padre'] === null) {
                $row['respuestas'] = [];
                $comentarios[$row['id_comentario']] = $row;
            } else {
                $respuestas[] = $row;
            }
        }

        foreach ($respuestas as $res) {
            if (isset($comentarios[$res['id_padre']])) {
                $comentarios[$res['id_padre']]['respuestas'][] = $res;
            }
        }

        return array_values($comentarios);
    }

    public function publicarComentario($id_usuario, $id_video, $texto, $id_padre = null) {
        try {
            $query = "INSERT INTO Comentario_Video (id_usuario, id_video, texto, id_padre, tipo) 
                      VALUES (?, ?, ?, ?, 'foro')";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_usuario, $id_video, $texto, $id_padre]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // --- SECCIÓN: VALORACIÓN DEL VÍDEO (ESTRELLAS) ---
    public function obtenerMiValoracionVideo($id_video, $id_usuario) {
        $query = "SELECT estrellas FROM Valoracion_Video WHERE id_video = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_video, $id_usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['estrellas'] : 0;
    }

    private function actualizarMediaEnVideo($id_video) {
        $query = "UPDATE Video 
                  SET valoracion_media = (
                      SELECT COALESCE(ROUND(AVG(estrellas), 1), 0) 
                      FROM Valoracion_Video 
                      WHERE id_video = ?
                  ) 
                  WHERE id_video = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_video, $id_video]);
    }

    public function valorarVideo($id_usuario, $id_video, $estrellas) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO Valoracion_Video (id_usuario, id_video, estrellas) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE estrellas = ?, fecha = CURRENT_TIMESTAMP";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_usuario, $id_video, $estrellas, $estrellas]);

            $this->actualizarMediaEnVideo($id_video);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>
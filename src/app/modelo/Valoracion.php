<?php
class Valoracion {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // A. SEGURIDAD: Comprobar si está matriculado
    public function estaMatriculado($id_curso, $id_usuario) {
        $query = "SELECT 1 FROM Inscripcion WHERE id_curso = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_usuario]);
        return $stmt->fetch() !== false;
    }

    // B. Obtener nota media
    public function obtenerMediaCurso($id_curso) {
        $query = "SELECT AVG(estrellas) as media, COUNT(*) as total 
                  FROM valoracion_curso 
                  WHERE id_curso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'media' => $resultado['media'] ? round($resultado['media'], 1) : 0,
            'total' => $resultado['total']
        ];
    }

    // C. Obtener reseña actual (Estrellas + Texto)
    public function obtenerResenaAlumno($id_curso, $id_usuario) {
        $q1 = "SELECT estrellas FROM valoracion_curso WHERE id_curso = ? AND id_usuario = ?";
        $st1 = $this->db->prepare($q1);
        $st1->execute([$id_curso, $id_usuario]);
        $voto = $st1->fetch(PDO::FETCH_ASSOC);

        $q2 = "SELECT texto FROM comentario_curso WHERE id_curso = ? AND id_usuario = ? AND id_padre IS NULL LIMIT 1";
        $st2 = $this->db->prepare($q2);
        $st2->execute([$id_curso, $id_usuario]);
        $comentario = $st2->fetch(PDO::FETCH_ASSOC);

        return [
            'estrellas' => $voto ? $voto['estrellas'] : null,
            'texto' => $comentario ? $comentario['texto'] : ''
        ];
    }

    // D. GUARDAR (Texto Opcional)
    public function guardarResenaCompleta($id_curso, $id_usuario, $estrellas, $texto) {
        try {
            $this->db->beginTransaction();

            // 1. Estrellas (Siempre se guardan)
            $qVoto = "INSERT INTO valoracion_curso (id_usuario, id_curso, estrellas) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE estrellas = ?, fecha = CURRENT_TIMESTAMP";
            $stVoto = $this->db->prepare($qVoto);
            $stVoto->execute([$id_usuario, $id_curso, $estrellas, $estrellas]);

            // 2. Texto (Opcional)
            if (trim($texto) !== '') {
                // Si hay texto, insertamos o actualizamos
                $checkCom = "SELECT id_comentario FROM comentario_curso WHERE id_curso = ? AND id_usuario = ? AND id_padre IS NULL LIMIT 1";
                $stCheck = $this->db->prepare($checkCom);
                $stCheck->execute([$id_curso, $id_usuario]);
                $existente = $stCheck->fetch();

                if ($existente) {
                    $qText = "UPDATE comentario_curso SET texto = ?, fecha = CURRENT_TIMESTAMP WHERE id_comentario = ?";
                    $stText = $this->db->prepare($qText);
                    $stText->execute([$texto, $existente['id_comentario']]);
                } else {
                    $qText = "INSERT INTO comentario_curso (id_usuario, id_curso, texto, tipo) VALUES (?, ?, ?, 'reseña')";
                    $stText = $this->db->prepare($qText);
                    $stText->execute([$id_usuario, $id_curso, $texto]);
                }
            } else {
                // Si envía el texto vacío, borramos su comentario anterior si existía para no dejar basura
                $qDel = "DELETE FROM comentario_curso WHERE id_curso = ? AND id_usuario = ? AND id_padre IS NULL";
                $stDel = $this->db->prepare($qDel);
                $stDel->execute([$id_curso, $id_usuario]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>
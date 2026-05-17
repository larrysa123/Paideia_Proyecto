<?php
class Valoracion
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function estaMatriculado($id_curso, $id_usuario)
    {
        $query = "SELECT 1 FROM Inscripcion WHERE id_curso = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_usuario]);
        return $stmt->fetch() !== false;
    }

    // AHORA LEE LA COLUMNA valoracion_media DE LA TABLA CURSO
    public function obtenerMediaCurso($id_curso)
    {
        $query = "SELECT c.valoracion_media as media, COUNT(v.estrellas) as total 
                  FROM Curso c
                  LEFT JOIN valoracion_curso v ON c.id_curso = v.id_curso
                  WHERE c.id_curso = ?
                  GROUP BY c.id_curso";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'media' => $resultado['media'] ? round($resultado['media'], 1) : 0,
            'total' => $resultado['total']
        ];
    }

    public function obtenerResenaAlumno($id_curso, $id_usuario)
    {
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

    // --- NUEVO: FUNCIÓN QUE ACTUALIZA LA TABLA CURSO ---
    private function actualizarMediaEnCurso($id_curso)
    {
        $query = "UPDATE Curso 
                  SET valoracion_media = (
                      SELECT COALESCE(ROUND(AVG(estrellas), 1), 0) 
                      FROM valoracion_curso 
                      WHERE id_curso = ?
                  ) 
                  WHERE id_curso = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_curso]);
    }

    public function guardarResenaCompleta($id_curso, $id_usuario, $estrellas, $texto)
    {
        try {
            $this->db->beginTransaction();

            // 1. Guardar Estrellas
            $qVoto = "INSERT INTO valoracion_curso (id_usuario, id_curso, estrellas) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE estrellas = ?, fecha = CURRENT_TIMESTAMP";
            $stVoto = $this->db->prepare($qVoto);
            $stVoto->execute([$id_usuario, $id_curso, $estrellas, $estrellas]);

            // 2. Guardar Texto
            if (trim($texto) !== '') {
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
                $qDel = "DELETE FROM comentario_curso WHERE id_curso = ? AND id_usuario = ? AND id_padre IS NULL";
                $stDel = $this->db->prepare($qDel);
                $stDel->execute([$id_curso, $id_usuario]);
            }

            // 3. ACTUALIZAR LA COLUMNA valoracion_media ANTES DE CERRAR LA TRANSACCIÓN
            $this->actualizarMediaEnCurso($id_curso);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}

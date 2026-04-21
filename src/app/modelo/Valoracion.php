<?php
class Valoracion {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    // 1. Obtener la nota media y el total de votos de un curso
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

    // 2. Comprobar si un alumno ya ha valorado este curso (para pintar sus estrellas)
    public function obtenerValoracionAlumno($id_curso, $id_usuario) {
        $query = "SELECT estrellas 
                  FROM valoracion_curso 
                  WHERE id_curso = ? AND id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Guardar o actualizar la valoración (Upsert)
    public function guardarValoracion($id_curso, $id_usuario, $estrellas) {
        $existe = $this->obtenerValoracionAlumno($id_curso, $id_usuario);

        if ($existe) {
            // Si ya votó, actualizamos su nota
            $query = "UPDATE valoracion_curso 
                      SET estrellas = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id_curso = ? AND id_usuario = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$estrellas, $id_curso, $id_usuario]);
        } else {
            // Si es nuevo, insertamos el registro
            $query = "INSERT INTO valoracion_curso (id_curso, id_usuario, estrellas) 
                      VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id_curso, $id_usuario, $estrellas]);
        }
    }
}
?>
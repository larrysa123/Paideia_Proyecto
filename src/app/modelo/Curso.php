<?php
class Curso
{
    private $db;
    private $table = 'Curso';

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function obtenerTodos()
    {
        // Hacemos el COUNT para saber cuántos votos tiene en total, y leemos todas las columnas de Curso (incluida valoracion_media)
        $query = "SELECT c.*, COUNT(v.estrellas) as total_votos
                  FROM " . $this->table . " c
                  LEFT JOIN valoracion_curso v ON c.id_curso = v.id_curso
                  WHERE c.estado = 'publicado'
                  GROUP BY c.id_curso";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorProfesor($id_profesor)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id_profesor = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar($id_profesor, $titulo, $descripcion, $precio, $imagen)
    {
        try {
            $sql = "INSERT INTO " . $this->table . " (id_profesor, titulo, descripcion, precio, imagen, estado) 
                    VALUES (?, ?, ?, ?, ?, 'publicado')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_profesor, $titulo, $descripcion, $precio, $imagen]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id_curso, $id_profesor)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id_curso = ? AND id_profesor = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_curso, $id_profesor]);
    }

    public function obtenerPorId($id_curso, $id_profesor)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id_curso = ? AND id_profesor = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso, $id_profesor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id_curso, $id_profesor, $titulo, $descripcion, $precio, $imagen)
    {
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

    public function obtenerDetallePublico($id_curso)
    {
        $query = "SELECT c.*, COUNT(v.estrellas) as total_votos
                  FROM " . $this->table . " c
                  LEFT JOIN valoracion_curso v ON c.id_curso = v.id_curso
                  WHERE c.id_curso = ? AND c.estado = 'publicado'
                  GROUP BY c.id_curso";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_curso]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

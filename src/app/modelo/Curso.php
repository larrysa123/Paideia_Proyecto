<?php
class Curso
{
    private $db;
    private $table = 'Curso';

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function obtenerTodos($id_usuario = null)
    {
        // Si hay un usuario logueado, cruzamos con Inscripcion para saber si lo ha comprado
        if ($id_usuario) {
            $query = "SELECT c.*, COUNT(v.estrellas) as total_votos,
                             MAX(CASE WHEN i.id_usuario IS NOT NULL THEN 1 ELSE 0 END) as comprado
                      FROM " . $this->table . " c
                      LEFT JOIN valoracion_curso v ON c.id_curso = v.id_curso
                      LEFT JOIN Inscripcion i ON c.id_curso = i.id_curso AND i.id_usuario = ?
                      WHERE c.estado = 'publicado'
                      GROUP BY c.id_curso";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_usuario]);
        } else {
            // Si es un visitante anónimo, simplemente devolvemos 0 en "comprado"
            $query = "SELECT c.*, COUNT(v.estrellas) as total_votos, 0 as comprado
                      FROM " . $this->table . " c
                      LEFT JOIN valoracion_curso v ON c.id_curso = v.id_curso
                      WHERE c.estado = 'publicado'
                      GROUP BY c.id_curso";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        
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

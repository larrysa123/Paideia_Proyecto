<?php
class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    // Obtener todos los usuarios (con su rol)
    public function obtenerUsuarios()
    {
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.email, r.nombre_rol 
                  FROM Usuario u 
                  INNER JOIN Rol r ON u.id_rol = r.id_rol 
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los datos de un usuario concreto para precargar el modal 
    public function obtenerUsuarioPorId($id_usuario)
    {
        $query = "SELECT id_usuario, nombre, apellidos, email, id_rol FROM Usuario WHERE id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    // Obtener todos los cursos (con el nombre del profesor)
    public function obtenerCursos()
    {
        $query = "SELECT c.id_curso, c.titulo, c.precio, c.estado, u.nombre AS profesor 
                  FROM Curso c 
                  INNER JOIN Usuario u ON c.id_profesor = u.id_usuario 
                  ORDER BY c.fecha_creacion DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar un usuario
    public function eliminarUsuario($id_usuario)
    {
        // Por seguridad, evitamos que el admin se borre a sí mismo accidentalmente
        if ($id_usuario == $_SESSION['user']['id_usuario']) {
            return false;
        }
        $query = "DELETE FROM Usuario WHERE id_usuario = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_usuario]);
    }

    // Eliminar un curso
    public function eliminarCurso($id_curso)
    {
        $query = "DELETE FROM Curso WHERE id_curso = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_curso]);
    }


    // Actualizar los datos de un usuario (con o sin cambio de contraseña) ---
    public function actualizarUsuario($id_usuario, $nombre, $apellidos, $email, $id_rol, $password = null)
    {
        try {
            if (!empty($password)) {
                // Si el administrador cambia la clave, la encriptamos de forma segura
                $password_encriptada = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE Usuario SET nombre = ?, apellidos = ?, email = ?, id_rol = ?, password = ? WHERE id_usuario = ?";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([$nombre, $apellidos, $email, $id_rol, $password_encriptada, $id_usuario]);
            } else {
                // Si la deja en blanco, actualizamos el resto de campos respetando la clave actual
                $query = "UPDATE Usuario SET nombre = ?, apellidos = ?, email = ?, id_rol = ? WHERE id_usuario = ?";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([$nombre, $apellidos, $email, $id_rol, $id_usuario]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }


    // Cambiar el estado de un curso (Aprobar / Ocultar)
    public function cambiarEstadoCurso($id_curso, $nuevo_estado)
    {
        $query = "UPDATE Curso SET estado = ? WHERE id_curso = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$nuevo_estado, $id_curso]);
    }
}

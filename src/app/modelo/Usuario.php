<?php

class Usuario
{
    private $db;

    public function __construct()
    {
        // Nos conectamos a la BD al instanciar la clase
        $this->db = Conexion::conectar();
    }

    // REGISTRAR UN NUEVO USUARIO

    public function registrar($nombre, $apellidos, $email, $password)
    {
        try {
            // 1. Encriptamos la contraseña 
            $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

            // 2. Asignamos el rol 1 (Alumno) por defecto
            $id_rol = 1;

            // 3. Preparamos la consulta SQL
            $sql = "INSERT INTO Usuario (nombre, apellidos, email, password, id_rol) 
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            // 4. Ejecutamos pasándole los datos limpios
            $stmt->execute([$nombre, $apellidos, $email, $password_encriptada, $id_rol]);

            return true; // Todo ha ido bien

        } catch (PDOException $e) {
            // Si el correo ya existe, MySQL nos lanza el código 23000 (porque es UNIQUE)
            if ($e->getCode() == 23000) {
                return "email_duplicado";
            }
            return false; // Otro tipo de error
        }
    }


    // Perfil 
    public function obtenerPorID($id)
    {
        $sql = "SELECT id_usuario, nombre, apellidos, email, telefono, foto, id_rol 
                FROM Usuario WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Actualizar
    public function actualizar($data)
    {
        $sql = "UPDATE Usuario 
                SET nombre = ?, apellidos = ?, telefono = ? 
                WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'],
            $data['telefono'],
            $data['id_usuario']
        ]);
    }

    // Obtener un usuario por su email (LOGIN)
    public function obtenerPorEmail($email)
    {
        // Preparamos la consulta SQL
        $sql = "SELECT * FROM Usuario WHERE email = ?";

        try {
            $stmt = $this->db->prepare($sql);
            // Ejecutamos pasándole el email
            $stmt->execute([$email]);

            // Devolvemos la fila encontrada en formato array asociativo
            // Si no encuentra nada, devolverá false automáticamente
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}

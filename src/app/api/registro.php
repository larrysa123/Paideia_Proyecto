<?php
// src/api/registro.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/db.php';

// 1. Recibir los datos JSON del cliente
$data = json_decode(file_get_contents("php://input"));

// Verificar que llegan los datos necesarios
if(
    !empty($data->nombre) && 
    !empty($data->apellidos) && 
    !empty($data->email) && 
    !empty($data->password)
){
    try {
        // 2. Comprobar si el email ya existe
        $checkQuery = "SELECT id_usuario FROM Usuario WHERE email = :email";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            // El email ya está registrado
            echo json_encode(["error" => "Este correo electrónico ya está registrado."]);
        } else {
            // 3. Crear el nuevo usuario
            // Asignamos rol '1' (Alumno) por defecto.
            // IMPORTANTE: Encriptamos la contraseña con password_hash
            $query = "INSERT INTO Usuario (nombre, apellidos, email, password, id_rol) 
                      VALUES (:nombre, :apellidos, :email, :password, 1)";
            
            $stmt = $pdo->prepare($query);

            // Sanitizar y asignar valores
            $nombre = htmlspecialchars(strip_tags($data->nombre));
            $apellidos = htmlspecialchars(strip_tags($data->apellidos));
            $email = htmlspecialchars(strip_tags($data->email));
            $password_hash = password_hash($data->password, PASSWORD_DEFAULT); // Hash seguro

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);

            if($stmt->execute()){
                http_response_code(201); // Código 201 = Creado
                echo json_encode(["message" => "Usuario registrado exitosamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["error" => "No se pudo registrar el usuario."]);
            }
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos. Faltan campos obligatorios."]);
}
?>
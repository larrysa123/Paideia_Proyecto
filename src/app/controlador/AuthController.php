<?php

class AuthController {

    public function loginUsuario($datos) {
        
        // 1. Validar que no lleguen vacíos
        if (empty($datos['email']) || empty($datos['password'])) {
            return ["status" => "error", "mensaje" => "Por favor, rellena todos los campos."];
        }

        $email = $datos['email'];
        $password = $datos['password'];

        // 2. Usamos tu Modelo con tu idioma: obtenerPorEmail
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        // 3. Verificamos usando la lógica de contraseñas de PHP
        if ($usuario && password_verify($password, $usuario['password'])) {
            
            // 4. Creamos la sesión 
            $_SESSION['user'] = $usuario; 
            
            // 5. Devolvemos la respuesta para que JS la lea
            return [
                "status" => "success", 
                "mensaje" => "¡Bienvenido de nuevo!"
            ];
            
        } else {
            // Devolvemos el error para que JS lo pinte en rojo
            return [
                "status" => "error", 
                "mensaje" => "Correo electrónico o contraseña incorrectos."
            ];
        }
    }

    public function logout() {
        session_destroy();
        // Asegúrate de que esta ruta coincida con tu constante BASE_URL si la cambias
        header("Location: /PAIDEIA_PROYECTO/src/public/index.php"); 
        exit();
    }
}
?>
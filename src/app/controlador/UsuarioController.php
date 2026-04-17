<?php

class UsuarioController {
    
  
    // Registrar por API (Fetch/JSON)
    public function registrarUsuario($datos) {
        if (isset($datos['nombre']) && isset($datos['apellidos']) && isset($datos['email']) && isset($datos['password'])) {
            $usuarioModel = new Usuario();
            $resultado = $usuarioModel->registrar($datos['nombre'], $datos['apellidos'], $datos['email'], $datos['password']);

            if ($resultado === true) {
                return ["status" => "success", "mensaje" => "¡Cuenta creada con éxito! Ya puedes iniciar sesión."];
            } else if ($resultado === "email_duplicado") {
                return ["status" => "error", "mensaje" => "Ese correo electrónico ya está registrado en Paideia."];
            } else {
                return ["status" => "error", "mensaje" => "Error interno al crear el usuario. Inténtalo más tarde."];
            }
        } else {
            return ["status" => "error", "mensaje" => "Faltan datos en el formulario."];
        }
    }

    /*
    
    public function perfil($id) {
        $usuarioModel = new Usuario();
        // Crear la función getById() en el modelo más adelante
        $usuario = $usuarioModel->getById($id);
        
        
        require __DIR__ . '/../../public/vista/usuario/perfil.php';
    }

    public function actualizar($data) {
        $usuarioModel = new Usuario();
        // Crear la función update() en el modelo más adelante
        $usuarioModel->update($data);
        
        
        header("Location: /public/vista/usuario/perfil.php");
    }

    */
}
?>
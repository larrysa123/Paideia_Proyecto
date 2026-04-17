<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    //Verificar las credenciales introducidas
    public function login($email, $password) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getByEmail($email);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['user'] = $usuario;
            header("Location: /views/cursos/catalogo.php");
        } else {
            echo "Credenciales incorrectas";
        }
    }


    //Salgo y redirijo a index
    public function logout() {
        session_destroy();
        header("Location: /index.php");
    }

    //Recibo los datos del usuario y ahora tengo que gestionarlo en el Modelo
    //Luego me envia al login
    public function register($data) {
        $usuarioModel = new Usuario();
        $usuarioModel->create($data);
        header("Location: /views/auth/login.php");
    }
}

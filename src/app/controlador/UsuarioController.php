<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {

    public function perfil($id) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($id);
        require __DIR__ . '/../../views/usuario/perfil.php';
    }

    public function actualizar($data) {
        $usuarioModel = new Usuario();
        $usuarioModel->update($data);
        header("Location: /views/usuario/perfil.php");
    }
}

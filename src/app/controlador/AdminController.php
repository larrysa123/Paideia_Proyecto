<?php
class AdminController {

    private function verificarPermisos() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
            return false;
        }
        return true;
    }

    public function dashboard() {
        if (!$this->verificarPermisos()) {
            return ["status" => "error", "mensaje" => "Acceso denegado."];
        }

        $adminModel = new Admin();
        
        // Empaquetamos todo en una sola respuesta
        return [
            "status" => "success",
            "data" => [
                "usuarios" => $adminModel->obtenerUsuarios(),
                "cursos" => $adminModel->obtenerCursos()
            ]
        ];
    }
}
?>
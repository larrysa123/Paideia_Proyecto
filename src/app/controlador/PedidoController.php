<?php
class PedidoController {

    public function procesarPago($datos) {
        // Protección 1: Estar logueado como alumno
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
            return ["status" => "error", "code" => "NO_LOGIN", "mensaje" => "Debes iniciar sesión como alumno para inscribirte."];
        }

        if (isset($datos['id_curso']) && isset($datos['precio'])) {
            $id_usuario = $_SESSION['user']['id_usuario'];
            $id_curso = $datos['id_curso'];
            $precio = floatval($datos['precio']);

            // Protección 2: Verificar si ya está matriculado (Evita que pague dos veces)
            $inscripcionModel = new Inscripcion();
            if ($inscripcionModel->verificar($id_usuario, $id_curso)) {
                return ["status" => "error", "code" => "YA_INSCRITO", "mensaje" => "Ya posees este curso. Ve a 'Mis Cursos' para verlo."];
            }

            // Procesar la compra
            $pedidoModel = new Pedido();
            $exito = $pedidoModel->procesarCompra($id_usuario, $id_curso, $precio);

            if ($exito) {
                return ["status" => "success", "mensaje" => "¡Pago procesado con éxito! Bienvenido al curso."];
            } else {
                return ["status" => "error", "mensaje" => "Hubo un error al procesar el pago en la base de datos."];
            }
        }
        return ["status" => "error", "mensaje" => "Faltan datos para procesar el pedido."];
    }
}
?>
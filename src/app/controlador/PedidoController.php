<?php
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController {

    public function crearPedido($usuario_id, $cursos) {
        $pedidoModel = new Pedido();
        $pedidoId = $pedidoModel->create($usuario_id);

        foreach ($cursos as $curso) {
            $pedidoModel->addDetalle($pedidoId, $curso['id'], $curso['precio']);
        }

        header("Location: /views/pedidos/historial.php");
    }

    public function historial($usuario_id) {
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->getByUsuario($usuario_id);
        require __DIR__ . '/../../views/pedidos/historial.php';
    }
}

<?php
class Pedido {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    public function procesarCompra($id_usuario, $id_curso, $precio) {
        try {
            // Empezamos la transacción
            $this->db->beginTransaction();

            // 1. Crear el Pedido principal
            $queryPedido = "INSERT INTO Pedido (id_usuario, total, metodo_pago, estado) 
                            VALUES (?, ?, 'Tarjeta Simulada', 'completado')";
            $stmtPedido = $this->db->prepare($queryPedido);
            $stmtPedido->execute([$id_usuario, $precio]);
            
            // Recuperamos el ID del pedido que se acaba de generar
            $id_pedido = $this->db->lastInsertId();

            // 2. Crear la línea de Detalle_Pedido
            $queryDetalle = "INSERT INTO Detalle_Pedido (id_pedido, id_curso, precio_uni, cantidad) 
                             VALUES (?, ?, ?, 1)";
            $stmtDetalle = $this->db->prepare($queryDetalle);
            $stmtDetalle->execute([$id_pedido, $id_curso, $precio]);

            // 3. Matricular al alumno (Inscripcion)
            $queryInscripcion = "INSERT INTO Inscripcion (id_usuario, id_curso) VALUES (?, ?)";
            $stmtInscripcion = $this->db->prepare($queryInscripcion);
            $stmtInscripcion->execute([$id_usuario, $id_curso]);

            // Si todo ha ido bien, cerramos el trato
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Si algo falla, revertimos absolutamente todo
            $this->db->rollBack();
            return false;
        }
    }
}
?>
<?php
// src/api/cursos.php

// 1. Cabeceras: Le decimos al navegador que vamos a enviar JSON y permitimos acceso desde fuera (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 2. Incluimos la conexión a la base de datos
include_once 'config/db.php';

try {
    // 3. Preparamos la consulta SQL
    // Solo queremos los cursos que estén 'publicado'
    $query = "SELECT * FROM Curso WHERE estado = 'publicado'";
    $stmt = $pdo->prepare($query);
    
    // 4. Ejecutamos la consulta
    $stmt->execute();
    
    // 5. Obtenemos los resultados y contamos cuántos hay
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC); // FETCH_ASSOC quita índices numéricos duplicados

    // 6. Devolvemos la respuesta en formato JSON
    echo json_encode($cursos);

} catch (PDOException $e) {
    // Si hay error, devolvemos un JSON con el mensaje
    echo json_encode(["error" => "Error al obtener cursos: " . $e->getMessage()]);
}
?>
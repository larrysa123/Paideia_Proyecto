<?php

require_once 'config/db.php';


//1.- Cabezera OBLIGATORIA para que JS entienda que es JSON
header ('Content-Type: application/json');


try{

//2.- Consulta
    $sql = "SELECT id_curso, titulo, precio, imagen FROM curso";
    $stmt = $pdo-> prepare ($sql);
    $stmt->execute();

//3.- Obtener datos
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//4.- Imprimir JSON

echo json_encode($cursos);
}catch (Exception $e){
    //Si falla, avisamos al frontend
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
}



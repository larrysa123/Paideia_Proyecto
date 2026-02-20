<?php

class Curso{

    private $conn;
    private $table = 'curso';


    public function __construct($db)
    {
        $this->conn = $db;
    }

    
}
?>
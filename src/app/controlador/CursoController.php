<?php
require_once __DIR__ . '/../models/Curso.php';

class CursoController {



    public function catalogo() {
        $cursoModel = new Curso();
        $cursos = $cursoModel->getAll();
        require __DIR__ . '/../../views/cursos/catalogo.php';
    }

    public function ver($id) {
        $cursoModel = new Curso();
        $curso = $cursoModel->getById($id);
        require __DIR__ . '/../../views/cursos/detalle.php';
    }

    public function crear($data) {
        $cursoModel = new Curso();
        $cursoModel->create($data);
        header("Location: /views/admin/cursos.php");
    }

    public function eliminar($id) {
        $cursoModel = new Curso();
        $cursoModel->delete($id);
        header("Location: /views/admin/cursos.php");
    }
}

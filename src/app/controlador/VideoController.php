<?php
require_once __DIR__ . '/../models/Video.php';

class VideoController {

    public function ver($id) {
        $videoModel = new Video();
        $video = $videoModel->getById($id);
        require __DIR__ . '/../../views/videos/ver.php';
    }

    public function agregar($data) {
        $videoModel = new Video();
        $videoModel->create($data);
        header("Location: /views/cursos/detalle.php?id=" . $data['id_curso']);
    }
}

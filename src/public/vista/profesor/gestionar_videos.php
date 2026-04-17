<?php
require_once __DIR__ . '/../../../app/config/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
    header("Location: " . RUTA_INICIO);
    exit();
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <a href="panel.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>

    <h2 class="fw-bold mb-4">Gestionar Temario del Curso</h2>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-paideia text-white fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Añadir Nueva Lección
                </div>
                <div class="card-body">
                    <form id="formVideo">
                        <input type="hidden" id="id_curso_video">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título de la lección</label>
                            <input type="text" id="titulo_video" class="form-control" placeholder="Ej: Introducción al tema 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enlace de YouTube</label>
                            <input type="url" id="url_video" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Orden (Nº)</label>
                                <input type="number" id="orden_video" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-paideia w-100">Guardar Vídeo</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold text-dark">
                    <i class="bi bi-list-ol me-2"></i>Lecciones publicadas
                </div>
                <div class="card-body">
                    <div id="cargando-videos" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <ul id="lista-videos" class="list-group list-group-flush d-none">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= RUTA_JS ?>gestionar_videos.js"></script>
<?php
require_once __DIR__ . '/../../../app/config/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
    header("Location: " . RUTA_INICIO);
    exit();
}
require_once __DIR__ . '/../includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<div class="container py-5">
    <a href="panel.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>

    <h2 class="fw-bold mb-4">Gestionar Temario del Curso</h2>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-paideia text-white fw-bold" id="titulo-formulario">
                    <i class="bi bi-plus-circle me-2"></i>Añadir Nueva Lección
                </div>
                <div class="card-body">
                    <form id="formVideo">
                        <input type="hidden" id="id_curso_video">
                        <input type="hidden" id="id_video_editar"> <div class="mb-3">
                            <label class="form-label fw-bold">Título de la lección</label>
                            <input type="text" id="titulo_video" class="form-control" placeholder="Ej: Introducción al tema 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enlace de YouTube</label>
                            <input type="url" id="url_video" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="btn-guardar-video" class="btn btn-paideia">Guardar Vídeo</button>
                            <button type="button" id="btn-cancelar-edicion" class="btn btn-outline-secondary d-none" onclick="cancelarEdicion()">Cancelar Edición</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold text-dark d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ol me-2"></i>Lecciones publicadas</span>
                    <small class="text-muted fw-normal"><i class="bi bi-arrows-move me-1"></i>Arrastra para reordenar</small>
                </div>
                <div class="card-body p-0">
                    <div id="cargando-videos" class="text-center py-4">
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
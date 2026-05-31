<?php
require_once __DIR__ . '/../../../app/config/config.php';

// CERROJO VIP (Solo Alumnos)
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
    header("Location: " . RUTA_INICIO);
    exit();
}

$id_curso = $_GET['id'] ?? null;
if (!$id_curso) {
    header("Location: mis_cursos.php");
    exit();
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4 pantalla-clase-contenedor">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="mis_cursos.php" class="btn-volver">
                <i class="bi bi-arrow-left me-2"></i> Volver a Mis Cursos
            </a>
            <button type="button" class="btn btn-warning btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalValoracion">
                <i class="bi bi-star-fill text-dark"></i> Valorar Curso Completo
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ratio ratio-16x9 bg-black rounded-3 overflow-hidden shadow-sm mb-3 border border-light">
                    <iframe id="reproductor-youtube" src="" allowfullscreen></iframe>
                </div>
                <h3 id="titulo-leccion-actual" class="fw-bold mt-3 text-dark">Selecciona una lección</h3>
                <p id="desc-leccion-actual" class="text-muted"></p>

                <div class="card border-0 shadow-sm mb-4 bg-light d-none" id="bloque-estrellas-video">
                    <div class="card-body d-flex align-items-center justify-content-between py-2">
                        <div class="d-flex align-items-center">
                            <span class="text-dark fw-bold me-3">¿Se entiende bien esta lección?</span>
                            <div id="estrellas-video" class="fs-4 text-warning cursor-pointer" data-puntuacion="0">
                                <i class="bi bi-star" data-value="1"></i>
                                <i class="bi bi-star" data-value="2"></i>
                                <i class="bi bi-star" data-value="3"></i>
                                <i class="bi bi-star" data-value="4"></i>
                                <i class="bi bi-star" data-value="5"></i>
                            </div>
                        </div>
                        <div id="feedback-video" class="small text-muted fw-bold"></div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4 d-none" id="bloque-foro-video">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-chat-dots text-primary me-2"></i>Foro de Dudas: <span id="titulo-foro-actual" class="text-muted fs-6"></span></h5>
                    </div>
                    <div class="card-body bg-light">
                        <div class="mb-4 bg-white p-3 rounded shadow-sm border">
                            <textarea id="texto-nuevo-comentario" class="form-control border-0 mb-2" rows="2" placeholder="¿Tienes alguna duda sobre este vídeo? Escríbela aquí para que el profesor u otros alumnos te ayuden..."></textarea>
                            <div class="text-end">
                                <button id="btn-enviar-comentario" class="btn btn-paideia btn-sm">Publicar Duda</button>
                            </div>
                        </div>

                        <div id="contenedor-comentarios">
                            <div class="text-center text-muted py-3">Cargando foro...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2"></i>Contenido del Curso</h5>
                    </div>
                    <div class="card-body p-0 lista-lecciones-scroll">
                        <div id="cargando-temario" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                        <div id="lista-lecciones" class="list-group list-group-flush rounded-bottom"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalValoracion" tabindex="-1" aria-labelledby="modalValoracionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-paideia text-white">
                <h5 class="modal-title fw-bold" id="modalValoracionLabel"><i class="bi bi-star-fill text-warning me-2"></i>Valorar Curso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold text-dark">¿Cuántas estrellas le das?</label>
                    <div id="modal-estrellas-curso" class="fs-1 text-warning cursor-pointer" data-puntuacion="0">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="modal-texto-curso" class="form-label fw-bold text-dark">Tu opinión (Opcional):</label>
                    <textarea class="form-control" id="modal-texto-curso" rows="3" placeholder="Si quieres, cuéntanos qué te ha parecido..."></textarea>
                </div>
                <div id="modal-feedback" class="small text-danger text-center fw-bold"></div>
            </div>
            <div class="modal-footer d-flex justify-content-between bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-paideia" id="btn-guardar-resena">Guardar Valoración</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="id_curso_oculto" value="<?= htmlspecialchars($id_curso) ?>">
<input type="hidden" id="id_video_actual" value="">

<script src="<?= RUTA_JS ?>clase.js?v=<?= time() ?>"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
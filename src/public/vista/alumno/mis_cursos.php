<?php
require_once __DIR__ . '/../../../app/config/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
    header("Location: " . RUTA_INICIO);
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <h5 class="mb-4 text-dark titulo-panel">Panel del Alumno</h5>

    <ul class="nav nav-underline mb-4 border-bottom" id="panel-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                <i class="bi bi-journal-bookmark-fill me-2"></i>Mis Cursos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="certificados-tab" data-bs-toggle="tab" data-bs-target="#certificados" type="button" role="tab">
                <i class="bi bi-award me-2"></i>Certificados
            </button>
        </li>
    </ul>

    <div class="tab-content" id="panel-tabsContent">
        <div class="tab-pane fade show active" id="cursos" role="tabpanel" tabindex="0">
            <a href="<?= RUTA_INICIO ?>" class="btn btn-paideia px-4 mb-4">
                <i class="bi bi-search me-1"></i> Explorar Catálogo
            </a>
            <div id="mensaje-vacio" class="alert alert-info d-none text-center py-4 mb-4">
                <h4>Todavía no estás inscrito en ningún curso.</h4>
                <p>¡Visita nuestro catálogo y empieza a aprender hoy mismo!</p>
            </div>
            <div id="grid-mis-cursos" class="row g-4">
                <div id="cargando-mis-cursos" class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="certificados" role="tabpanel" tabindex="0">
            <div class="text-center py-5">
                <i class="bi bi-award text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Aún no tienes certificados</h5>
                <p class="text-muted">Completa el 100% de un curso para obtener tu diploma.</p>
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

<input type="hidden" id="modal_id_curso_oculto" value="">

<script src="<?= RUTA_JS ?>mis_cursos.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
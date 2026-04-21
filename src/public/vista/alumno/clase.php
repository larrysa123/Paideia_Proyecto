<?php
require_once __DIR__ . '/../../../app/config/config.php';

// CERROJO VIP (Solo Alumnos)
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
    header("Location: " . RUTA_INICIO);
    exit();
}

// Necesitamos saber a qué curso está intentando entrar
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
            <a href="mis_cursos.php" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver a Mis Cursos
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ratio ratio-16x9 bg-black rounded-3 overflow-hidden shadow-sm mb-3 border border-light">
                    <iframe id="reproductor-youtube" src="" allowfullscreen></iframe>
                </div>
                <h3 id="titulo-leccion-actual" class="fw-bold mt-3 text-dark">Selecciona una lección</h3>
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body d-flex align-items-center justify-content-between py-2">
                        <div class="d-flex align-items-center">
                            <span class="text-dark fw-bold me-3">¿Qué te parece el curso?</span>
                            <div id="estrellas-curso" class="fs-4 text-warning cursor-pointer">
                                <i class="bi bi-star" data-value="1"></i>
                                <i class="bi bi-star" data-value="2"></i>
                                <i class="bi bi-star" data-value="3"></i>
                                <i class="bi bi-star" data-value="4"></i>
                                <i class="bi bi-star" data-value="5"></i>
                            </div>
                        </div>
                        <div id="feedback-valoracion" class="small text-muted"></div>
                    </div>
                </div>
                <p id="desc-leccion-actual" class="text-muted"></p>
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

                        <div id="lista-lecciones" class="list-group list-group-flush rounded-bottom">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<input type="hidden" id="id_curso_oculto" value="<?= htmlspecialchars($id_curso) ?>">
<script src="<?= RUTA_JS ?>clase.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
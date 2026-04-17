<?php
// 1. Conectamos con el cerebro de la app
require_once __DIR__ . '/../../../app/config/config.php';

// =====================================================================
// 2. EL CERROJO VIP (Solo Alumnos)
// =====================================================================
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 1) {
    header("Location: " . RUTA_INICIO);
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container mt-5">

    <h5 class="mb-4 text-dark titulo-panel">
        Panel del Alumno
    </h5>

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
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando tus cursos...</span>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= RUTA_JS ?>mis_cursos.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
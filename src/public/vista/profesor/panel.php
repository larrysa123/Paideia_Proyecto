<?php
// 1. Conectamos con el cerebro de la app
require_once __DIR__ . '/../../../app/config/config.php';

// =====================================================================
// 2. EL CERROJO VIP (Solo Profesores)
// =====================================================================
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
    header("Location: " . RUTA_INICIO);
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container mt-5">

    <h5 class="mb-4 text-dark titulo-panel">
        Panel del Profesor
    </h5>

    <ul class="nav nav-underline mb-4 border-bottom" id="panel-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                <i class="bi bi-book me-2"></i>Mis Cursos (3)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comentarios-tab" data-bs-toggle="tab" data-bs-target="#comentarios" type="button" role="tab">
                <i class="bi bi-chat-left-text me-2"></i>Comentarios (0)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="panel-tabsContent">

        <div class="tab-pane fade show active" id="cursos" role="tabpanel" tabindex="0">

            <a href="crear_curso.php" class="btn btn-paideia px-4 mb-4">
                <i class="bi bi-plus-lg me-1"></i> Crear Nuevo Curso
            </a>

            <div id="contenedor-mis-cursos" class="row g-4">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando tus cursos...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="comentarios" role="tabpanel" tabindex="0">
            <div class="text-center py-5">
                <i class="bi bi-chat-square-dots text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Aún no hay comentarios</h5>
                <p class="text-muted">Los mensajes de tus alumnos aparecerán aquí.</p>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= RUTA_JS ?>panel.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
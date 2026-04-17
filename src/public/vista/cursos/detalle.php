<?php
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <a href="<?= RUTA_INICIO ?>" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Volver a Cursos
    </a>

    <div id="cargando-detalle" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div id="contenido-detalle" class="row d-none">

        <div class="col-md-7 mb-4">
            <img id="det-imagen" src="" class="img-fluid rounded shadow-lg w-100" alt="Portada del curso" style="max-height: 400px; object-fit: cover;">
        </div>

        <div class="col-md-5">
            <h1 id="det-titulo" class="fw-bold mb-3">Cargando...</h1>
            <p id="det-descripcion" class="text-muted fs-5 mb-4"></p>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <h2 id="det-precio" class="fw-bold text-primary mb-3">0.00 €</h2>
                    <button id="btn-inscribirse" class="btn btn-paideia btn-lg w-100 rounded-pill mb-2">
                        <i class="bi bi-cart-plus me-2"></i> INSCRIBIRSE AHORA
                    </button>
                    <small class="text-muted">Acceso de por vida. Pago seguro.</small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="<?= RUTA_JS ?>detalle.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
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
                    <small class="text-muted d-block mt-2"><i class="bi bi-shield-lock"></i> Acceso de por vida. Pago 100% seguro.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Pago Seguro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="mb-4 bg-light p-3 rounded text-center border">
                    <h6 class="text-muted mb-1 text-uppercase small">Total a pagar</h6>
                    <h2 class="fw-bold text-dark mb-0" id="modal-precio-total">0.00 €</h2>
                </div>
                
                <form id="form-pago">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre en la tarjeta</label>
                        <input type="text" class="form-control" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Número de tarjeta</label>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Caducidad (MM/AA)</label>
                            <input type="text" class="form-control" placeholder="12/26" maxlength="5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">CVC</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                    <button type="submit" id="btn-procesar-pago" class="btn btn-paideia w-100 py-2 fw-bold fs-5">
                        <i class="bi bi-lock-fill me-1"></i> Pagar Ahora
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= RUTA_JS ?>detalle.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
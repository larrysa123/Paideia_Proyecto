<?php
// 1. Conectamos con el cerebro de la app
require_once __DIR__ . '/../../../app/config/config.php';

// 2. EL CERROJO VIP (Solo Profesores)
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 2) {
    header("Location: " . RUTA_INICIO);
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container mt-5 mb-5">

    <a href="panel.php" class="btn-volver mb-4">
        <i class="bi bi-arrow-left me-2"></i> Volver al Panel
    </a>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="contenedor-form">
                <h3 class="titulo-form-paideia text-center mb-4">
                    Crear Nuevo Curso
                </h3>

                <p class="text-muted text-center mb-4 card-desc-panel">
                    Edita los detalles del curso seleccionado.
                </p>

                <form id="formEditarCurso">

                    <div class="mb-4">
                        <label for="titulo" class="form-label form-label-paideia">Título del Curso *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required
                            placeholder="Ej: Curso de React desde cero" maxlength="150">
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label form-label-paideia">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                            placeholder="Explica qué aprenderán los alumnos en este curso..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="precio" class="form-label form-label-paideia">Precio (€) *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="precio" name="precio" required
                                    placeholder="0.00" step="1.00" min="0">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="imagen" class="form-label form-label-paideia">Nombre de la Imagen</label>
                            <input type="text" class="form-control" id="imagen" name="imagen"
                                placeholder="Ej: ajedrez.png">
                            <div class="form-text form-text-paideia">Escribe solo el nombre del archivo con su extensión (.png, .jpg). La imagen debe estar subida en la carpeta de assets.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-paideia py-2 fs-5">
                            <i class="bi bi-save me-2"></i> Guardar y Publicar Curso
                        </button>
                    </div>
                    <input type="hidden" id="id_curso" name="id_curso">
                </form>

            </div>
        </div>

        <script src="<?= RUTA_JS ?>editar_curso.js"></script>
        <?php
        // require_once __DIR__ . '/../includes/footer.php'; 
        ?>
<?php require_once __DIR__ . '/vista/includes/header.php'; ?>


<div class="container py-5">


    <h1 class="text-center mb-5 display-4 fw-bold">Biblioteca de Cursos</h1>

    <div id="contenedor-cursos" class="row g-4">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= RUTA_JS ?>cursos.js"></script>
</body>

</html>
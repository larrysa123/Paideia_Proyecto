<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="contenedor-form">
                <h2 class="text-center mb-4 fw-bold">Crear una cuenta</h2>
                
                <form id="formRegistro">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre</label>
                        <input type="text" class="form-control bg-light" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellidos" class="form-label fw-bold">Apellidos</label>
                        <input type="text" class="form-control bg-light" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo electrónico</label>
                        <input type="email" class="form-control bg-light" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control bg-light" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-paideia w-100 py-2 fs-5 rounded-pill">Comenzar mi aprendizaje</button>
                </form>

                <div id="respuestaServidor" class="mt-3 text-center fw-bold fs-5"></div>
                
                <div class="mt-4 text-center">
                    <p class="text-muted">¿Ya tienes cuenta? <a href="login.php" style="color: var(--color-primary); font-weight: bold; text-decoration: none;">Inicia sesión aquí</a></p>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= RUTA_JS ?>registro.js"></script>
</body>
</html>
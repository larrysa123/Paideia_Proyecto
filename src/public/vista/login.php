<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="contenedor-form">
                <h2 class="text-center mb-4 fw-bold">Acceso a Paideia</h2>
                
                <form id="formLogin">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo electrónico</label>
                        <input type="email" class="form-control bg-light" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control bg-light" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-paideia w-100 py-2 fs-5 rounded-pill">Login</button>
                </form>

                <div id="respuestaServidor" class="mt-3 text-center fw-bold fs-5"></div>
                <div class="inf">
                    <h4>Usuarios de prueba</h4>
                    <p>alumno@paideia.com / alumno</p>
                    <p>profe@paideia.com / profe</p>
                    <p>admin@paideia.com / admin</p>
                </div>
              
            </div>

        </div>
    </div>
</div>

<script src="<?= RUTA_JS ?>login.js"></script>
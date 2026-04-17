<?php
// 1. Conectamos con el cerebro de la app (3 saltos hacia atrás)
require_once __DIR__ . '/../../../app/config/config.php';

// =====================================================================
// 2. EL CERROJO DE MÁXIMA SEGURIDAD (Solo Administradores)
// =====================================================================
// Si no estás logueado, O si tu rol NO es el 3 (Admin)... ¡A la calle!
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
    header("Location: " . RUTA_INICIO);
    exit(); // Cortamos la ejecución al instante
}

// =====================================================================
// 3. EL CONTENIDO DEL PANEL
// =====================================================================
// Cargamos el header (subimos un nivel a "vista" y entramos a "includes")
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="color: var(--color-primary);">Panel de Control General</h1>
        <p class="text-muted" style="font-size: 1.1rem;">
            Bienvenido, Administrador <strong><?= $_SESSION['user']['nombre'] ?></strong>.
        </p>
    </div>

    <div class="row g-4 mt-3">
        
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <h3 class="card-title">Usuarios</h3>
                <p class="card-text mb-4">Gestiona alumnos, profesores y administradores de la plataforma.</p>
                <div class="mt-auto">
                    <a href="#" class="btn btn-paideia w-100">Ver Usuarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <h3 class="card-title">Todos los Cursos</h3>
                <p class="card-text mb-4">Supervisa, edita o elimina cualquier curso creado por los profesores.</p>
                <div class="mt-auto">
                    <a href="#" class="btn btn-paideia w-100">Ver Cursos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <h3 class="card-title">Ajustes</h3>
                <p class="card-text mb-4">Configuración general de la plataforma Paideia.</p>
                <div class="mt-auto">
                    <a href="#" class="btn btn-paideia w-100">Ir a Ajustes</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php 
// require_once __DIR__ . '/../includes/footer.php'; 
?>
<?php
require_once __DIR__ . '/../../../app/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paideia</title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="<?= RUTA_CSS ?>estilos.css" rel="stylesheet">

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-paideia">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="<?= RUTA_INICIO ?>">Paideia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>

                        <?php 
                        // Extraemos el rol de la sesión para usarlo cómodamente
                        $rol = $_SESSION['user']['id_rol']; 
                        ?>

                        <?php if ($rol == 1): // ALUMNO ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="<?= RUTA_VISTAS ?>alumno/mis_cursos.php">Mis Cursos</a>
                            </li>
                        
                        <?php elseif ($rol == 2): // PROFESOR ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold" href="<?= RUTA_VISTAS ?>profesor/panel.php">Panel de Profesor</a>
                            </li>
                        
                        <?php elseif ($rol == 3): // ADMINISTRADOR ?>
                            <li class="nav-item">
                                <a class="nav-link text-info fw-bold" href="<?= RUTA_VISTAS ?>admin/panel.php">Panel de Control</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item d-flex align-items-center ms-lg-3">
                            <span class="nav-link text-white fw-bold" style="cursor: default;">
                                ¡Hola, <?= $_SESSION['user']['nombre'] ?>!
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light ms-2 px-3" href="<?= BASE_URL ?>app/api/logout.php">
                                Cerrar sesión
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?= RUTA_VISTAS ?>registro.php">Registrarse</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light ms-2 px-3" href="<?= RUTA_VISTAS ?>login.php">Acceder</a>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
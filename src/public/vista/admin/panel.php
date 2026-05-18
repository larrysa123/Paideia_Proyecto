<?php
require_once __DIR__ . '/../../../app/config/config.php';

// CERROJO VIP (Solo Administradores - Rol 3)
if (!isset($_SESSION['user']) || $_SESSION['user']['id_rol'] != 3) {
    header("Location: " . RUTA_INICIO);
    exit();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5 mb-5">
    <h5 class="mb-4 text-dark titulo-panel">
        <i class="bi bi-shield-lock-fill text-paideia me-2"></i> Panel de Control (Administrador)
    </h5>

    <ul class="nav nav-underline mb-4 border-bottom" id=\"admin-tabs\" role=\"tablist\">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                <i class="bi bi-people me-2"></i>Usuarios (<span id="count-usuarios">0</span>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                <i class="bi bi-journal-album me-2"></i>Cursos (<span id="count-cursos">0</span>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="admin-tabsContent">
        <div class="tab-pane fade show active" id="usuarios" role="tabpanel" tabindex="0">
            <div class="table-responsive bg-white shadow-sm rounded-3 p-3 border-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small">ID</th>
                            <th class="text-muted small">Nombre y Apellidos</th>
                            <th class="text-muted small">Email</th>
                            <th class="text-muted small">Rol</th>
                            <th class="text-muted small text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="cursos" role="tabpanel" tabindex="0">
            <div class="table-responsive bg-white shadow-sm rounded-3 p-3 border-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small">ID</th>
                            <th class="text-muted small">Título del Curso</th>
                            <th class="text-muted small">Profesor</th>
                            <th class="text-muted small">Estado</th>
                            <th class="text-muted small text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-cursos">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Ficha de Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-editar-usuario">
                    <input type="hidden" id="edit-id-usuario">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Nombre</label>
                            <input type="text" id="edit-nombre" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Apellidos</label>
                            <input type="text" id="edit-apellidos" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Correo Electrónico</label>
                        <input type="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Rol asignado</label>
                        <select id="edit-rol" class="form-select" required>
                            <option value="1">ALUMNO</option>
                            <option value="2">PROFESOR</option>
                            <option value="3">ADMINISTRADOR</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nueva Contraseña (Opcional)</label>
                        <input type="password" id="edit-password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                    </div>
                    <div class="text-end mt-4 pt-2 border-top">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-paideia px-4 fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= RUTA_JS ?>admin_panel.js"></script>

<?php
// require_once __DIR__ . '/../includes/footer.php'; 
?>
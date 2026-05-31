let modalEditarInstance = null;

document.addEventListener('DOMContentLoaded', async function () {
    cargarDashboardAdmin();

    // Escuchar el envío del formulario de edición de usuario
    const formEditar = document.getElementById('form-editar-usuario');
    if (formEditar) {
        formEditar.addEventListener('submit', async function (e) {
            e.preventDefault();
            await procesarGuardarUsuario();
        });
    }
});

async function cargarDashboardAdmin() {
    try {
        const respuesta = await fetch('/api/admin.php?accion=dashboard');
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const usuarios = resultado.data.usuarios;
            const cursos = resultado.data.cursos;

            document.getElementById('count-usuarios').innerText = usuarios.length;
            document.getElementById('count-cursos').innerText = cursos.length;

            // PINTAR USUARIOS (Ahora con botón de editar)
            const tbodyUsuarios = document.getElementById('tabla-usuarios');
            tbodyUsuarios.innerHTML = '';

            if (usuarios.length === 0) {
                tbodyUsuarios.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>';
            } else {
                usuarios.forEach(u => {
                    let badgeColor = u.nombre_rol === 'administrador' ? 'bg-danger' :
                        (u.nombre_rol === 'profesor' ? 'bg-info text-dark' : 'bg-secondary');

                    tbodyUsuarios.innerHTML += `
                        <tr>
                            <td class="text-muted">#${u.id_usuario}</td>
                            <td class="fw-bold text-dark">${u.nombre} ${u.apellidos}</td>
                            <td>${u.email}</td>
                            <td><span class="badge ${badgeColor}">${u.nombre_rol.toUpperCase()}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="abrirModalEditarUsuario(${u.id_usuario})" title="Editar Usuario">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${u.id_usuario})" title="Eliminar Usuario">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            // PINTAR CURSOS 
            const tbodyCursos = document.getElementById('tabla-cursos');
            tbodyCursos.innerHTML = '';

            if (cursos.length === 0) {
                tbodyCursos.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No hay cursos creados.</td></tr>';
            } else {
                cursos.forEach(c => {
                    let badgeEstado = c.estado === 'publicado' ? 'bg-success' : 'bg-warning text-dark';

                    let btnModeracion = '';
                    if (c.estado === 'pendiente') {
                        btnModeracion = `<button class="btn btn-sm btn-success me-1" onclick="cambiarEstadoCurso(${c.id_curso}, 'publicado')" title="Aprobar y Publicar"><i class="bi bi-check-circle"></i> Aprobar</button>`;
                    } else {
                        btnModeracion = `<button class="btn btn-sm btn-warning text-dark me-1" onclick="cambiarEstadoCurso(${c.id_curso}, 'pendiente')" title="Ocultar del catálogo"><i class="bi bi-eye-slash"></i> Ocultar</button>`;
                    }

                    tbodyCursos.innerHTML += `
                        <tr>
                            <td class="text-muted">#${c.id_curso}</td>
                            <td class="fw-bold text-dark">${c.titulo}</td>
                            <td><i class="bi bi-person-video3 me-1"></i> ${c.profesor}</td>
                            <td><span class="badge ${badgeEstado}">${c.estado.toUpperCase()}</span></td>
                            <td class="text-end">
                                ${btnModeracion}
                                <a href="../cursos/detalle.php?id=${c.id_curso}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Ver Curso">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarCurso(${c.id_curso})" title="Eliminar Curso">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
        } else {
            alert("Error al cargar el panel: " + resultado.mensaje);
        }
    } catch (error) {
        console.error("Error cargando dashboard de admin:", error);
    }
}

// =========================================================
// NUEVO: FUNCIONES DE EDICIÓN DE USUARIOS
// =========================================================
async function abrirModalEditarUsuario(id) {
    try {
        const respuesta = await fetch('/api/admin.php?accion=detalle_usuario&id=' + id);
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const u = resultado.data;

            // Precargamos los campos del modal
            document.getElementById('edit-id-usuario').value = u.id_usuario;
            document.getElementById('edit-nombre').value = u.nombre;
            document.getElementById('edit-apellidos').value = u.apellidos;
            document.getElementById('edit-email').value = u.email;
            document.getElementById('edit-rol').value = u.id_rol;
            document.getElementById('edit-password').value = ''; // Limpio por seguridad

            const modalEl = document.getElementById('modalEditarUsuario');
            modalEditarInstance = new bootstrap.Modal(modalEl);
            modalEditarInstance.show();
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        console.error("Error al obtener detalles del usuario:", error);
    }
}

async function procesarGuardarUsuario() {
    const id = document.getElementById('edit-id-usuario').value;
    const nombre = document.getElementById('edit-nombre').value.trim();
    const apellidos = document.getElementById('edit-apellidos').value.trim();
    const email = document.getElementById('edit-email').value.trim();
    const idRol = document.getElementById('edit-rol').value;
    const password = document.getElementById('edit-password').value;

    try {
        const respuesta = await fetch('/api/admin.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'editar_usuario',
                id_usuario: id,
                nombre: nombre,
                apellidos: apellidos,
                email: email,
                id_rol: idRol,
                password: password
            })
        });
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            if (modalEditarInstance) modalEditarInstance.hide();
            alert(resultado.mensaje);
            cargarDashboardAdmin(); // Refrescar la tabla al instante
        } else {
            alert(resultado.mensaje);
        }
    } catch (error) {
        alert("Error de red al intentar actualizar el usuario.");
    }
}

// =========================================================
// FUNCIONES DE MODERACIÓN Y BORRADO 
// =========================================================
async function cambiarEstadoCurso(id, nuevoEstado) {
    document.body.style.cursor = 'wait';
    try {
        const respuesta = await fetch('/api/admin.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'cambiar_estado', id_curso: id, estado: nuevoEstado })
        });
        const resultado = await respuesta.json();
        if (resultado.status === 'success') cargarDashboardAdmin();
        else alert(resultado.mensaje);
    } catch (error) {
        alert("Error de conexión con el servidor.");
    } finally {
        document.body.style.cursor = 'default';
    }
}

async function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción es irreversible y borrará sus datos.')) {
        try {
            const respuesta = await fetch('/api/admin.php?tipo=usuario&id=' + id, { method: 'DELETE' });
            const resultado = await respuesta.json();
            alert(resultado.mensaje);
            if (resultado.status === 'success') cargarDashboardAdmin();
        } catch (error) {
            alert("Hubo un error de conexión.");
        }
    }
}

async function eliminarCurso(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este curso globalmente? Se borrará todo su contenido y matrículas.')) {
        try {
            const respuesta = await fetch('/api/admin.php?tipo=curso&id=' + id, { method: 'DELETE' });
            const resultado = await respuesta.json();
            alert(resultado.mensaje);
            if (resultado.status === 'success') cargarDashboardAdmin();
        } catch (error) {
            alert("Hubo un error de conexión.");
        }
    }
}
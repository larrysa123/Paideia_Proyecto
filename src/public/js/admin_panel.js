document.addEventListener('DOMContentLoaded', async function () {
    cargarDashboardAdmin();
});

async function cargarDashboardAdmin() {
    try {
        const respuesta = await fetch(BASE_URL + 'app/api/admin.php?accion=dashboard');
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const usuarios = resultado.data.usuarios;
            const cursos = resultado.data.cursos;

            document.getElementById('count-usuarios').innerText = usuarios.length;
            document.getElementById('count-cursos').innerText = cursos.length;

            // PINTAR USUARIOS
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

                    tbodyCursos.innerHTML += `
                        <tr>
                            <td class="text-muted">#${c.id_curso}</td>
                            <td class="fw-bold text-dark">${c.titulo}</td>
                            <td><i class="bi bi-person-video3 me-1"></i> ${c.profesor}</td>
                            <td><span class="badge ${badgeEstado}">${c.estado.toUpperCase()}</span></td>
                            <td class="text-end">
                                <a href="../cursos/detalle.php?id=${c.id_curso}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver Curso">
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
// NUEVAS FUNCIONES PARA ELIMINAR
// =========================================================

async function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción es irreversible y borrará sus datos.')) {
        try {
            const respuesta = await fetch(BASE_URL + 'app/api/admin.php?tipo=usuario&id=' + id, {
                method: 'DELETE'
            });
            const resultado = await respuesta.json();

            alert(resultado.mensaje);

            if (resultado.status === 'success') {
                cargarDashboardAdmin(); // Recargamos la tabla para que desaparezca visualmente
            }
        } catch (error) {
            console.error("Error al eliminar usuario:", error);
            alert("Hubo un error de conexión.");
        }
    }
}

async function eliminarCurso(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este curso globalmente? Se borrará todo su contenido y matrículas.')) {
        try {
            const respuesta = await fetch(BASE_URL + 'app/api/admin.php?tipo=curso&id=' + id, {
                method: 'DELETE'
            });
            const resultado = await respuesta.json();

            alert(resultado.mensaje);

            if (resultado.status === 'success') {
                cargarDashboardAdmin(); // Recargamos la tabla
            }
        } catch (error) {
            console.error("Error al eliminar curso:", error);
            alert("Hubo un error de conexión.");
        }
    }
}